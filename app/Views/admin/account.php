<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title"><div><h1>관리자 비밀번호 변경</h1><p><?= esc($admin['email']) ?> · 마지막 변경 <?= $admin['password_changed_at'] ? ko_date($admin['password_changed_at']) : '없음 (초기 비밀번호 사용 중)' ?></p></div></div>
<form class="card" action="<?= site_url('admin/account/password') ?>" method="post" id="pwForm">
  <?= csrf_field() ?>
  <div class="form-grid one-col">
    <div class="field"><label for="current_password">현재 비밀번호</label><input type="password" id="current_password" name="current_password" autocomplete="current-password" required></div>
    <div class="field"><label for="new_password">새 비밀번호</label><input type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="10" required><span class="hint">10자 이상, 영문과 숫자를 함께 넣어 주세요.</span></div>
    <div class="field"><label for="new_password_confirm">새 비밀번호 확인</label><input type="password" id="new_password_confirm" name="new_password_confirm" autocomplete="new-password" required></div>
  </div>
  <div class="form-foot"><span></span><button class="btn" type="submit">변경</button></div>
</form>
<?= $this->endSection() ?>
