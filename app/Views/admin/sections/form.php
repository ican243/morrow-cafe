<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $v = static fn ($k) => old($k, $row[$k] ?? ''); ?>
<div class="page-title"><h1><?= esc($title) ?></h1></div>
<form class="card" action="<?= site_url("admin/sections/{$key}") ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="field">
      <label for="eyebrow">작은 영문 제목</label>
      <input type="text" id="eyebrow" name="eyebrow" maxlength="60" value="<?= esc($v('eyebrow'), 'attr') ?>" placeholder="예: Our Story">
    </div>
    <div class="field">
      <label for="title">제목<span class="req">*</span></label>
      <input type="text" id="title" name="title" maxlength="150" required value="<?= esc($v('title'), 'attr') ?>">
    </div>
    <div class="field full">
      <label for="body">내용<span class="req">*</span></label>
      <textarea class="tall" id="body" name="body" required><?= esc($v('body')) ?></textarea>
      <span class="hint">빈 줄을 한 번 넣으면 문단이 나뉩니다.</span>
    </div>
    <?php if ($def['ratio']): ?>
    <?= $this->setData(['ratio' => $def['ratio'], 'label' => '사진'])->include('admin/_image_field') ?>
    <?php endif ?>
  </div>
  <div class="form-foot">
    <a class="btn btn--line" href="<?= site_url('admin/sections') ?>">목록</a>
    <button class="btn" type="submit">저장</button>
  </div>
</form>
<?= $this->endSection() ?>
