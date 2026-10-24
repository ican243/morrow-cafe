<?php

namespace App\Models;

use App\Libraries\ImageService;
use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table         = 'media';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'path', 'original_name', 'width', 'height', 'file_size', 'alt_text',
        'source', 'source_url', 'author', 'license', 'license_url', 'is_ai_generated', 'is_demo',
    ];

    /** 사진을 가리키는 테이블들 — 삭제 전 사용처 확인에 쓴다 */
    private const USED_BY = [
        'menus'          => ['label' => '메뉴', 'title' => 'name_ko'],
        'banners'        => ['label' => '메인 배너', 'title' => 'title'],
        'gallery_images' => ['label' => '갤러리', 'title' => 'caption'],
        'posts'          => ['label' => '공지·이벤트', 'title' => 'title'],
        'page_sections'  => ['label' => '브랜드 소개', 'title' => 'title'],
    ];

    /**
     * 이 사진을 쓰고 있는 곳 목록. 비어 있어야 삭제할 수 있다.
     *
     * @return list<array{label:string,title:string}>
     */
    public function usages(int $id): array
    {
        $out = [];
        foreach (self::USED_BY as $table => $meta) {
            $rows = $this->db->table($table)->select($meta['title'] . ' AS t')->where('media_id', $id)->get()->getResultArray();
            foreach ($rows as $row) {
                $out[] = ['label' => $meta['label'], 'title' => (string) $row['t']];
            }
        }

        return $out;
    }

    /** 사용 횟수를 한 번의 쿼리로 (목록 화면용) */
    public function usageCounts(): array
    {
        $parts = [];
        foreach (array_keys(self::USED_BY) as $table) {
            $parts[] = "SELECT media_id FROM {$table} WHERE media_id IS NOT NULL";
        }
        $rows = $this->db->query('SELECT media_id, COUNT(*) AS c FROM (' . implode(' UNION ALL ', $parts) . ') u GROUP BY media_id')->getResultArray();

        return array_column($rows, 'c', 'media_id');
    }

    /** 파일과 DB 행을 같이 삭제. 어디선가 쓰는 중이면 false */
    public function remove(int $id): bool
    {
        $media = $this->find($id);
        if (! $media || $this->usages($id) !== []) {
            return false;
        }

        $this->delete($id);
        (new ImageService())->delete($media['path']);

        return true;
    }

    /**
     * 다른 사진으로 교체된 뒤 아무 데서도 안 쓰이게 된 사진 정리.
     * 데모 사진은 다시 쓸 수 있도록 남겨둔다.
     */
    public function removeIfOrphan(?int $id): void
    {
        if (! $id) {
            return;
        }
        $media = $this->find($id);
        if ($media && ! $media['is_demo']) {
            $this->remove($id);
        }
    }
}
