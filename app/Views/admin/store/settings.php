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
      <div class="field<?= in_array($f['type'], ['textarea', 'url'], true) ? ' full' : '' ?>">
        <label for="<?= $key ?>"><?= esc($f['label']) ?><?= ! empty($f['required']) ? '<span class="req">*</span>' : '' ?><?= ! empty($f['optional']) ? ' <span class="hint hint--inline">(선택)</span>' : '' ?></label>
        <?php if ($f['type'] === 'textarea'): ?>
        <textarea id="<?= $key ?>" name="<?= $key ?>" rows="3"><?= esc($val) ?></textarea>
        <?php else: ?>
        <?php $saved = $f['type'] === 'url' ? safe_url($values[$key] ?? '') : ''; ?>
        <?php $row = $f['type'] === 'url' || ! empty($f['search']); ?>
        <?php if ($row): ?><div class="link-row"><?php endif ?>
        <input type="<?= $type ?>" id="<?= $key ?>" name="<?= $key ?>" value="<?= esc($val, 'attr') ?>"<?= $f['type'] === 'number' ? ' inputmode="decimal"' : '' ?><?= $f['type'] === 'url' ? ' inputmode="url" autocapitalize="off" spellcheck="false"' : '' ?><?= ! empty($f['placeholder']) ? ' placeholder="' . esc($f['placeholder'], 'attr') . '"' : '' ?><?= ! empty($f['required']) ? ' required' : '' ?>>
        <?php if ($saved !== ''): ?>
        <a class="btn btn--line btn--sm" href="<?= esc($saved, 'attr') ?>" target="_blank" rel="noopener noreferrer" title="저장된 주소를 새 창으로 열어 봅니다">열어보기</a>
        <?php endif ?>
        <?php if (! empty($f['search'])): ?>
        <button class="btn btn--line btn--sm" type="button" data-address-search>주소 검색</button>
        <?php endif ?>
        <?php if ($row): ?></div><?php endif ?>
        <?php endif ?>
        <?php if (! empty($f['help'])): ?>
        <span class="hint"><?= esc($f['help']) ?></span>
        <?php endif ?>
        <?php if (! empty($f['optional'])): ?>
        <span class="hint">비워둬도 됩니다. 비우면 길찾기 버튼이 매장 주소로 열립니다. 넣으면 리뷰·사진이 있는 매장 페이지로 열립니다.</span>
        <?php endif ?>
      </div>
      <?php endforeach ?>
    </div>
    <?php if ($g === 'store'): ?>
    <div class="map-check" data-map-check data-kakao-key="<?= esc($kakaoKey, 'attr') ?>">
      <p class="label">지도 위치 확인</p>
      <div class="map-box" data-map-box hidden></div>
      <p class="muted" data-map-msg>'주소 검색'으로 주소를 고르면 위도·경도가 자동으로 채워지고 여기 지도에 표시됩니다. 비워두면 홈페이지에 지도 대신 주소만 나옵니다.</p>
    </div>
    <?php endif ?>
    <?php if ($g === 'sns'): ?>
    <p class="muted note-top">비워둔 인스타그램·카카오톡 채널은 홈페이지에 버튼이 나오지 않습니다.<br>
    저장한 뒤 '열어보기'를 눌러 내 계정·내 매장으로 잘 열리는지 꼭 확인해 주세요.</p>
    <?php endif ?>
  </section>
  <?php endforeach ?>
  <div class="form-foot"><span></span><button class="btn" type="submit">저장</button></div>
</form>
<?= $this->endSection() ?>
