<?php

namespace App\Controllers;

use App\Models\GalleryImageModel;

class Space extends SiteController
{
    public function index(): string
    {
        return $this->render('space', [
            'title'     => 'Space',
            'navActive' => 'space',
            'description' => '{매장} 매장 둘러보기 — 매장 사진과 좌석·주차·와이파이 이용 안내.',
            'images'    => model(GalleryImageModel::class)->visible(),
            'areas'     => config(\Config\Cafe::class)->galleryAreas,
        ]);
    }
}
