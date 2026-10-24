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
            'images'    => model(GalleryImageModel::class)->visible(),
            'areas'     => config(\Config\Cafe::class)->galleryAreas,
        ]);
    }
}
