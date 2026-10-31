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
    <?php
    $pages  = config('Cafe')->linkPages;
    $url    = $v('link_url');
    $target = old('link_target', $url === '' ? '' : (isset($pages[$url]) ? $url : 'custom'));
    ?>
    <div class="field">
      <label for="link_target">버튼 누르면 갈 곳</label>
      <select id="link_target" name="link_target" data-link-target>
        <option value=""<?= $target === '' ? ' selected' : '' ?>>버튼 없음</option>
        <?php foreach ($pages as $path => [$name]): ?>
        <option value="<?= esc($path, 'attr') ?>"<?= $target === $path ? ' selected' : '' ?>><?= esc($name) ?></option>
        <?php endforeach ?>
        <option value="custom"<?= $target === 'custom' ? ' selected' : '' ?>>직접 입력 (특정 글·다른 사이트)</option>
      </select>
      <div class="link-custom" data-link-custom<?= $target === 'custom' ? '' : ' hidden' ?>>
        <input type="text" id="link_url" name="link_url" maxlength="300" value="<?= esc($target === 'custom' ? $url : '', 'attr') ?>" inputmode="url" autocapitalize="off" spellcheck="false" placeholder="연결할 페이지 주소를 복사해서 붙여넣기" aria-label="직접 입력할 주소">
        <span class="hint">홈페이지의 특정 공지 글이나 예약·주문 사이트처럼 목록에 없는 곳으로 보낼 때 씁니다.</span>
      </div>
    </div>
    <div class="field" data-link-label<?= $target === '' ? ' hidden' : '' ?>>
      <label for="link_label">버튼 글자</label>
      <input type="text" id="link_label" name="link_label" maxlength="30" value="<?= esc($v('link_label'), 'attr') ?>" placeholder="예: 메뉴 보기">
      <span class="hint">비워두면 '메뉴 보기'처럼 자동으로 넣습니다.</span>
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
