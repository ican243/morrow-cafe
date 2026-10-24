<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>관리자 로그인 | MORROW COFFEE</title>
<link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/variable/pretendardvariable-dynamic-subset.min.css">
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css?v=' . filemtime(FCPATH . 'assets/css/admin.css')) ?>">
</head>
<body class="login-page">
<form class="login-box" action="<?= site_url('admin/login') ?>" method="post">
  <?= csrf_field() ?>
  <h1>MORROW</h1>
  <p>관리자 로그인</p>
  <?= $this->include('admin/_flash') ?>
  <div class="field">
    <label for="email">이메일</label>
    <input type="email" id="email" name="email" value="<?= esc(old('email'), 'attr') ?>" autocomplete="username" required autofocus>
  </div>
  <div class="field">
    <label for="password">비밀번호</label>
    <input type="password" id="password" name="password" autocomplete="current-password" required>
  </div>
  <button class="btn" type="submit">로그인</button>
</form>
</body>
</html>
