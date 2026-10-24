<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title"><div><h1>매장 사진 갤러리</h1><p>SPACE 페이지에 이 순서대로 보입니다. 'HOME에 표시'를 켠 사진 중 앞쪽 5장은 HOME 공간 소개에 나옵니다.</p></div></div>

<form class="card" action="<?= site_url('admin/gallery') ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <h2>사진 추가</h2>
  <div class="form-grid">
    <?= $this->setData(['row' => null, 'ratio' => 0, 'required' => true, 'label' => '사진'])->include('admin/_image_field') ?>
    <div class="field">
      <label for="area">분류</label>
      <select id="area" name="area"><?php foreach ($areas as $k => $label): ?><option value="<?= $k ?>"<?= old('area') === $k ? ' selected' : '' ?>><?= $label ?></option><?php endforeach ?></select>
    </div>
    <div class="field">
      <label for="caption">사진 설명 문구 (화면에 보임)</label>
      <input type="text" id="caption" name="caption" maxlength="100" value="<?= esc(old('caption'), 'attr') ?>" placeholder="예: 오후 햇살이 드는 창가 자리">
    </div>
    <div class="field full"><label class="check"><input type="checkbox" name="show_on_home" value="1"> HOME에 표시</label></div>
  </div>
  <div class="form-foot"><span></span><button class="btn" type="submit">사진 추가</button></div>
</form>

<div class="tiles">
  <?php foreach ($images as $i => $g): ?>
  <article class="tile" id="g-<?= $g['id'] ?>">
    <img src="<?= media_url($g['media_path'], 'sm') ?>" alt="<?= esc($g['media_alt'], 'attr') ?>" loading="lazy">
    <form class="tile-body" action="<?= site_url("admin/gallery/{$g['id']}") ?>" method="post">
      <?= csrf_field() ?>
      <label class="sr-only" for="cap-<?= $g['id'] ?>">설명 문구</label>
      <input type="text" id="cap-<?= $g['id'] ?>" name="caption" maxlength="100" value="<?= esc($g['caption'], 'attr') ?>" placeholder="설명 문구">
      <label class="sr-only" for="area-<?= $g['id'] ?>">분류</label>
      <select id="area-<?= $g['id'] ?>" name="area"><?php foreach ($areas as $k => $label): ?><option value="<?= $k ?>"<?= $g['area'] === $k ? ' selected' : '' ?>><?= $label ?></option><?php endforeach ?></select>
      <div class="checks">
        <label class="check"><input type="checkbox" name="show_on_home" value="1"<?= $g['show_on_home'] ? ' checked' : '' ?>> HOME</label>
        <label class="check"><input type="checkbox" name="is_visible" value="1"<?= $g['is_visible'] ? ' checked' : '' ?>> 표시</label>
      </div>
      <button class="btn btn--line btn--sm" type="submit">저장</button>
    </form>
    <div class="tile-foot">
      <div class="actions">
        <?php foreach (['up' => '◀', 'down' => '▶'] as $dir => $arrow): ?>
        <form class="inline" action="<?= site_url("admin/gallery/{$g['id']}/move/{$dir}") ?>" method="post"><?= csrf_field() ?>
          <button class="btn btn--line btn--sm btn--icon" type="submit" aria-label="<?= $dir === 'up' ? '앞으로' : '뒤로' ?>"<?= ($dir === 'up' && $i === 0) || ($dir === 'down' && $i === count($images) - 1) ? ' disabled' : '' ?>><?= $arrow ?></button>
        </form>
        <?php endforeach ?>
      </div>
      <form class="inline" action="<?= site_url("admin/gallery/{$g['id']}/delete") ?>" method="post" data-confirm="이 사진을 갤러리에서 삭제할까요?">
        <?= csrf_field() ?><button class="btn btn--danger btn--sm" type="submit">삭제</button>
      </form>
    </div>
  </article>
  <?php endforeach ?>
</div>
<?php if (! $images): ?><div class="card muted">등록된 사진이 없습니다.</div><?php endif ?>
<?= $this->endSection() ?>
