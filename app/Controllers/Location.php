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
            'description' => '{매장} 오시는 길 — {주소}. 지도, 길찾기, 영업시간과 휴무 안내.',
            'closures'  => model(StoreClosureModel::class)->upcoming(),
            'kakaoKey'  => (string) env('kakao.jsKey', ''),
        ]);
    }
}
