<?php

namespace App\Models;

class BannerModel extends SortableModel
{
    protected $table         = 'banners';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['media_id', 'eyebrow', 'title', 'subtitle', 'link_label', 'link_url', 'is_active', 'starts_at', 'ends_at', 'sort_order'];

    protected $validationRules = [
        'title'      => 'required|max_length[100]',
        'eyebrow'    => 'permit_empty|max_length[60]',
        'subtitle'   => 'permit_empty|max_length[200]',
        'link_label' => 'permit_empty|max_length[30]',
        'link_url'   => 'permit_empty|max_length[300]',
        'starts_at'  => 'permit_empty|valid_date',
        'ends_at'    => 'permit_empty|valid_date',
    ];
    protected $validationMessages = [
        'title' => ['required' => '제목을 입력해 주세요.'],
    ];

    public function withMedia(): static
    {
        $this->select('banners.*, media.path AS media_path, media.alt_text AS media_alt, media.width AS media_w, media.height AS media_h')
            ->join('media', 'media.id = banners.media_id');

        return $this;
    }

    /** 지금 노출 중인 배너 (기간이 비어 있으면 항상 노출) */
    public function current(): array
    {
        $now = date('Y-m-d H:i:s');

        return $this->withMedia()
            ->where('banners.is_active', 1)
            ->groupStart()->where('starts_at', null)->orWhere('starts_at <=', $now)->groupEnd()
            ->groupStart()->where('ends_at', null)->orWhere('ends_at >=', $now)->groupEnd()
            ->orderBy('banners.sort_order')
            ->findAll(5);
    }
}
