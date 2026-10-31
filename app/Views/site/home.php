<?= $this->extend('layouts/site') ?>
<?= $this->section('content') ?>
<?php
$intro = $sections['home_intro'] ?? null;
$story = $sections['brand_story'] ?? null;
$links = map_links($s);
?>

<?php if ($banners): ?>
<section class="hero" aria-roledescription="carousel" aria-label="메인 사진">
  <?php foreach ($banners as $i => $b): ?>
  <div class="hero-slide<?= $i === 0 ? ' is-active' : '' ?>" id="hero-<?= $i ?>"<?= $i ? ' aria-hidden="true"' : '' ?>>
    <?= media_img($b, '(max-aspect-ratio: 3/2) 150vh, 100vw', $i === 0 ? ['loading' => 'eager', 'fetchpriority' => 'high'] : []) ?>
    <div class="hero-copy">
      <div class="wrap">
        <?php if ($b['eyebrow']): ?><p class="eyebrow"><?= esc($b['eyebrow']) ?></p><?php endif ?>
        <?php if ($i === 0): ?><h1><?= esc($b['title']) ?></h1><?php else: ?><h2 class="h1"><?= esc($b['title']) ?></h2><?php endif ?>
        <?php if ($b['subtitle']): ?><p><?= esc($b['subtitle']) ?></p><?php endif ?>
        <?php if ($b['link_label'] && ($url = safe_url($b['link_url']))): ?>
        <a class="btn btn--light" href="<?= esc($url, 'attr') ?>"><?= esc($b['link_label']) ?></a>
        <?php endif ?>
      </div>
    </div>
  </div>
  <?php endforeach ?>
  <?php if (count($banners) > 1): ?>
  <div class="hero-dots">
    <?php foreach ($banners as $i => $b): ?>
    <button type="button" data-slide="<?= $i ?>" aria-controls="hero-<?= $i ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="<?= $i + 1 ?>번째 사진"><?= sprintf('%02d', $i + 1) ?></button>
    <?php endforeach ?>
  </div>
  <?php endif ?>
</section>
<?php else: ?>
<?= $this->setData(['en' => $s['store_name_en'] ?? 'MORROW COFFEE', 'ko' => $s['store_name'] ?? '', 'lead' => $s['tagline'] ?? ''])->include('partials/page_head') ?>
<?php endif ?>

<?php if ($latest): ?>
<div class="notice-bar">
  <div class="wrap">
    <a href="<?= site_url('news/' . $latest['id']) ?>">
      <b>Notice</b>
      <span><?= esc($latest['title']) ?></span>
      <time datetime="<?= esc($latest['published_at'], 'attr') ?>"><?= ko_date($latest['published_at']) ?></time>
    </a>
  </div>
</div>
<?php endif ?>

<?php if ($intro): ?>
<section class="intro" aria-labelledby="intro-title">
  <div class="wrap">
    <div>
      <?php if ($intro['eyebrow']): ?><p class="eyebrow"><?= esc($intro['eyebrow']) ?></p><?php endif ?>
      <h2 id="intro-title"><?= esc($intro['title']) ?></h2>
    </div>
    <div class="intro-body">
      <?= paragraphs($intro['body']) ?>
      <?php $st = today_status($week) ?>
      <p class="today<?= $st['open'] ? '' : ' is-closed' ?>"><i aria-hidden="true"></i><?= esc($st['text']) ?></p>
    </div>
  </div>
</section>
<?php endif ?>

<?php if ($signature): ?>
<section class="signature" aria-labelledby="sig-title">
  <div class="wrap">
    <div class="sec-head">
      <div>
        <p class="eyebrow">모로우의 대표 메뉴</p>
        <h2 class="display" id="sig-title">Signature</h2>
      </div>
      <a class="link-more" href="<?= site_url('menu') ?>">전체 메뉴 보기</a>
    </div>
    <ul class="sig-grid">
      <?php foreach ($signature as $m): ?>
      <li class="sig-item">
        <figure><?= media_img($m, '(max-width: 560px) 100vw, (max-width: 860px) 50vw, 400px') ?></figure>
        <h3><?= esc($m['name_ko']) ?> <span><?= won($m['price']) ?></span></h3>
        <?php if ($m['name_en'] !== ''): ?><p class="en"><?= esc($m['name_en']) ?></p><?php endif ?>
        <?php if ($m['description']): ?><p class="desc"><?= esc($m['description']) ?></p><?php endif ?>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>

<?php if ($story): ?>
<section class="story" aria-labelledby="story-title">
  <div class="wrap split">
    <div class="split-media"><?= media_img($story, '(max-width: 860px) 100vw, 50vw') ?></div>
    <div class="split-text">
      <?php if ($story['eyebrow']): ?><p class="eyebrow"><?= esc($story['eyebrow']) ?></p><?php endif ?>
      <h2 id="story-title"><?= esc($story['title']) ?></h2>
      <div class="body"><?= paragraphs($story['body']) ?></div>
      <a class="link-more" href="<?= site_url('about') ?>">브랜드 이야기 더 보기</a>
    </div>
  </div>
</section>
<?php endif ?>

<?php if ($space): ?>
<section class="space-preview" aria-labelledby="space-title">
  <div class="wrap">
    <div class="sec-head">
      <div>
        <p class="eyebrow">머물고 싶은 자리</p>
        <h2 class="display" id="space-title">Space</h2>
      </div>
      <a class="link-more" href="<?= site_url('space') ?>">공간 둘러보기</a>
    </div>
    <ul class="mosaic">
      <?php foreach ($space as $i => $g): ?>
      <li><?= media_img($g, $i === 0 ? '(max-width: 860px) 80vw, 600px' : '(max-width: 860px) 80vw, 300px') ?></li>
      <?php endforeach ?>
    </ul>
  </div>
</section>
<?php endif ?>

<section class="visit" aria-labelledby="visit-title">
  <div class="wrap">
    <div>
      <p class="eyebrow">찾아오시는 길</p>
      <h2 class="display display-lg" id="visit-title">Visit</h2>
      <address><?= esc(trim(($s['address'] ?? '') . ' ' . ($s['address_detail'] ?? ''))) ?></address>
      <?php if (! empty($s['address_note'])): ?><p class="note"><?= esc($s['address_note']) ?></p><?php endif ?>
      <div class="btn-row">
        <a class="btn" href="<?= site_url('location') ?>"><?= icon('pin') ?> 오시는 길</a>
        <a class="btn btn--line" href="<?= esc($links['naver'], 'attr') ?>" target="_blank" rel="noopener">네이버 길찾기</a>
        <?php if (! empty($s['phone'])): ?><a class="btn btn--line" href="<?= tel_href($s['phone']) ?>"><?= icon('phone') ?> 전화</a><?php endif ?>
      </div>
    </div>
    <div><?= $this->include('partials/hours_table') ?></div>
  </div>
</section>

<?= $this->endSection() ?>
