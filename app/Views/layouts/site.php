<?php
/**
 * 고객용 화면 공통 틀
 * @var array       $s         매장 설정 (store_settings)
 * @var string|null $title
 * @var string      $navActive
 */
$storeName = $s['store_name'] ?? 'MORROW COFFEE';
$pageTitle = $title ? $title . ' | ' . $storeName : $storeName . ' — ' . ($s['tagline'] ?? '');
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($pageTitle) ?></title>
<meta name="description" content="<?= esc($description ?? '', 'attr') ?>">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#F5F0E8">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= esc($s['store_name'] ?? '', 'attr') ?>">
<meta property="og:locale" content="ko_KR">
<meta property="og:url" content="<?= esc(current_url(), 'attr') ?>">
<meta property="og:title" content="<?= esc($pageTitle, 'attr') ?>">
<meta property="og:description" content="<?= esc($description ?? '', 'attr') ?>">
<?php if (! empty($ogImage)): ?><meta property="og:image" content="<?= esc($ogImage, 'attr') ?>"><?php endif ?>
<link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,400;1,500&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/site.css?v=' . filemtime(FCPATH . 'assets/css/site.css')) ?>">
</head>
<body class="<?= $navActive === 'home' ? 'is-home' : '' ?>">
<a class="skip" href="#main">본문 바로가기</a>

<?= $this->include('partials/header') ?>

<main id="main">
<?= $this->renderSection('content') ?>
</main>

<?= $this->include('partials/footer') ?>

<?= $this->renderSection('scripts') ?>
<script src="<?= base_url('assets/js/site.js?v=' . filemtime(FCPATH . 'assets/js/site.js')) ?>" defer></script>
</body>
</html>
