<?php

namespace App\Controllers\Admin;

use App\Models\BannerModel;
use App\Models\MediaModel;

class Banners extends AdminController
{
    private BannerModel $model;
    private ?string $linkError = null;

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
        if (! $this->model->validate($data) || $this->linkError) {
            return $this->backWithErrors(array_filter([...array_values($this->model->errors()), $this->linkError]));
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
        if (! $this->model->validate($data) || $this->linkError) {
            return $this->backWithErrors(array_filter([...array_values($this->model->errors()), $this->linkError]));
        }

        $mediaId = $this->uploadImage('image', $data['title']);
        if (is_string($mediaId)) {
            return $this->backWithErrors($mediaId);
        }
        if ($mediaId) {
            $data['media_id'] = $mediaId;
        }

        $this->model->update($id, $data);
        if (! array_key_exists('media_id', $data)) {
            $this->saveCurrentAlt($old['media_id']);
        }
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
            ...$this->link($p('link_target'), $p('link_url'), $p('link_label')),
            'is_active'  => $this->flag('is_active'),
            'starts_at'  => $this->dt($p('starts_at')),
            'ends_at'    => $this->dt($p('ends_at')),
        ];
    }

    /**
     * 버튼 연결: 목록에서 고른 화면, 또는 '직접 입력'한 주소.
     * 직접 입력은 /news/3 같은 사이트 안 주소나 https:// 주소만 받는다 (그 외는 버튼이 조용히 사라지므로 미리 알림)
     */
    private function link(string $target, string $url, string $label): array
    {
        $pages = config('Cafe')->linkPages;

        if ($target === '') {
            return ['link_label' => '', 'link_url' => ''];
        }
        if (isset($pages[$target])) {
            return ['link_label' => $label ?: $pages[$target][1], 'link_url' => $target];
        }

        if ($url !== '' && ! str_starts_with($url, '/') && ! preg_match('#^https?://#i', $url)) {
            $url = preg_match('#^[a-z0-9-]+(\.[a-z0-9-]+)+([/?\#]\S*)?$#i', $url) ? 'https://' . $url : '';
            if ($url === '') {
                $this->linkError = '버튼 연결 주소: 인터넷 주소 형식이 아닙니다. 홈페이지 화면으로 연결하려면 목록에서 골라 주세요.';
            }
        }
        if ($url === '' && ! $this->linkError) {
            $this->linkError = '버튼 연결 주소를 입력해 주세요.';
        }
        if ($label === '') {
            $this->linkError ??= '버튼 글자를 입력해 주세요.';
        }

        return ['link_label' => $label, 'link_url' => $url];
    }
}
