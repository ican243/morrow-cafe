<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * MORROW COFFEE 사이트 전용 설정.
 * 화면 여러 곳에서 같이 쓰는 목록(알레르기 항목, 매장 정보 칸, 사진 크기)을 한 곳에 모아둔다.
 */
class Cafe extends BaseConfig
{
    /**
     * 알레르기 유발 성분 (식품 등의 표시기준 22종 중 카페 메뉴에서 쓰일 만한 것)
     * DB에는 key만 저장한다.
     */
    public array $allergens = [
        'milk'    => '우유',
        'egg'     => '알류(난류)',
        'wheat'   => '밀',
        'soy'     => '대두',
        'peanut'  => '땅콩',
        'walnut'  => '호두',
        'pinenut' => '잣',
        'peach'   => '복숭아',
        'tomato'  => '토마토',
        'sulfite' => '아황산류',
    ];

    public array $temperatures = [
        'none' => '-',
        'hot'  => 'HOT',
        'ice'  => 'ICE',
        'both' => 'HOT / ICE',
    ];

    public array $galleryAreas = [
        'inside'  => '매장 내부',
        'seat'    => '좌석',
        'outside' => '외부',
    ];

    public array $postTypes = [
        'notice' => '공지',
        'event'  => '이벤트',
    ];

    /**
     * store_settings 에 저장되는 항목. group 별로 관리자 화면 탭이 나뉜다.
     * type: text | textarea | url | tel | number
     */
    public array $settingFields = [
        'store' => [
            'store_name'     => ['label' => '매장 이름', 'type' => 'text', 'required' => true],
            'store_name_en'  => ['label' => '영문 이름', 'type' => 'text'],
            'tagline'        => ['label' => '한 줄 소개 (푸터·검색 설명)', 'type' => 'text'],
            'address'        => ['label' => '주소', 'type' => 'text', 'required' => true, 'search' => true],
            'address_detail' => ['label' => '상세 주소', 'type' => 'text'],
            'address_note'   => ['label' => '찾아오는 길 안내', 'type' => 'textarea'],
            'phone'          => ['label' => '전화번호', 'type' => 'tel'],
            'map_lat'        => ['label' => '지도 위치 · 위도', 'type' => 'number', 'placeholder' => '주소 검색으로 고르면 자동 입력'],
            'map_lng'        => ['label' => '지도 위치 · 경도', 'type' => 'number', 'placeholder' => '주소 검색으로 고르면 자동 입력'],
            'regular_holiday'=> ['label' => '정기 휴무 안내 문구', 'type' => 'text'],
            'hours_note'     => ['label' => '영업시간 추가 안내', 'type' => 'text'],
        ],
        'facility' => [
            'seats'          => ['label' => '좌석 안내', 'type' => 'textarea'],
            'parking'        => ['label' => '주차', 'type' => 'textarea'],
            'wifi'           => ['label' => '와이파이', 'type' => 'textarea'],
            'pets'           => ['label' => '반려동물', 'type' => 'textarea'],
            'facilities'     => ['label' => '기타 시설 (콘센트, 화장실 등)', 'type' => 'textarea'],
            'menu_notice'    => ['label' => '메뉴 페이지 하단 안내 (원산지 등)', 'type' => 'textarea'],
        ],
        // help: 주소 찾는 법 / hosts: 이 칸에 넣을 수 있는 사이트 (다른 칸 주소를 잘못 넣는 것 방지)
        'sns' => [
            'instagram_url' => [
                'label' => '인스타그램', 'type' => 'url', 'hosts' => ['instagram.com'],
                'placeholder' => '예: morrow.coffee',
                'help' => '계정 이름만 적어도 됩니다. 인스타 앱 → 내 프로필 맨 위에 보이는 이름이 계정 이름입니다.',
            ],
            'kakao_channel_url' => [
                'label' => '카카오톡 채널', 'type' => 'url', 'hosts' => ['pf.kakao.com', 'open.kakao.com'],
                'placeholder' => '채널 URL을 복사해서 붙여넣기',
                'help' => '카카오톡 채널 관리자센터(center-pf.kakao.com)에 로그인 → 내 채널 선택 → 왼쪽 메뉴 \'채널 홍보\' → 채널 URL \'복사\' 후 여기에 붙여넣기.',
            ],
            'naver_place_url' => [
                'label' => '네이버 지도', 'type' => 'url', 'hosts' => ['naver.me', 'map.naver.com', 'place.naver.com'], 'optional' => true,
                'placeholder' => '공유 → 링크 복사한 내용을 그대로 붙여넣기',
                'help' => '네이버 지도 앱에서 매장 검색 → 매장 화면의 \'공유\' → \'링크 복사\' → 그대로 붙여넣기. 안내 글이 같이 복사돼도 주소만 골라 저장합니다.',
            ],
            'kakao_place_url' => [
                'label' => '카카오맵', 'type' => 'url', 'hosts' => ['map.kakao.com', 'kko.kakao.com', 'kko.to'], 'optional' => true,
                'placeholder' => '공유 → 링크 복사한 내용을 그대로 붙여넣기',
                'help' => '카카오맵 앱에서 매장 검색 → 매장 화면의 \'공유\' → \'링크 복사\' → 그대로 붙여넣기. 안내 글이 같이 복사돼도 주소만 골라 저장합니다.',
            ],
        ],
    ];

