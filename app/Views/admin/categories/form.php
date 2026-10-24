<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title"><h1><?= esc($title) ?></h1></div>
<form class="card" action="<?= site_url($row ? "admin/categories/{$row['id']}" : 'admin/categories') ?>" method="post">
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="field">
      <label for="name_ko">한글 이름<span class="req">*</span></label>
      <input type="text" id="name_ko" name="name_ko" maxlength="50" required value="<?= esc(old('name_ko', $row['name_ko'] ?? ''), 'attr') ?>" placeholder="예: 커피">
    </div>
    <div class="field">
      <label for="name_en">영문 이름<span class="req">*</span></label>
      <input type="text" id="name_en" name="name_en" maxlength="50" required value="<?= esc(old('name_en', $row['name_en'] ?? ''), 'attr') ?>" placeholder="예: Coffee">
      <span class="hint">MENU 페이지에서 큰 제목으로 보입니다.</span>
    </div>
    <div class="field full">
      <label class="check"><input type="checkbox" name="is_visible" value="1"<?= old('is_visible', $row['is_visible'] ?? 1) ? ' checked' : '' ?>> 홈페이지에 표시</label>
    </div>
  </div>
  <div class="form-foot">
    <a class="btn btn--line" href="<?= site_url('admin/categories') ?>">목록</a>
    <button class="btn" type="submit">저장</button>
  </div>
</form>
<?= $this->endSection() ?>
