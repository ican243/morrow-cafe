<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table         = 'posts';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['type', 'title', 'summary', 'body', 'media_id', 'is_published', 'is_pinned', 'published_at', 'event_start', 'event_end'];

    protected $validationRules = [
        'type'         => 'required|in_list[notice,event]',
        'title'        => 'required|max_length[150]',
        'summary'      => 'permit_empty|max_length[300]',
        'body'         => 'required|max_length[20000]',
        'published_at' => 'required|valid_date[Y-m-d\TH:i]',
        'event_start'  => 'permit_empty|valid_date[Y-m-d]',
        'event_end'    => 'permit_empty|valid_date[Y-m-d]',
    ];
    protected $validationMessages = [
        'title'        => ['required' => '제목을 입력해 주세요.'],
        'body'         => ['required' => '내용을 입력해 주세요.'],
        'published_at' => ['required' => '게시일을 입력해 주세요.', 'valid_date' => '게시일 형식이 올바르지 않습니다.'],
    ];

    public function withMedia(): static
    {
        $this->select('posts.*, media.path AS media_path, media.alt_text AS media_alt, media.width AS media_w, media.height AS media_h')
            ->join('media', 'media.id = posts.media_id', 'left');

        return $this;
    }

    /** 공개 글 조건: 게시 상태 + 게시일이 지난 글 (예약 게시) */
    public function published(): static
    {
        $this->where('posts.is_published', 1)->where('posts.published_at <=', date('Y-m-d H:i:s'));

        return $this;
    }

    public function findPublic(int $id): ?array
    {
        return $this->withMedia()->published()->where('posts.id', $id)->first();
    }

    /** 상세 화면의 이전/다음 글 */
    public function neighbors(array $post): array
    {
        $prev = $this->published()->select('id, title')
            ->where('published_at <', $post['published_at'])
            ->orderBy('published_at', 'DESC')->first();
        $next = $this->published()->select('id, title')
            ->where('published_at >', $post['published_at'])
            ->orderBy('published_at', 'ASC')->first();

        return [$prev, $next];
    }
}
