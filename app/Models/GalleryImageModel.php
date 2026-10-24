<?php

namespace App\Models;

class GalleryImageModel extends SortableModel
{
    protected $table         = 'gallery_images';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['media_id', 'area', 'caption', 'show_on_home', 'is_visible', 'sort_order'];

    protected $validationRules = [
        'area'    => 'required|in_list[inside,seat,outside]',
        'caption' => 'permit_empty|max_length[100]',
    ];

    public function withMedia(): static
    {
        $this->select('gallery_images.*, media.path AS media_path, media.alt_text AS media_alt, media.width AS media_w, media.height AS media_h')
            ->join('media', 'media.id = gallery_images.media_id');

        return $this;
    }

    public function visible(?bool $homeOnly = false, ?int $limit = null): array
    {
        $this->withMedia()->where('gallery_images.is_visible', 1);
        if ($homeOnly) {
            $this->where('gallery_images.show_on_home', 1);
        }

        return $this->orderBy('gallery_images.sort_order')->findAll($limit ?? 0);
    }
}
