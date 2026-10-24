<?php

namespace App\Controllers;

use App\Models\StoreClosureModel;

class Location extends SiteController
{
    public function index(): string
    {
        return $this->render('location', [
            'title'     => 'Location',
            'navActive' => 'location',
            'closures'  => model(StoreClosureModel::class)->upcoming(),
            'kakaoKey'  => (string) env('kakao.jsKey', ''),
        ]);
    }
}
