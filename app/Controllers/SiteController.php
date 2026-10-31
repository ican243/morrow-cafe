<?php

namespace App\Controllers;

use App\Models\BannerModel;
use App\Models\BusinessHourModel;
use App\Models\StoreSettingModel;

/**
 * 고객용 화면 공통. 머리말·바닥글에 필요한 매장 정보를 모든 화면에 같이 넘긴다.
 */
abstract class SiteController extends BaseController
{
    protected function render(string $view, array $data = []): string
    {
        $settings = model(StoreSettingModel::class)->all();

        $data += [
            'title'       => null,
            'description' => $settings['tagline'] ?? '',
            'navActive'   => '',
        ];
        // 화면별 설명 문구의 {매장}·{주소} 자리에 관리자에서 넣은 값을 채운다
        $data['description'] = strtr($data['description'], [
            '{매장}' => $settings['store_name'] ?? '',
            '{주소}' => trim(($settings['address'] ?? '') . ' ' . ($settings['address_detail'] ?? '')),
        ]);
        $data['s']     = $settings;
        $data['week']  ??= model(BusinessHourModel::class)->week();
        $data['today'] = (int) date('w');

        // 카톡·인스타 등에 링크를 보낼 때 미리보기 사진: 따로 정한 게 없으면 지금 걸린 메인 배너 첫 장
        if (empty($data['ogImage'])) {
            $banner          = model(BannerModel::class)->current()[0] ?? null;
            $data['ogImage'] = $banner ? media_url($banner['media_path'], 'lg') : '';
        }

        return view('site/' . $view, $data);
    }
}
