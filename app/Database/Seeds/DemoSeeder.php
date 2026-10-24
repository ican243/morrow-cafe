<?php

namespace App\Database\Seeds;

use App\Libraries\ImageService;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

/**
 * 포트폴리오·화면 확인용 데모 데이터 (가상 매장).
 * 사진은 Database/Seeds/images 의 무료 라이선스 사진을 WebP 로 변환해 넣고 출처를 media 테이블에 기록한다.
 *
 *   php spark db:seed DemoSeeder
 *
 * 실제 메뉴가 하나라도 있으면 실행하지 않는다 (운영 데이터 보호).
 * 납품 전에는 데모 데이터를 지우고 InitialSeeder 만 사용.
 */
class DemoSeeder extends Seeder
{
    private array $media = [];

    public function run()
    {
        if ($this->db->table('menus')->countAllResults() > 0) {
            CLI::error('menus 테이블에 데이터가 있어 데모 데이터를 넣지 않습니다. (운영 데이터 보호)');

            return;
        }

        $this->call(InitialSeeder::class);
        $this->db->transStart();

        $this->seedMedia();
        $this->seedSettings();
        $this->seedHours();
        $this->seedSections();
        $this->seedMenus();
        $this->seedBanners();
        $this->seedGallery();
        $this->seedPosts();

        $this->db->transComplete();
        cache()->delete('cafe_settings');
        CLI::write('데모 데이터를 넣었습니다. 사진 ' . count($this->media) . '장', 'green');
    }

