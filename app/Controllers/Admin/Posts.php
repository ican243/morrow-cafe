<?php

namespace App\Controllers\Admin;

use App\Models\MediaModel;
use App\Models\PostModel;

class Posts extends AdminController
{
    private PostModel $model;

    public function __construct()
    {
        $this->model = model(PostModel::class);
    }

    public function index()
    {
        $type = $this->request->getGet('type');
        if (in_array($type, ['notice', 'event'], true)) {
            $this->model->where('type', $type);
        }

        return $this->render('posts/index', [
            'title' => '공지·이벤트',
            'nav'   => 'posts',
            'posts' => $this->model->orderBy('is_pinned', 'DESC')->orderBy('published_at', 'DESC')->paginate(20),
            'pager' => $this->model->pager,
            'type'  => $type,
            'types' => config('Cafe')->postTypes,
        ]);
    }

    public function new()
    {
        return $this->form(null);
    }

    public function edit(int $id)
    {
        return $this->form($this->model->withMedia()->where('posts.id', $id)->first() ?? $this->findOr404($this->model, $id));
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }
        if ($error = $this->checkEventDates($data)) {
            return $this->backWithErrors($error);
        }

        $mediaId = $this->uploadImage('image', $data['title']);
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }

        $data['media_id']     = $mediaId;
        $data['published_at'] = $this->dt($data['published_at']);
        $id                   = $this->model->skipValidation()->insert($data, true);

        return redirect()->to(site_url("admin/posts/{$id}/edit"))->with('message', '글을 등록했습니다.');
    }

    public function update(int $id)
    {
        $old  = $this->findOr404($this->model, $id);
        $data = $this->input();
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }
        if ($error = $this->checkEventDates($data)) {
            return $this->backWithErrors($error);
        }

        $mediaId = $this->uploadImage('image', $data['title']);
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }
        if ($mediaId) {
            $data['media_id'] = $mediaId;
        } elseif ($this->request->getPost('remove_image')) {
            $data['media_id'] = null;
        }

        $data['published_at'] = $this->dt($data['published_at']);
        $this->model->skipValidation()->update($id, $data);

        if (array_key_exists('media_id', $data) && $old['media_id']) {
            model(MediaModel::class)->removeIfOrphan((int) $old['media_id']);
        }

        return redirect()->to(site_url("admin/posts/{$id}/edit"))->with('message', '저장했습니다.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($this->model, $id);
        $this->model->delete($id);
        model(MediaModel::class)->removeIfOrphan($row['media_id'] ? (int) $row['media_id'] : null);

        return redirect()->to(site_url('admin/posts'))->with('message', "'{$row['title']}' 글을 삭제했습니다.");
    }

    private function form(?array $row)
    {
        return $this->render('posts/form', [
            'title' => $row ? '글 수정' : '글 쓰기',
            'nav'   => 'posts',
            'row'   => $row,
            'types' => config('Cafe')->postTypes,
            'ratio' => config('Cafe')->cropRatios['post'],
        ]);
    }

    private function checkEventDates(array $data): ?string
    {
        if ($data['event_start'] && $data['event_end'] && $data['event_end'] < $data['event_start']) {
            return '이벤트 종료일이 시작일보다 빠릅니다.';
        }

        return null;
    }

    private function input(): array
    {
        $p = fn ($k) => trim((string) $this->request->getPost($k));

        return [
            'type'         => $p('type'),
            'title'        => $p('title'),
            'summary'      => $p('summary'),
            'body'         => $p('body'),
            'is_published' => $this->flag('is_published'),
            'is_pinned'    => $this->flag('is_pinned'),
            'published_at' => $p('published_at'),
            'event_start'  => $p('event_start') ?: null,
            'event_end'    => $p('event_end') ?: null,
        ];
    }
}
