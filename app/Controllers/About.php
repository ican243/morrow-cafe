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
            'sections'  => model(PageSectionModel::class)->byKeys(['brand_story', 'coffee_philosophy', 'brand_image', 'space_concept']),
        ]);
    }
}
