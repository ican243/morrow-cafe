<?php
// 없는 주소로 들어왔을 때 화면 (고객·관리자 공용). 개발 모드에서만 원인 메시지를 보여준다.
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>페이지를 찾을 수 없습니다 | MORROW COFFEE</title>
<link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;1,400&display=swap">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/site.css') ?>">
</head>
<body>
<main class="wrap" style="min-height:100vh;display:grid;place-content:center;text-align:center">
  <p class="eyebrow">Page not found</p>
  <h1 class="display" style="font-size:clamp(4rem,12vw,7rem)">404</h1>
  <p style="margin:1rem 0 2rem;color:var(--brown)">찾으시는 페이지가 없거나 주소가 바뀌었습니다.</p>
  <?php if (ENVIRONMENT !== 'production' && ! empty($message) && $message !== '(null)'): ?>
  <p class="muted"><?= nl2br(esc($message)) ?></p>
  <?php endif ?>
  <p><a class="btn" href="<?= base_url('/') ?>">홈으로 가기</a></p>
</main>
</body>
</html>
