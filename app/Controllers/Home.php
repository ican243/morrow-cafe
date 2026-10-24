<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\GalleryImageModel;
use App\Models\MenuModel;
use App\Models\PageSectionModel;
use App\Models\PostModel;

class Home extends SiteController
{
    public function index(): string
    {
        return $this->render('home', [
            'navActive' => 'home',
            'banners'   => model(BannerModel::class)->current(),
            'sections'  => model(PageSectionModel::class)->byKeys(['home_intro', 'brand_story']),
            'signature' => model(MenuModel::class)->signatures(3),
            'space'     => model(GalleryImageModel::class)->visible(true, 5),
            'latest'    => model(PostModel::class)->published()->orderBy('published_at', 'DESC')->findAll(1)[0] ?? null,
        ]);
    }
}
