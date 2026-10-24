<?php

/**
 * 화면 공통 함수 (사진 출력, 가격, 날짜)
 * BaseController 에서 자동으로 불러온다.
 */

if (! function_exists('media_url')) {
    function media_url(?string $path, string $size = 'md'): string
    {
        return $path ? base_url('uploads/' . $path . '_' . $size . '.webp') : '';
    }
}

if (! function_exists('media_img')) {
    /**
     * 반응형 <img> 태그. 화면 크기에 맞는 파일을 브라우저가 골라서 받는다.
     *
     * @param array|null $row   media_path, media_alt, media_w, media_h 를 가진 행 (join 결과)
     * @param string     $sizes <img sizes> 값
     * @param array      $attr  class, loading 등 추가 속성
     */
    function media_img(?array $row, string $sizes = '100vw', array $attr = []): string
    {
        if (empty($row['media_path'])) {
            return '<div class="img-empty" aria-hidden="true"></div>';
        }

        $path   = $row['media_path'];
        $config = config(\Config\Cafe::class);
        $srcset = [];
        foreach ($config->imageSizes as $suffix => $w) {
            $srcset[] = media_url($path, $suffix) . ' ' . min($w, (int) $row['media_w']) . 'w';
        }

        $attr += ['loading' => 'lazy', 'decoding' => 'async'];
        $extra = '';
        foreach ($attr as $k => $v) {
            $extra .= ' ' . $k . '="' . esc($v, 'attr') . '"';
        }

        return sprintf(
            '<img src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s"%s>',
            media_url($path, 'md'),
            esc(implode(', ', array_unique($srcset)), 'attr'),
            esc($sizes, 'attr'),
            (int) $row['media_w'],
            (int) $row['media_h'],
            esc($row['media_alt'] ?? '', 'attr'),
            $extra
        );
    }
}

if (! function_exists('won')) {
    function won(int|string|null $price): string
    {
        return number_format((int) $price);
    }
}

if (! function_exists('hm')) {
    /** '09:00:00' → '09:00' */
    function hm(?string $time): string
    {
        return $time ? substr($time, 0, 5) : '';
    }
}

if (! function_exists('ko_date')) {
    function ko_date(?string $datetime): string
    {
        return $datetime ? date('Y.m.d', strtotime($datetime)) : '';
    }
}

if (! function_exists('paragraphs')) {
    /** 관리자가 입력한 일반 텍스트를 문단(<p>)으로. HTML 은 모두 이스케이프 */
    function paragraphs(?string $text): string
    {
        $blocks = preg_split('/\R{2,}/', trim((string) $text)) ?: [];
        $html   = '';
        foreach ($blocks as $block) {
            if ($block !== '') {
                $html .= '<p>' . nl2br(esc($block), false) . '</p>';
            }
        }

        return $html;
    }
}

if (! function_exists('tel_href')) {
    function tel_href(?string $phone): string
    {
        return 'tel:' . preg_replace('/[^0-9+]/', '', (string) $phone);
    }
}

if (! function_exists('safe_url')) {
    /** http(s) 주소만 링크로 허용 (javascript: 같은 주소가 저장돼도 실행되지 않게) */
    function safe_url(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return site_url(ltrim($url, '/'));
        }

        return preg_match('#^https?://#i', $url) ? $url : '';
    }
}

if (! function_exists('icon')) {
    /**
     * 선 아이콘 (24x24). 브랜드 로고 대신 일반 아이콘을 쓴다 (상표 이미지 사용 문제 회피).
     */
    function icon(string $name): string
    {
        $paths = [
            'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
            'pin'       => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
            'route'     => '<circle cx="6" cy="19" r="2"/><circle cx="18" cy="5" r="2"/><path d="M8 19h8.5a3.5 3.5 0 0 0 0-7h-9a3.5 3.5 0 0 1 0-7H16"/>',
            'camera'    => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/>',
            'chat'      => '<path d="M12 4C7 4 3 7.1 3 11c0 2.4 1.5 4.5 3.9 5.8L6 20l3.8-2.2c.7.1 1.4.2 2.2.2 5 0 9-3.1 9-7s-4-7-9-7z"/>',
            'close'     => '<path d="M6 6l12 12M18 6L6 18"/>',
            'prev'      => '<path d="M15 5l-7 7 7 7"/>',
            'next'      => '<path d="M9 5l7 7-7 7"/>',
            'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        ];

        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
            . ($paths[$name] ?? '') . '</svg>';
    }
}

if (! function_exists('map_links')) {
    /**
     * 네이버·카카오 길찾기 주소.
     * 관리자가 장소 페이지 주소를 넣어두면 그걸 쓰고, 없으면 주소·좌표로 만든다.
     */
    function map_links(array $s): array
    {
        $name    = $s['store_name'] ?? 'MORROW COFFEE';
        $address = trim(($s['address'] ?? '') . ' ' . ($s['address_detail'] ?? ''));
        $lat     = $s['map_lat'] ?? '';
        $lng     = $s['map_lng'] ?? '';
        $hasPos  = is_numeric($lat) && is_numeric($lng);

        $naver = safe_url($s['naver_place_url'] ?? '')
            ?: 'https://map.naver.com/p/search/' . rawurlencode($address ?: $name);
        $kakao = $hasPos
            ? 'https://map.kakao.com/link/to/' . rawurlencode($name) . ',' . $lat . ',' . $lng
            : 'https://map.kakao.com/link/search/' . rawurlencode($address ?: $name);

        return [
            'naver'      => $naver,
            'kakao'      => $kakao,
            'kakaoPlace' => safe_url($s['kakao_place_url'] ?? ''),
        ];
    }
}

if (! function_exists('today_status')) {
    /** HOME 소개 옆 "오늘 영업" 표시용 */
    function today_status(array $week, array $closures = []): array
    {
        $d   = (int) date('w');
        $row = $week[$d] ?? null;
        if (! $row || $row['is_closed'] || in_array(date('Y-m-d'), $closures, true)) {
            return ['open' => false, 'text' => '오늘은 쉬어갑니다'];
        }

        return ['open' => true, 'text' => '오늘 ' . hm($row['open_time']) . ' – ' . hm($row['close_time']) . ' 영업'];
    }
}
