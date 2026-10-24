<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$v = static fn ($k, $d = '') => old($k, $row[$k] ?? $d);
$published = old('published_at', ! empty($row['published_at']) ? date('Y-m-d\TH:i', strtotime($row['published_at'])) : date('Y-m-d\TH:i'));
?>
<div class="page-title">
  <h1><?= esc($title) ?></h1>
  <?php if ($row && $row['is_published']): ?><a class="btn btn--line" href="<?= site_url('news/' . $row['id']) ?>" target="_blank" rel="noopener">홈페이지에서 보기 ↗</a><?php endif ?>
</div>
<form class="card" action="<?= site_url($row ? "admin/posts/{$row['id']}" : 'admin/posts') ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="field">
      <label for="type">분류<span class="req">*</span></label>
      <select id="type" name="type">
        <?php foreach ($types as $k => $label): ?><option value="<?= $k ?>"<?= $v('type', 'notice') === $k ? ' selected' : '' ?>><?= $label ?></option><?php endforeach ?>
      </select>
    </div>
    <div class="field">
      <label for="published_at">게시일<span class="req">*</span></label>
      <input type="datetime-local" id="published_at" name="published_at" required value="<?= esc($published, 'attr') ?>">
      <span class="hint">미래 시간으로 정하면 그때 공개됩니다.</span>
    </div>
    <div class="field full">
      <label for="title">제목<span class="req">*</span></label>
      <input type="text" id="title" name="title" maxlength="150" required value="<?= esc($v('title'), 'attr') ?>">
    </div>
    <div class="field full">
      <label for="summary">요약</label>
      <input type="text" id="summary" name="summary" maxlength="300" value="<?= esc($v('summary'), 'attr') ?>" placeholder="목록에 두 줄까지 보입니다">
    </div>
    <div class="field full">
      <label for="body">내용<span class="req">*</span></label>
      <textarea class="tall" id="body" name="body" required><?= esc($v('body')) ?></textarea>
      <span class="hint">빈 줄을 한 번 넣으면 문단이 나뉩니다.</span>
    </div>
    <div class="field">
      <label for="event_start">이벤트 시작일</label>
      <input type="date" id="event_start" name="event_start" value="<?= esc($v('event_start'), 'attr') ?>">
    </div>
    <div class="field">
      <label for="event_end">이벤트 종료일</label>
      <input type="date" id="event_end" name="event_end" value="<?= esc($v('event_end'), 'attr') ?>">
      <span class="hint">분류가 '이벤트'일 때만 화면에 표시됩니다.</span>
    </div>
    <?= $this->setData(['ratio' => $ratio, 'removable' => true, 'label' => '대표 사진'])->include('admin/_image_field') ?>
    <div class="field full">
      <div class="checks">
        <label class="check"><input type="checkbox" name="is_published" value="1"<?= $v('is_published', 1) ? ' checked' : '' ?>> 공개</label>
        <label class="check"><input type="checkbox" name="is_pinned" value="1"<?= $v('is_pinned', 0) ? ' checked' : '' ?>> 목록 맨 위에 고정</label>
      </div>
    </div>
  </div>
  <div class="form-foot">
    <a class="btn btn--line" href="<?= site_url('admin/posts') ?>">목록</a>
    <button class="btn" type="submit">저장</button>
  </div>
</form>
<?= $this->endSection() ?>
