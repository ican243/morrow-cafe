<?php

namespace App\Controllers;

use App\Models\PageSectionModel;

class About extends SiteController
{
    public function index(): string
    {
        return $this->render('about', [
            'title'     => 'About',
            'navActive' => 'about',
            'description' => '{매장}의 이야기 — 원두와 커피 철학, 오래 머물고 싶은 공간을 소개합니다.',
            'sections'  => model(PageSectionModel::class)->byKeys(['brand_story', 'coffee_philosophy', 'brand_image', 'space_concept']),
        ]);
    }
}
