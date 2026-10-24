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
            'address'        => ['label' => '주소', 'type' => 'text', 'required' => true],
            'address_detail' => ['label' => '상세 주소', 'type' => 'text'],
            'address_note'   => ['label' => '찾아오는 길 안내', 'type' => 'textarea'],
            'phone'          => ['label' => '전화번호', 'type' => 'tel'],
            'map_lat'        => ['label' => '지도 위도 (예: 37.5446)', 'type' => 'number'],
            'map_lng'        => ['label' => '지도 경도 (예: 127.0559)', 'type' => 'number'],
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
        'sns' => [
            'instagram_url'  => ['label' => '인스타그램 주소', 'type' => 'url'],
            'kakao_channel_url' => ['label' => '카카오톡 채널 주소', 'type' => 'url'],
            'naver_place_url'   => ['label' => '네이버 지도(플레이스) 주소', 'type' => 'url'],
            'kakao_place_url'   => ['label' => '카카오맵 장소 주소', 'type' => 'url'],
        ],
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
