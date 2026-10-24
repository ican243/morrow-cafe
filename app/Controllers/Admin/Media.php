<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;

/**
 * 사진 보관함: 올라간 사진 전체, 출처·라이선스 기록, 안 쓰는 사진 정리
 */
class Media extends AdminController
{
    public function index()
    {
        $model  = model(MediaModel::class);
        $filter = $this->request->getGet('filter');
        $counts = $model->usageCounts();

        $items = $model->orderBy('id', 'DESC')->findAll();
        if ($filter === 'unused') {
            $items = array_values(array_filter($items, static fn ($m) => empty($counts[$m['id']])));
        } elseif ($filter === 'noalt') {
            $items = array_values(array_filter($items, static fn ($m) => $m['alt_text'] === ''));
        }

        $bytes = array_sum(array_column($model->select('file_size')->findAll(), 'file_size'));

        return $this->render('media/index', [
            'title'  => '사진 보관함',
            'nav'    => 'media',
            'items'  => $items,
            'counts' => $counts,
            'filter' => $filter,
            'totalMb' => round($bytes / 1048576, 1),
        ]);
    }

    public function update(int $id)
    {
        $model = model(MediaModel::class);
        $this->findOr404($model, $id);

        $p    = fn ($k, $len) => mb_substr(trim((string) $this->request->getPost($k)), 0, $len);
        $data = [
            'alt_text'        => $p('alt_text', 200),
            'source'          => $p('source', 50),
            'source_url'      => $p('source_url', 500),
            'author'          => $p('author', 100),
            'license'         => $p('license', 100),
            'is_ai_generated' => $this->flag('is_ai_generated'),
        ];
        if ($data['source_url'] !== '' && ! preg_match('#^https?://#i', $data['source_url'])) {
            return $this->backWithErrors('출처 주소는 http:// 또는 https:// 로 시작해야 합니다.');
        }
        $model->update($id, $data);

        return redirect()->to(previous_url() . '#m-' . $id)->with('message', '사진 정보를 저장했습니다.');
    }

    public function delete(int $id)
    {
        $model  = model(MediaModel::class);
        $this->findOr404($model, $id);
        $usages = $model->usages($id);

        if ($usages) {
            $where = implode(', ', array_map(static fn ($u) => $u['label'] . '「' . $u['title'] . '」', $usages));

            return redirect()->back()->with('error', "이 사진은 {$where}에서 쓰고 있어 삭제할 수 없습니다. 먼저 그곳의 사진을 바꿔 주세요.");
        }

        $model->remove($id);

        return redirect()->back()->with('message', '사진을 삭제했습니다.');
    }
}
