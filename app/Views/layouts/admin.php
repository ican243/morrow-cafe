<?php
$menu = [
    '운영' => [
        'dashboard' => ['admin', '대시보드'],
        'menus'     => ['admin/menus', '메뉴'],
        'categories'=> ['admin/categories', '메뉴 카테고리'],
        'posts'     => ['admin/posts', '공지·이벤트'],
    ],
    '화면 꾸미기' => [
        'banners'  => ['admin/banners', '메인 배너'],
        'sections' => ['admin/sections', '브랜드 소개 글'],
        'gallery'  => ['admin/gallery', '매장 사진 갤러리'],
        'media'    => ['admin/media', '사진 보관함'],
    ],
    '매장 정보' => [
        'store' => ['admin/store', '주소·연락처·이용안내'],
        'hours' => ['admin/hours', '영업시간·휴무일'],
        'sns'   => ['admin/sns', 'SNS·외부 링크'],
    ],
];
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<?= csrf_meta() ?>
<title><?= esc($title) ?> | MORROW 관리자</title>
<link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css">
<link rel="stylesheet" href="<?= base_url('assets/vendor/cropper.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=' . filemtime(FCPATH . 'assets/css/admin.css')) ?>">
</head>
<body>
<div class="topbar">
  <b>MORROW</b>
  <button type="button" id="sideOpen" aria-label="관리 메뉴 열기">☰</button>
</div>

<aside class="side">
  <a class="side-brand" href="<?= site_url('admin') ?>"><b>MORROW</b><span>관리자</span></a>
  <nav aria-label="관리 메뉴">
    <?php foreach ($menu as $group => $items): ?>
    <h2><?= $group ?></h2>
    <?php foreach ($items as $key => [$path, $label]): ?>
    <a href="<?= site_url($path) ?>"<?= $nav === $key ? ' aria-current="page"' : '' ?>><?= $label ?></a>
    <?php endforeach ?>
    <?php endforeach ?>
  </nav>
  <div class="side-foot">
    <a href="<?= site_url('/') ?>" target="_blank" rel="noopener">홈페이지 보기 ↗</a>
    <a href="<?= site_url('admin/account') ?>"<?= $nav === 'account' ? ' aria-current="page"' : '' ?>>비밀번호 변경</a>
    <form action="<?= site_url('admin/logout') ?>" method="post">
      <?= csrf_field() ?>
      <button type="submit"><?= esc($adminName ?? '') ?> 로그아웃</button>
    </form>
  </div>
</aside>

<main class="main">
  <?= $this->include('admin/_flash') ?>
  <?= $this->renderSection('content') ?>
</main>

<dialog class="crop-dialog" id="cropDialog" aria-labelledby="cropTitle">
  <header>
    <strong id="cropTitle">사진 자르기</strong>
    <button class="btn btn--line btn--sm" type="button" data-crop="rotate">90° 회전</button>
  </header>
  <div class="crop-stage"><img src="" alt="자를 사진"></div>
  <footer>
    <button class="btn btn--line" type="button" data-crop="cancel">취소</button>
    <div class="actions">
      <button class="btn btn--line" type="button" data-crop="original">자르지 않고 사용</button>
      <button class="btn" type="button" data-crop="apply">이 영역 사용</button>
    </div>
  </footer>
</dialog>

<script src="<?= base_url('assets/vendor/cropper.min.js') ?>" defer></script>
<script src="<?= base_url('assets/vendor/sortable.min.js') ?>" defer></script>
<script src="<?= base_url('assets/js/admin.js?v=' . filemtime(FCPATH . 'assets/js/admin.js')) ?>" defer></script>
</body>
</html>
