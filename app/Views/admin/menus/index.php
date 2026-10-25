<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title">
  <div><h1>메뉴 관리</h1><p>품절·가격·숨김은 목록에서 바로 바꿀 수 있습니다. ⋮⋮ 를 끌거나 ▲▼ 로 카테고리 안의 순서를 바꿉니다.</p></div>
  <a class="btn" href="<?= site_url('admin/menus/new' . ($filter['category'] ? '?category=' . $filter['category'] : '')) ?>">+ 메뉴 추가</a>
</div>

<form class="card" method="get" action="<?= site_url('admin/menus') ?>">
  <div class="form-grid filter-grid">
    <div class="field">
      <label for="f-cat">카테고리</label>
      <select id="f-cat" name="category">
        <option value="">전체</option>
        <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"<?= $filter['category'] === (int) $c['id'] ? ' selected' : '' ?>><?= esc($c['name_ko']) ?></option><?php endforeach ?>
      </select>
    </div>
    <div class="field">
      <label for="f-q">메뉴명 검색</label>
      <input type="search" id="f-q" name="q" value="<?= esc($filter['q'], 'attr') ?>" placeholder="한글 또는 영문">
    </div>
    <label class="check"><input type="checkbox" name="soldout" value="1"<?= $filter['soldout'] ? ' checked' : '' ?>> 품절만</label>
    <button class="btn btn--line" type="submit">보기</button>
  </div>
</form>

<?php if (! $categories): ?>
<div class="card"><p>메뉴를 넣으려면 먼저 <a href="<?= site_url('admin/categories/new') ?>">카테고리를 추가</a>해 주세요.</p></div>
<?php endif ?>

<?php foreach ($categories as $c): if (empty($grouped[$c['id']])) continue; $items = $grouped[$c['id']]; ?>
<section class="card table-wrap">
  <div class="card-head">
    <h2><?= esc($c['name_ko']) ?> <span class="muted"><?= count($items) ?>개<?= $c['is_visible'] ? '' : ' · 카테고리 숨김 상태' ?></span></h2>
    <a class="btn btn--line btn--sm" href="<?= site_url('admin/menus/new?category=' . $c['id']) ?>">+ 이 카테고리에 추가</a>
  </div>
  <table class="list list--menu">
    <thead><tr><th>순서</th><th>사진</th><th>메뉴</th><th>가격(원)</th><th>판매</th><th>표시</th><th></th></tr></thead>
    <tbody<?= $filter['q'] === '' && ! $filter['soldout'] ? ' data-sortable="' . site_url('admin/menus/sort') . '"' : '' ?>>
    <?php foreach ($items as $i => $m): ?>
    <tr id="menu-<?= $m['id'] ?>" data-id="<?= $m['id'] ?>" class="<?= $m['is_visible'] ? '' : 'is-hidden-row' ?>">
      <td>
        <div class="actions">
          <?php if ($filter['q'] === '' && ! $filter['soldout']): ?><span class="drag-handle" title="끌어서 순서 바꾸기" aria-hidden="true"></span><?php endif ?>
          <?php foreach (['up' => '▲', 'down' => '▼'] as $dir => $arrow): ?>
          <form class="inline" action="<?= site_url("admin/menus/{$m['id']}/move/{$dir}") ?>" method="post"><?= csrf_field() ?>
            <button class="btn btn--line btn--sm btn--icon" type="submit" data-move="<?= $dir ?>" aria-label="<?= esc($m['name_ko'], 'attr') ?> <?= $dir === 'up' ? '위로' : '아래로' ?>"<?= ($dir === 'up' && $i === 0) || ($dir === 'down' && $i === count($items) - 1) || $filter['q'] !== '' || $filter['soldout'] ? ' disabled' : '' ?>><?= $arrow ?></button>
          </form>
          <?php endforeach ?>
        </div>
      </td>
      <td><?= $m['media_path'] ? '<img class="thumb" src="' . media_url($m['media_path'], 'sm') . '" alt="">' : '<div class="thumb"></div>' ?></td>
      <td>
        <strong><?= esc($m['name_ko']) ?></strong>
        <?= $m['is_signature'] ? '<span class="badge badge--dark">SIGNATURE</span>' : '' ?>
        <?= $m['is_best'] ? '<span class="badge">BEST</span>' : '' ?>
        <div class="muted"><?= esc($m['name_en']) ?></div>
      </td>
      <td>
        <form class="price-form" action="<?= site_url("admin/menus/{$m['id']}/quick") ?>" method="post">
          <?= csrf_field() ?><input type="hidden" name="field" value="price">
          <label class="sr-only" for="price-<?= $m['id'] ?>"><?= esc($m['name_ko']) ?> 가격</label>
          <input type="text" inputmode="numeric" pattern="[0-9]*" id="price-<?= $m['id'] ?>" name="price" value="<?= (int) $m['price'] ?>" required>
          <button class="btn btn--line btn--sm" type="submit">변경</button>
        </form>
      </td>
      <td>
        <form class="inline" action="<?= site_url("admin/menus/{$m['id']}/quick") ?>" method="post">
          <?= csrf_field() ?><input type="hidden" name="field" value="is_sold_out">
          <button class="btn btn--line btn--sm toggle<?= $m['is_sold_out'] ? ' is-on' : '' ?>" type="submit" aria-label="<?= esc($m['name_ko'], 'attr') ?> <?= $m['is_sold_out'] ? '판매 재개' : '품절 처리' ?>"><?= $m['is_sold_out'] ? '품절' : '판매중' ?></button>
        </form>
      </td>
      <td>
        <form class="inline" action="<?= site_url("admin/menus/{$m['id']}/quick") ?>" method="post">
          <?= csrf_field() ?><input type="hidden" name="field" value="is_visible">
          <button class="btn btn--line btn--sm toggle" type="submit" aria-label="<?= esc($m['name_ko'], 'attr') ?> <?= $m['is_visible'] ? '숨기기' : '표시하기' ?>"><?= $m['is_visible'] ? '표시' : '숨김' ?></button>
        </form>
      </td>
      <td class="num">
        <div class="actions actions--end">
          <a class="btn btn--line btn--sm" href="<?= site_url("admin/menus/{$m['id']}/edit") ?>">수정</a>
          <form class="inline" action="<?= site_url("admin/menus/{$m['id']}/delete") ?>" method="post" data-confirm="'<?= esc($m['name_ko'], 'attr') ?>' 메뉴를 삭제할까요? 되돌릴 수 없습니다. 잠시 내리려면 '숨김'을 쓰세요.">
            <?= csrf_field() ?><button class="btn btn--danger btn--sm" type="submit">삭제</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</section>
<?php endforeach ?>
<?php if ($categories && ! $grouped): ?><div class="card"><p class="muted">조건에 맞는 메뉴가 없습니다.</p></div><?php endif ?>
<?= $this->endSection() ?>
