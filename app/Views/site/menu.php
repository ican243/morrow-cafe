<?= $this->extend('layouts/site') ?>
<?= $this->section('content') ?>
<?= $this->setData(['en' => 'Menu', 'ko' => '메뉴', 'lead' => '가격은 원(₩) 기준이며, 계절에 따라 메뉴가 바뀔 수 있습니다.'])->include('partials/page_head') ?>

<?php if (! $categories): ?>
<div class="wrap"><p class="empty">메뉴를 준비하고 있습니다.</p></div>
<?php else: ?>
<nav class="menu-tabs" aria-label="메뉴 카테고리">
  <div class="wrap">
    <ul>
      <?php foreach ($categories as $i => $c): ?>
      <li><a href="#cat-<?= $c['id'] ?>"<?= $i === 0 ? ' class="is-active"' : '' ?>><?= esc($c['name_ko']) ?></a></li>
      <?php endforeach ?>
    </ul>
  </div>
</nav>

<div class="wrap">
  <?php foreach ($categories as $c): ?>
  <section class="menu-cat" id="cat-<?= $c['id'] ?>" aria-labelledby="cat-title-<?= $c['id'] ?>">
    <div class="menu-cat-head">
      <h2 id="cat-title-<?= $c['id'] ?>"><?= esc($c['name_en']) ?></h2>
      <span><?= esc($c['name_ko']) ?></span>
    </div>
    <ul class="menu-list">
      <?php foreach ($menus[$c['id']] as $m):
          $codes = array_filter(explode(',', $m['allergens']));
          $names = array_map(static fn ($k) => $allergens[$k] ?? $k, $codes); ?>
      <li class="menu-item<?= $m['is_sold_out'] ? ' is-sold-out' : '' ?>">
        <figure><?= media_img($m, '104px') ?></figure>
        <div>
          <div class="menu-item-top">
            <h3>
              <?= esc($m['name_ko']) ?>
              <?php if ($m['is_signature'] || $m['is_best'] || $m['is_sold_out']): ?>
              <span class="tags">
                <?php if ($m['is_signature']): ?><span class="tag tag--sig">SIGNATURE</span><?php endif ?>
                <?php if ($m['is_best']): ?><span class="tag">BEST</span><?php endif ?>
                <?php if ($m['is_sold_out']): ?><span class="tag tag--out">품절</span><?php endif ?>
              </span>
              <?php endif ?>
            </h3>
            <span class="price"><?php if ($m['is_sold_out']): ?><span class="sr-only">품절, 가격 </span><?php endif ?><?= won($m['price']) ?></span>
          </div>
          <?php if ($m['name_en'] !== ''): ?><p class="en"><?= esc($m['name_en']) ?></p><?php endif ?>
          <?php if ($m['description']): ?><p class="desc"><?= esc($m['description']) ?></p><?php endif ?>
          <?php if ($m['temperature'] !== 'none'): ?><p class="menu-meta"><span><?= $temps[$m['temperature']] ?></span></p><?php endif ?>
          <?php if ($names || $m['ingredients']): ?>
          <details class="allergy">
            <summary>알레르기·원재료 정보</summary>
            <?php if ($names): ?><p>알레르기 유발: <?= esc(implode(', ', $names)) ?></p><?php endif ?>
            <?php if ($m['ingredients']): ?><p>원재료: <?= esc($m['ingredients']) ?></p><?php endif ?>
          </details>
          <?php endif ?>
        </div>
      </li>
      <?php endforeach ?>
    </ul>
  </section>
  <?php endforeach ?>

  <aside class="menu-notice" aria-labelledby="menu-notice-title">
    <h2 id="menu-notice-title">알레르기 및 원재료 안내</h2>
    <?= paragraphs($s['menu_notice'] ?? '알레르기가 있으신 분은 주문 전 직원에게 말씀해 주세요.') ?>
  </aside>
</div>
<?php endif ?>

<?= $this->endSection() ?>
