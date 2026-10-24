<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$v = static fn ($k, $d = '') => old($k, $row[$k] ?? $d);
$checkedAllergens = old('allergens', array_filter(explode(',', $row['allergens'] ?? '')));
$catSelected = (int) $v('category_id', $preset);
?>
<div class="page-title"><h1><?= esc($title) ?></h1></div>
<form class="card" action="<?= site_url($row ? "admin/menus/{$row['id']}" : 'admin/menus') ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="form-grid">
    <div class="field">
      <label for="category_id">카테고리<span class="req">*</span></label>
      <select id="category_id" name="category_id" required>
        <option value="">선택</option>
        <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"<?= $catSelected === (int) $c['id'] ? ' selected' : '' ?>><?= esc($c['name_ko']) ?></option><?php endforeach ?>
      </select>
    </div>
    <div class="field">
      <label for="price">가격 (원)<span class="req">*</span></label>
      <input type="text" inputmode="numeric" id="price" name="price" required value="<?= esc($v('price'), 'attr') ?>" placeholder="예: 5500">
    </div>
    <div class="field">
      <label for="name_ko">한글 메뉴명<span class="req">*</span></label>
      <input type="text" id="name_ko" name="name_ko" maxlength="60" required value="<?= esc($v('name_ko'), 'attr') ?>" placeholder="예: 모로우 크림 라떼">
    </div>
    <div class="field">
      <label for="name_en">영문 메뉴명<span class="req">*</span></label>
      <input type="text" id="name_en" name="name_en" maxlength="60" required value="<?= esc($v('name_en'), 'attr') ?>" placeholder="예: Morrow Cream Latte">
    </div>
    <div class="field full">
      <label for="description">설명</label>
      <input type="text" id="description" name="description" maxlength="300" value="<?= esc($v('description'), 'attr') ?>" placeholder="한두 문장으로 맛과 특징을 적어 주세요">
    </div>
    <div class="field">
      <label for="temperature">온도</label>
      <select id="temperature" name="temperature">
        <?php foreach ($temps as $k => $label): ?><option value="<?= $k ?>"<?= $v('temperature', 'none') === $k ? ' selected' : '' ?>><?= $k === 'none' ? '표시 안 함' : $label ?></option><?php endforeach ?>
      </select>
    </div>
    <div class="field">
      <span class="label">표시</span>
      <div class="checks">
        <label class="check"><input type="checkbox" name="is_signature" value="1"<?= $v('is_signature', 0) ? ' checked' : '' ?>> SIGNATURE</label>
        <label class="check"><input type="checkbox" name="is_best" value="1"<?= $v('is_best', 0) ? ' checked' : '' ?>> BEST</label>
        <label class="check"><input type="checkbox" name="is_sold_out" value="1"<?= $v('is_sold_out', 0) ? ' checked' : '' ?>> 품절</label>
        <label class="check"><input type="checkbox" name="is_visible" value="1"<?= $v('is_visible', 1) ? ' checked' : '' ?>> 홈페이지에 표시</label>
      </div>
      <span class="hint">SIGNATURE 메뉴 중 앞쪽 3개가 HOME 화면에 나옵니다.</span>
    </div>
    <div class="field full">
      <span class="label">알레르기 유발 성분</span>
      <div class="checks">
        <?php foreach ($allergens as $k => $label): ?>
        <label class="check"><input type="checkbox" name="allergens[]" value="<?= $k ?>"<?= in_array($k, (array) $checkedAllergens, true) ? ' checked' : '' ?>> <?= $label ?></label>
        <?php endforeach ?>
      </div>
    </div>
    <div class="field full">
      <label for="ingredients">원재료 / 원산지</label>
      <input type="text" id="ingredients" name="ingredients" maxlength="500" value="<?= esc($v('ingredients'), 'attr') ?>" placeholder="예: 에스프레소(브라질·에티오피아), 우유(국산), 생크림(국산)">
    </div>
    <?= $this->setData(['ratio' => $ratio, 'removable' => true, 'label' => '메뉴 사진'])->include('admin/_image_field') ?>
  </div>
  <div class="form-foot">
    <a class="btn btn--line" href="<?= site_url('admin/menus') ?>">목록</a>
    <button class="btn" type="submit">저장</button>
  </div>
</form>
<?= $this->endSection() ?>
