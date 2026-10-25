<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ---- 고객용 ----
$routes->get('/', 'Home::index');
$routes->get('about', 'About::index');
$routes->get('menu', 'Menu::index');
$routes->get('space', 'Space::index');
$routes->get('news', 'News::index');
$routes->get('news/(:num)', 'News::show/$1');
$routes->get('location', 'Location::index');

// ---- 관리자 (/admin) ----
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('login', 'Auth::loginForm');
    $routes->post('login', 'Auth::login');

    // 여기서부터는 로그인한 관리자만
    $routes->group('', ['filter' => 'adminAuth'], static function ($routes) {
        $routes->post('logout', 'Auth::logout');
        $routes->get('/', 'Dashboard::index');

        // 목록·등록·수정·삭제·순서변경이 같은 모양인 화면들
        foreach (['categories' => 'Categories', 'menus' => 'Menus', 'banners' => 'Banners', 'posts' => 'Posts'] as $path => $controller) {
            $routes->get($path, "{$controller}::index");
            $routes->get("{$path}/new", "{$controller}::new");
            $routes->post($path, "{$controller}::create");
            $routes->get("{$path}/(:num)/edit", "{$controller}::edit/$1");
            $routes->post("{$path}/(:num)", "{$controller}::update/$1");
            $routes->post("{$path}/(:num)/delete", "{$controller}::delete/$1");
            if ($path !== 'posts') {
                $routes->post("{$path}/sort", "{$controller}::sort");
                $routes->post("{$path}/(:num)/move/(up|down)", "{$controller}::move/$1/$2");
            }
        }
        $routes->post('menus/(:num)/quick', 'Menus::quick/$1');

        $routes->get('sections', 'Sections::index');
        $routes->get('sections/(:segment)/edit', 'Sections::edit/$1');
        $routes->post('sections/(:segment)', 'Sections::update/$1');

        $routes->get('gallery', 'Gallery::index');
        $routes->post('gallery', 'Gallery::create');
        $routes->post('gallery/(:num)', 'Gallery::update/$1');
        $routes->post('gallery/(:num)/delete', 'Gallery::delete/$1');
        $routes->post('gallery/sort', 'Gallery::sort');
        $routes->post('gallery/(:num)/move/(up|down)', 'Gallery::move/$1/$2');

        $routes->get('store', 'Store::info');
        $routes->post('store', 'Store::saveInfo');
        $routes->get('hours', 'Store::hours');
        $routes->post('hours', 'Store::saveHours');
        $routes->post('closures', 'Store::addClosure');
        $routes->post('closures/(:num)/delete', 'Store::deleteClosure/$1');
        $routes->get('sns', 'Store::sns');
        $routes->post('sns', 'Store::saveSns');

        $routes->get('media', 'Media::index');
        $routes->post('media/(:num)', 'Media::update/$1');
        $routes->post('media/(:num)/delete', 'Media::delete/$1');

        $routes->get('account', 'Account::index');
        $routes->post('account/password', 'Account::password');
    });
});
