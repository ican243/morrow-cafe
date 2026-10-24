<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$v  = static fn ($k, $d = '') => old($k, $row[$k] ?? $d);
$dt = static fn ($k) => old($k, ! empty($row[$k]) ? date('Y-m-d\TH:i', strtotime($row[$k])) : '');
?>
<div class="page-title"><h1><?= esc($title) ?></h1></div>
<form class="card" action="<?= site_url($row ? "admin/banners/{$row['id']}" : 'admin/banners') ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="form-grid">
    <?= $this->setData(['ratio' => $ratio, 'required' => true, 'label' => '배너 사진 (가로로 넓은 사진 권장, 1600px 이상)'])->include('admin/_image_field') ?>
    <div class="field">
      <label for="eyebrow">작은 영문 문구</label>
      <input type="text" id="eyebrow" name="eyebrow" maxlength="60" value="<?= esc($v('eyebrow'), 'attr') ?>" placeholder="예: Slow coffee, warm light">
    </div>
    <div class="field">
      <label for="title">제목<span class="req">*</span></label>
      <input type="text" id="title" name="title" maxlength="100" required value="<?= esc($v('title'), 'attr') ?>">
    </div>
    <div class="field full">
      <label for="subtitle">설명</label>
      <input type="text" id="subtitle" name="subtitle" maxlength="200" value="<?= esc($v('subtitle'), 'attr') ?>">
    </div>
    <div class="field">
      <label for="link_label">버튼 글자</label>
      <input type="text" id="link_label" name="link_label" maxlength="30" value="<?= esc($v('link_label'), 'attr') ?>" placeholder="예: 메뉴 보기">
      <span class="hint">비워두면 버튼이 나오지 않습니다.</span>
    </div>
    <div class="field">
      <label for="link_url">버튼 연결 주소</label>
      <input type="text" id="link_url" name="link_url" maxlength="300" value="<?= esc($v('link_url'), 'attr') ?>" placeholder="/menu 또는 https://...">
    </div>
    <div class="field">
      <label for="starts_at">노출 시작</label>
      <input type="datetime-local" id="starts_at" name="starts_at" value="<?= esc($dt('starts_at'), 'attr') ?>">
    </div>
    <div class="field">
      <label for="ends_at">노출 종료</label>
      <input type="datetime-local" id="ends_at" name="ends_at" value="<?= esc($dt('ends_at'), 'attr') ?>">
      <span class="hint">기간을 비워두면 항상 노출됩니다. 시즌 배너에 쓰세요.</span>
    </div>
    <div class="field full"><label class="check"><input type="checkbox" name="is_active" value="1"<?= $v('is_active', 1) ? ' checked' : '' ?>> 사용</label></div>
  </div>
  <div class="form-foot">
    <a class="btn btn--line" href="<?= site_url('admin/banners') ?>">목록</a>
    <button class="btn" type="submit">저장</button>
  </div>
</form>
<?= $this->endSection() ?>
