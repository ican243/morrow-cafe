<?php

namespace App\Controllers\Admin;

use App\Models\GalleryImageModel;
use App\Models\MediaModel;

class Gallery extends AdminController
{
    private GalleryImageModel $model;

    public function __construct()
    {
        $this->model = model(GalleryImageModel::class);
    }

    public function index()
    {
        return $this->render('gallery/index', [
            'title'  => '매장 사진 갤러리',
            'nav'    => 'gallery',
            'images' => $this->model->withMedia()->orderBy('gallery_images.sort_order')->findAll(),
            'areas'  => config('Cafe')->galleryAreas,
        ]);
    }

    public function create()
    {
        $data = [
            'area'         => (string) $this->request->getPost('area'),
            'caption'      => trim((string) $this->request->getPost('caption')),
            'show_on_home' => $this->flag('show_on_home'),
            'is_visible'   => 1,
        ];
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }

        $mediaId = $this->uploadImage('image', $data['caption'] ?: '매장 사진');
        if ($mediaId === null) {
            return $this->backWithErrors('사진을 선택해 주세요.');
        }
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }

        $this->model->insert($data + ['media_id' => $mediaId, 'sort_order' => $this->model->nextSortOrder()], false);

        return redirect()->to(site_url('admin/gallery'))->with('message', '사진을 추가했습니다.');
    }

    public function update(int $id)
    {
        $old  = $this->findOr404($this->model, $id);
        $data = [
            'area'         => (string) $this->request->getPost('area'),
            'caption'      => trim((string) $this->request->getPost('caption')),
            'show_on_home' => $this->flag('show_on_home'),
            'is_visible'   => $this->flag('is_visible'),
        ];
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }

        // 새 사진을 골랐으면 그 자리 사진만 바꾸고 순서·설명은 그대로 둔다
        $mediaId = $this->uploadImage('image', $data['caption'] ?: '매장 사진');
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }
        if ($mediaId) {
            $data['media_id'] = $mediaId;
        }

        $this->model->update($id, $data);

        if ($mediaId) {
            model(MediaModel::class)->removeIfOrphan((int) $old['media_id']);
        }

        return redirect()->to(site_url('admin/gallery') . '#g-' . $id)->with('message', $mediaId ? '사진을 바꿨습니다.' : '저장했습니다.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($this->model, $id);
        $this->model->delete($id);
        model(MediaModel::class)->removeIfOrphan((int) $row['media_id']);

        return redirect()->to(site_url('admin/gallery'))->with('message', '사진을 삭제했습니다.');
    }

    public function sort()
    {
        return $this->saveOrder($this->model);
    }
}