    private function seedMedia(): void
    {
        $dir     = __DIR__ . '/images/';
        $credits = json_decode((string) file_get_contents($dir . 'credits.json'), true);
        $images  = new ImageService();
        $now     = date('Y-m-d H:i:s');

        foreach ($credits as $c) {
            $data = $images->store($dir . $c['file'], $c['file']);
            $this->db->table('media')->insert($data + [
                'alt_text'        => $c['alt'],
                'source'          => $c['source'],
                'source_url'      => $c['source_url'],
                'author'          => $c['author'],
                'license'         => $c['license'],
                'license_url'     => $c['license_url'],
                'is_ai_generated' => $c['is_ai_generated'] ? 1 : 0,
                'is_demo'         => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            $this->media[pathinfo($c['file'], PATHINFO_FILENAME)] = $this->db->insertID();
        }
    }

    private function m(string $name): int
    {
        return $this->media[$name];
    }

    private function seedSettings(): void
    {
        $values = [
            'store_name'        => 'MORROW COFFEE',
            'store_name_en'     => 'MORROW COFFEE',
            'tagline'           => '천천히 내린 커피와 오래 머물고 싶은 자리, 모로우 커피',
            // 가상 매장: 실제 존재하지 않는 번지·번호를 쓴다 (실제 가게·개인에게 연결되지 않도록)
            'address'           => '서울특별시 성동구 성수이로 000 (예시 주소)',
            'address_detail'    => '1층',
            'address_note'      => "성수역 3번 출구에서 걸어서 7분 거리입니다.\n건물 앞 큰 은행나무가 보이면 다 오셨어요.",
            'phone'             => '02-000-0000',
            'map_lat'           => '37.5446',
            'map_lng'           => '127.0559',
            'regular_holiday'   => '매주 월요일 정기 휴무',
            'hours_note'        => '공휴일은 주말과 같은 시간에 엽니다.',
            'seats'             => "1층 28석 (창가 바 좌석 8석, 4인 테이블 3개, 8인 원목 테이블 1개)\n노트북 작업은 평일 오후에 가장 여유롭습니다.",
            'parking'           => "전용 주차장은 없습니다.\n근처 성수동 공영주차장(도보 3분)을 이용해 주세요.",
            'wifi'              => "무료 와이파이를 제공합니다.\n비밀번호는 영수증 하단에 있습니다.",
            'pets'              => "소형견은 이동장 또는 안고 계실 때 동반 가능합니다.\n다른 손님을 위해 테이블·의자 위에는 올려두지 말아 주세요.",
            'facilities'        => '창가·벽면 좌석에 콘센트가 있습니다. 화장실은 매장 안 남녀 분리.',
            'menu_notice'       => "모든 음료는 디카페인 원두로 바꿀 수 있습니다 (+500원).\n우유는 국내산 1등급 원유를 사용하며, 오트 밀크로 바꿀 수 있습니다 (+500원).\n알레르기가 있으신 분은 주문 전 직원에게 꼭 말씀해 주세요. 같은 공간에서 우유·밀·달걀·견과류를 다룹니다.",
            'instagram_url'     => 'https://www.instagram.com/',
            'kakao_channel_url' => 'https://pf.kakao.com/',
            'naver_place_url'   => '',
            'kakao_place_url'   => '',
        ];
        model(\App\Models\StoreSettingModel::class)->saveMany($values);
    }

    private function seedHours(): void
    {
        $rows = [];
        foreach (range(0, 6) as $d) {
            $weekend = in_array($d, [0, 6], true);
            $rows[]  = [
                'day_of_week' => $d,
                'is_closed'   => $d === 1 ? 1 : 0,
                'open_time'   => $d === 1 ? null : ($weekend ? '10:00:00' : '08:30:00'),
                'close_time'  => $d === 1 ? null : ($weekend ? '21:00:00' : '20:00:00'),
                'last_order'  => $d === 1 ? null : ($weekend ? '20:30:00' : '19:30:00'),
                'break_start' => null,
                'break_end'   => null,
                'updated_at'  => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('business_hours')->upsertBatch($rows);

        $this->db->table('store_closures')->insertBatch([
            ['closed_on' => date('Y-m-d', strtotime('+18 days')), 'reason' => '원두 입고·매장 정비', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    private function seedSections(): void
    {
        $now  = date('Y-m-d H:i:s');
        $rows = [
            ['home_intro', 'Slow mornings, honest coffee', '하루를 조금 천천히 시작하는 곳',
                "모로우는 '내일'을 뜻하는 morrow에서 이름을 가져왔습니다. 오늘 마신 커피 한 잔이 내일을 조금 더 기대하게 만들었으면 하는 마음입니다.\n\n큰 창으로 들어오는 오전 햇살, 손때 묻은 원목 테이블, 서두르지 않고 내리는 커피. 모로우에서는 잠깐 쉬어가도 괜찮습니다.", null],
            ['brand_story', 'Our Story', '동네에 오래 남는 작은 커피집',
                "로스터리에서 7년 동안 원두를 볶던 두 사람이 성수동 골목의 오래된 인쇄소 자리를 고쳐 문을 열었습니다.\n\n유행보다 매일 마셔도 질리지 않는 커피를 만들고 싶었습니다. 그래서 메뉴는 많지 않습니다. 대신 하나하나 오래 고민해서 내놓습니다.", 'about-pour-02'],
            ['coffee_philosophy', 'Beans & Brewing', '좋은 원두를, 알맞게 볶아, 정직하게',
                "생두는 산지와 수확 연도가 분명한 것만 들여옵니다. 지금은 에티오피아 예가체프와 콜롬비아 우일라를 중심으로 블렌딩합니다.\n\n로스팅은 매주 화요일, 소량씩. 볶은 지 3일에서 14일 사이의 원두만 에스프레소로 씁니다. 핸드드립은 그날 손님의 취향을 여쭤보고 원두를 추천해 드립니다.", 'about-roast-03'],
            ['brand_image', 'Warm & Quiet', '따뜻한 색, 조용한 소리',
                "베이지와 브라운, 그리고 원목. 모로우의 모든 것은 햇살이 가장 예쁘게 드는 오후 세 시의 색에서 시작했습니다.\n\n음악은 작게, 조명은 낮게. 대화가 편안하고 혼자여도 어색하지 않은 온도를 지키려 합니다.", 'hero-interior-02'],
            ['space_concept', 'The Space', '창가에서, 긴 테이블에서, 각자의 속도로',
                "오래된 인쇄소의 높은 천장과 큰 창을 그대로 살렸습니다. 혼자 오신 분을 위한 창가 바 좌석, 여럿이 둘러앉는 8인 원목 테이블, 조용히 책 읽기 좋은 안쪽 자리가 있습니다.\n\n가구는 모두 동네 목공방과 함께 만들었습니다.", 'space-inside-05'],
        ];
        foreach ($rows as [$key, $eyebrow, $title, $body, $img]) {
            $this->db->table('page_sections')->insert([
                'section_key' => $key, 'eyebrow' => $eyebrow, 'title' => $title, 'body' => $body,
                'media_id'    => $img ? $this->m($img) : null, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    private function seedMenus(): void
    {
        $now  = date('Y-m-d H:i:s');
        $cats = [
            ['커피', 'Coffee'],
            ['시그니처', 'Signature'],
            ['논커피 · 티', 'Non-Coffee & Tea'],
            ['디저트', 'Dessert'],
        ];
        $catIds = [];
        foreach ($cats as $i => [$ko, $en]) {
            $this->db->table('menu_categories')->insert(['name_ko' => $ko, 'name_en' => $en, 'sort_order' => $i + 1, 'is_visible' => 1, 'created_at' => $now, 'updated_at' => $now]);
            $catIds[$en] = $this->db->insertID();
        }

        // [카테고리, 한글, 영문, 가격, 설명, 온도, 사진, SIG, BEST, 품절, 알레르기, 원재료]
        $menus = [
            ['Coffee', '아메리카노', 'Americano', 4500, '고소한 견과류와 다크 초콜릿 향의 하우스 블렌드', 'both', 'menu-drink-01', 0, 1, 0, '', '에스프레소(에티오피아·콜롬비아 블렌드)'],
            ['Coffee', '아이스 아메리카노', 'Iced Americano', 4500, '얼음이 녹아도 맛이 흐려지지 않도록 진하게 추출했습니다', 'ice', 'menu-drink-02', 0, 0, 0, '', '에스프레소(에티오피아·콜롬비아 블렌드)'],
            ['Coffee', '카페 라테', 'Caffè Latte', 5000, '부드러운 우유와 에스프레소의 균형', 'both', 'menu-drink-03', 0, 1, 0, 'milk', '에스프레소, 우유(국산)'],
            ['Coffee', '카푸치노', 'Cappuccino', 5000, '풍성한 우유 거품을 올린 클래식 카푸치노', 'hot', 'menu-drink-04', 0, 0, 0, 'milk', '에스프레소, 우유(국산)'],
            ['Coffee', '오늘의 핸드드립', 'Hand Drip of the Day', 6500, '그날 가장 맛있는 싱글 오리진을 한 잔씩 내립니다', 'both', 'menu-drink-05', 0, 0, 0, '', '싱글 오리진 원두 (매장 문의)'],
            ['Coffee', '바닐라 빈 라테', 'Vanilla Bean Latte', 5800, '마다가스카르 바닐라 빈으로 직접 만든 시럽', 'both', 'menu-drink-06', 0, 0, 1, 'milk', '에스프레소, 우유(국산), 바닐라 빈 시럽(자체 제조)'],

            ['Signature', '모로우 크림 라테', 'Morrow Cream Latte', 6500, '꿀을 더한 크림을 얹은 모로우의 대표 라테', 'both', 'menu-signature-01', 1, 1, 0, 'milk', '에스프레소, 우유(국산), 생크림(국산), 꿀'],
            ['Signature', '오후 세 시 아인슈페너', '3 PM Einspänner', 6800, '진한 콜드브루 위에 쌉싸름한 코코아 크림', 'ice', 'menu-signature-02', 1, 0, 0, 'milk', '콜드브루, 생크림(국산), 코코아 파우더'],

            ['Non-Coffee & Tea', '말차 라테', 'Matcha Latte', 6000, '제주 유기농 말차를 진하게 풀었습니다', 'both', 'menu-drink-08', 0, 1, 0, 'milk', '말차(제주산), 우유(국산), 비정제 원당'],
            ['Non-Coffee & Tea', '캐모마일 블렌드 티', 'Chamomile Blend Tea', 5500, '캐모마일과 레몬밤을 섞은 편안한 허브티', 'hot', 'menu-drink-07', 0, 0, 0, '', '캐모마일, 레몬밤, 레몬그라스'],

            ['Dessert', '딸기 생크림 케이크', 'Strawberry Shortcake', 7500, '매일 아침 굽는 시트에 제철 딸기를 듬뿍', 'none', 'menu-signature-03', 1, 0, 0, 'milk,egg,wheat', '밀가루(미국산), 생크림(국산), 딸기(국산), 달걀'],
            ['Dessert', '바스크 치즈케이크', 'Basque Cheesecake', 7000, '겉은 진하게 그을리고 속은 촉촉하게', 'none', 'menu-dessert-02', 0, 1, 0, 'milk,egg,wheat', '크림치즈(호주산), 생크림, 달걀, 밀가루'],
            ['Dessert', '버터 스콘', 'Butter Scone', 4000, '프랑스산 버터로 구운 담백한 스콘', 'none', 'menu-dessert-01', 0, 0, 0, 'milk,egg,wheat', '밀가루, 버터(프랑스산), 우유, 달걀'],
            ['Dessert', '크루아상', 'Croissant', 4500, '결이 살아 있는 버터 크루아상', 'none', 'menu-dessert-03', 0, 0, 0, 'milk,egg,wheat', '밀가루, 버터(프랑스산), 달걀'],
            ['Dessert', '초코칩 쿠키', 'Chocolate Chip Cookie', 3500, '겉은 바삭, 속은 쫀득한 수제 쿠키', 'none', 'menu-dessert-04', 0, 0, 0, 'milk,egg,wheat,soy,walnut', '밀가루, 버터, 다크초콜릿(대두 레시틴), 호두'],
            ['Dessert', '마들렌', 'Madeleine', 3000, '레몬 제스트를 더한 촉촉한 마들렌', 'none', 'menu-dessert-05', 0, 0, 0, 'milk,egg,wheat', '밀가루, 버터, 달걀, 레몬'],
        ];

        $order = [];
        foreach ($menus as [$cat, $ko, $en, $price, $desc, $temp, $img, $sig, $best, $out, $alg, $ing]) {
            $order[$cat] = ($order[$cat] ?? 0) + 1;
            $this->db->table('menus')->insert([
                'category_id' => $catIds[$cat], 'name_ko' => $ko, 'name_en' => $en, 'price' => $price,
                'description' => $desc, 'temperature' => $temp, 'media_id' => $this->m($img),
                'is_signature' => $sig, 'is_best' => $best, 'is_sold_out' => $out, 'is_visible' => 1,
                'sort_order' => $order[$cat], 'allergens' => $alg, 'ingredients' => $ing,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    private function seedBanners(): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('banners')->insertBatch([
            ['media_id' => $this->m('hero-interior-01'), 'eyebrow' => 'Slow coffee, warm light', 'title' => '햇살 좋은 날, 천천히 내린 커피 한 잔', 'subtitle' => '성수동 골목의 작은 커피집, 모로우입니다.', 'link_label' => '메뉴 보기', 'link_url' => '/menu', 'is_active' => 1, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['media_id' => $this->m('hero-interior-02'), 'eyebrow' => 'Stay a little longer', 'title' => '오래 머물러도 괜찮은 자리', 'subtitle' => '창가 바 좌석부터 여럿이 둘러앉는 원목 테이블까지.', 'link_label' => '공간 둘러보기', 'link_url' => '/space', 'is_active' => 1, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedGallery(): void
    {
        $now  = date('Y-m-d H:i:s');
        $rows = [
            ['space-inside-03', 'inside', '8명이 둘러앉는 원목 테이블', 1],
            ['space-inside-02', 'seat', '햇살이 드는 창가 바 좌석', 1],
            ['space-inside-04', 'inside', '커피를 내리는 바 카운터', 1],
            ['space-inside-05', 'inside', '식물과 오후 햇살', 1],
            ['space-inside-06', 'seat', '조용한 안쪽 창가 자리', 1],
            ['space-inside-01', 'seat', '창밖 초록이 보이는 2인 테이블', 0],
            ['hero-interior-01', 'inside', '원목 카운터와 큰 창', 0],
            ['space-exterior-01', 'outside', '정원이 있는 입구', 0],
        ];
        foreach ($rows as $i => [$img, $area, $caption, $home]) {
            $this->db->table('gallery_images')->insert([
                'media_id' => $this->m($img), 'area' => $area, 'caption' => $caption, 'show_on_home' => $home,
                'is_visible' => 1, 'sort_order' => $i + 1, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    private function seedPosts(): void
    {
        $at   = static fn (string $rel) => date('Y-m-d H:i:s', strtotime($rel));
        $rows = [
            ['event', '가을 한정 메이플 시나몬 라테 출시', '11월 한 달 동안만 만나는 계절 음료입니다.', "선선한 바람이 부는 계절, 메이플 시럽과 시나몬을 더한 라테를 준비했습니다.\n\n기간: 11월 1일 ~ 11월 30일\n가격: 6,500원 (HOT / ICE)\n\n준비한 수량이 소진되면 일찍 마감될 수 있습니다.", 'news-thumb-01', 1, '-2 days', date('Y-m-01', strtotime('+1 month')), date('Y-m-t', strtotime('+1 month'))],
            ['notice', '원두 입고·매장 정비로 하루 쉬어갑니다', '새 원두 입고와 로스터 점검을 위해 휴무합니다.', "새로 들어오는 생두를 정리하고 로스터를 점검하기 위해 하루 문을 닫습니다.\n\n휴무일은 LOCATION 페이지와 매장 앞 안내문으로도 확인하실 수 있습니다. 불편을 드려 죄송합니다.", 'news-thumb-04', 0, '-6 days', null, null],
            ['notice', '원두 소분 판매를 시작합니다', '매장에서 쓰는 하우스 블렌드를 200g 단위로 판매합니다.', "집에서도 모로우의 커피를 즐기실 수 있도록 원두 소분 판매를 시작합니다.\n\n하우스 블렌드 200g 15,000원\n싱글 오리진 200g 17,000원부터\n\n분쇄를 원하시면 추출 도구를 말씀해 주세요. 맞춰 갈아 드립니다.", 'news-thumb-02', 0, '-15 days', null, null],
            ['event', '오픈 1주년, 감사의 마음을 담아', '1주년 기념 음료 1+1 쿠폰을 드립니다.', "모로우가 문을 연 지 1년이 되었습니다. 찾아주신 모든 분께 감사드립니다.\n\n1주년 주간 동안 음료를 주문하시면 다음 방문 때 쓸 수 있는 1+1 쿠폰을 드립니다.", 'news-thumb-03', 0, '-40 days', date('Y-m-d', strtotime('-40 days')), date('Y-m-d', strtotime('-34 days'))],
        ];
        foreach ($rows as [$type, $title, $summary, $body, $img, $pin, $when, $s, $e]) {
            $this->db->table('posts')->insert([
                'type' => $type, 'title' => $title, 'summary' => $summary, 'body' => $body,
                'media_id' => $this->m($img), 'is_published' => 1, 'is_pinned' => $pin,
                'published_at' => $at($when), 'event_start' => $s, 'event_end' => $e,
                'created_at' => $at($when), 'updated_at' => $at($when),
            ]);
        }
    }
}