    /** 배너 버튼이 연결할 수 있는 홈페이지 화면. 값: [고르는 목록에 보일 이름, 버튼 글자를 비웠을 때 기본 글자] */
    public array $linkPages = [
        '/menu'     => ['MENU · 메뉴', '메뉴 보기'],
        '/about'    => ['ABOUT · 카페 소개', '카페 소개 보기'],
        '/space'    => ['SPACE · 매장 공간', '공간 둘러보기'],
        '/news'     => ['NEWS · 공지·이벤트', '소식 보기'],
        '/location' => ['LOCATION · 오시는 길', '오시는 길 보기'],
    ];

    /**
     * page_sections 의 칸. 관리자는 내용만 고치고 칸을 추가·삭제하지 않는다.
     * ratio: 사진 크롭 비율 (가로/세로)
     */
    public array $sections = [
        'home_intro'        => ['label' => 'HOME · 카페 소개', 'ratio' => 0],
        'brand_story'       => ['label' => '브랜드 스토리 (HOME·ABOUT)', 'ratio' => 0.8],
        'coffee_philosophy' => ['label' => 'ABOUT · 원두와 커피 철학', 'ratio' => 0.8],
        'brand_image'       => ['label' => 'ABOUT · 브랜드 이미지', 'ratio' => 1.5],
        'space_concept'     => ['label' => 'ABOUT · 공간 콘셉트', 'ratio' => 0.8],
    ];

    // ---- 사진 업로드 ----

    /** 업로드 최대 용량 (KB). nginx client_max_body_size / php upload_max_filesize 보다 작아야 한다 */
    public int $uploadMaxKb = 10240;

    public array $uploadMimes = ['image/jpeg', 'image/png', 'image/webp'];

    /** 한 변 최대 픽셀 (너무 큰 이미지는 서버 메모리를 다 써버려서 거절) */
    public int $uploadMaxDimension = 8000;

    /** 저장할 크기들. 긴 변 기준이 아니라 "가로" 최대값 */
    public array $imageSizes = [
        'lg' => 1600,
        'md' => 900,
        'sm' => 480,
    ];

    public int $webpQuality = 80;

    /** 용도별 크롭 비율 (가로/세로, 0 = 자유) */
    public array $cropRatios = [
        'menu'    => 1,
        'banner'  => 16 / 9,
        'post'    => 16 / 9,
        'gallery' => 0,
    ];
}
