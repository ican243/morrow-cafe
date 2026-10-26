<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $groupTitles = ['store' => '기본 정보', 'facility' => '이용 안내 (SPACE·MENU 페이지)', 'sns' => 'SNS·외부 링크']; ?>
<div class="page-title"><h1><?= esc($title) ?></h1></div>
<form action="<?= site_url($action) ?>" method="post">
  <?= csrf_field() ?>
  <?php foreach ($groups as $g => $fields): ?>
  <section class="card">
    <h2><?= $groupTitles[$g] ?></h2>
    <div class="form-grid">
      <?php foreach ($fields as $key => $f):
          $val  = old($key, $values[$key] ?? '');
          $type = in_array($f['type'], ['number', 'url'], true) ? 'text' : $f['type']; ?>
      <div class="field<?= $f['type'] === 'textarea' ? ' full' : '' ?>">
        <label for="<?= $key ?>"><?= esc($f['label']) ?><?= ! empty($f['required']) ? '<span class="req">*</span>' : '' ?></label>
        <?php if ($f['type'] === 'textarea'): ?>
        <textarea id="<?= $key ?>" name="<?= $key ?>" rows="3"><?= esc($val) ?></textarea>
        <?php else: ?>
        <input type="<?= $type ?>" id="<?= $key ?>" name="<?= $key ?>" value="<?= esc($val, 'attr') ?>"<?= $f['type'] === 'number' ? ' inputmode="decimal"' : '' ?><?= $f['type'] === 'url' ? ' inputmode="url" autocapitalize="off" spellcheck="false"' : '' ?><?= ! empty($f['placeholder']) ? ' placeholder="' . esc($f['placeholder'], 'attr') . '"' : '' ?><?= ! empty($f['required']) ? ' required' : '' ?>>
        <?php endif ?>
      </div>
      <?php endforeach ?>
    </div>
    <?php if ($g === 'store'): ?>
    <p class="muted note-top">위도·경도는 카카오맵에서 매장을 검색 → 우클릭 '이 위치의 좌표'로 확인할 수 있습니다. 비워두면 지도 대신 주소만 표시됩니다.</p>
    <?php endif ?>
    <?php if ($g === 'sns'): ?>
    <p class="muted note-top">비워둔 SNS는 홈페이지에서 버튼이 나오지 않습니다. 주소 앞의 https:// 는 빼고 넣어도 저장할 때 자동으로 붙습니다.<br>
    네이버·카카오 장소 주소를 넣으면 길찾기 버튼이 그 매장 페이지(리뷰·사진)로 연결되고, 비우면 매장 주소와 지도 좌표로 길찾기가 열립니다.</p>
    <?php endif ?>
  </section>
  <?php endforeach ?>
  <div class="form-foot"><span></span><button class="btn" type="submit">저장</button></div>
</form>
<?= $this->endSection() ?>
