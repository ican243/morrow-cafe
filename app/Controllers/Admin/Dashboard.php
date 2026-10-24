<?php

namespace App\Controllers\Admin;

use App\Models\AdminModel;
use App\Models\BannerModel;
use App\Models\BusinessHourModel;
use App\Models\GalleryImageModel;
use App\Models\MediaModel;
use App\Models\MenuModel;
use App\Models\PostModel;
use App\Models\StoreClosureModel;
use App\Models\StoreSettingModel;

class Dashboard extends AdminController
{
    public function index()
    {
        $menus    = model(MenuModel::class);
        $settings = model(StoreSettingModel::class)->all();
        $admin    = model(AdminModel::class)->find(session('admin_id'));

        // 손봐야 할 것들을 모아서 보여준다
        $todo = [];
        if (! $admin['password_changed_at']) {
            $todo[] = ['초기 비밀번호를 아직 바꾸지 않았습니다.', 'admin/account'];
        }
        if (! env('kakao.jsKey')) {
            $todo[] = ['카카오 지도 키가 없어 LOCATION 페이지에 지도가 표시되지 않습니다. (서버 .env 의 kakao.jsKey)', null];
        }
        foreach (['address' => '주소', 'phone' => '전화번호'] as $key => $label) {
            if (empty($settings[$key])) {
                $todo[] = ["매장 {$label}가 비어 있습니다.", 'admin/store'];
            }
        }
        $noAlt = model(MediaModel::class)->where('alt_text', '')->countAllResults();
        if ($noAlt) {
            $todo[] = ["사진 설명(대체 텍스트)이 비어 있는 사진이 {$noAlt}장 있습니다.", 'admin/media'];
        }
        if (! model(BannerModel::class)->current()) {
            $todo[] = ['지금 노출 중인 메인 배너가 없습니다.', 'admin/banners'];
        }

        $week  = model(BusinessHourModel::class)->week();
        $today = $week[(int) date('w')];

        return $this->render('dashboard', [
            'title'    => '대시보드',
            'nav'      => 'dashboard',
            'stats'    => [
                ['메뉴', $menus->countAllResults(), 'admin/menus'],
                ['품절', $menus->where('is_sold_out', 1)->countAllResults(), 'admin/menus?soldout=1'],
                ['공지·이벤트', model(PostModel::class)->countAllResults(), 'admin/posts'],
                ['갤러리 사진', model(GalleryImageModel::class)->countAllResults(), 'admin/gallery'],
            ],
            'soldOut'  => $menus->select('id, name_ko')->where('is_sold_out', 1)->orderBy('name_ko')->findAll(),
            'todo'     => $todo,
            'today'    => $today,
            'closures' => model(StoreClosureModel::class)->upcoming(3),
            'posts'    => model(PostModel::class)->orderBy('published_at', 'DESC')->findAll(5),
        ]);
    }
}
