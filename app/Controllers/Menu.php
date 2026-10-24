<?php

namespace App\Controllers;

use App\Models\MenuCategoryModel;
use App\Models\MenuModel;

class Menu extends SiteController
{
    public function index(): string
    {
        $categories = model(MenuCategoryModel::class)->ordered(true);
        $menus      = model(MenuModel::class)->publicByCategory();

        // 메뉴가 하나도 없는 카테고리는 탭에서도 뺀다
        $categories = array_values(array_filter($categories, static fn ($c) => ! empty($menus[$c['id']])));

        return $this->render('menu', [
            'title'      => 'Menu',
            'navActive'  => 'menu',
            'categories' => $categories,
            'menus'      => $menus,
            'allergens'  => config(\Config\Cafe::class)->allergens,
            'temps'      => config(\Config\Cafe::class)->temperatures,
        ]);
    }
}
