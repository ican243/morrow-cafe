<?php

namespace App\Controllers;

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
        $data['s']     = $settings;
        $data['week']  ??= model(BusinessHourModel::class)->week();
        $data['today'] = (int) date('w');

        return view('site/' . $view, $data);
    }
}
