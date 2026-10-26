<?php

namespace App\Controllers\Admin;

use App\Models\BannerModel;
use App\Models\MediaModel;

class Banners extends AdminController
{
    private BannerModel $model;

    public function __construct()
    {
        $this->model = model(BannerModel::class);
    }

    public function index()
    {
        return $this->render('banners/index', [
            'title'   => '메인 배너',
            'nav'     => 'banners',
            'banners' => $this->model->withMedia()->orderBy('banners.sort_order')->findAll(),
            'current' => array_column($this->model->current(), 'id'),
        ]);
    }

    public function new()
    {
        return $this->render('banners/form', ['title' => '배너 추가', 'nav' => 'banners', 'row' => null, 'ratio' => config('Cafe')->cropRatios['banner']]);
    }

    public function edit(int $id)
    {
        $row = $this->model->withMedia()->where('banners.id', $id)->first() ?? $this->findOr404($this->model, $id);

        return $this->render('banners/form', ['title' => '배너 수정', 'nav' => 'banners', 'row' => $row, 'ratio' => config('Cafe')->cropRatios['banner']]);
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }

        $mediaId = $this->uploadImage('image', $data['title']);
        if ($mediaId === null) {
            return $this->backWithErrors('배너 사진을 선택해 주세요.');
        }
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }

        $this->model->insert($data + ['media_id' => $mediaId, 'sort_order' => $this->model->nextSortOrder()], false);

        return redirect()->to(site_url('admin/banners'))->with('message', '배너를 추가했습니다.');
    }

    public function update(int $id)
    {
        $old  = $this->findOr404($this->model, $id);
        $data = $this->input();
        if (! $this->model->validate($data)) {
            return $this->backWithErrors($this->model->errors());
        }

        $mediaId = $this->uploadImage('image', $data['title']);
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

        return redirect()->to(site_url('admin/banners'))->with('message', '배너를 저장했습니다.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($this->model, $id);
        $this->model->delete($id);
        model(MediaModel::class)->removeIfOrphan((int) $row['media_id']);

        return redirect()->to(site_url('admin/banners'))->with('message', '배너를 삭제했습니다.');
    }

    public function sort()
    {
        return $this->saveOrder($this->model);
    }


    private function input(): array
    {
        $p = fn ($k) => trim((string) $this->request->getPost($k));

        return [
            'eyebrow'    => $p('eyebrow'),
            'title'      => $p('title'),
            'subtitle'   => $p('subtitle'),
            'link_label' => $p('link_label'),
            'link_url'   => $p('link_url'),
            'is_active'  => $this->flag('is_active'),
            'starts_at'  => $this->dt($p('starts_at')),
            'ends_at'    => $this->dt($p('ends_at')),
        ];
    }
}
