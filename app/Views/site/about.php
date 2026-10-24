<?= $this->extend('layouts/site') ?>
<?= $this->section('content') ?>
<?= $this->setData(['en' => 'About', 'ko' => '브랜드 소개', 'lead' => $s['tagline'] ?? ''])->include('partials/page_head') ?>

<?php
// 사진·글 좌우를 번갈아 배치. brand_image 는 넓은 사진 한 장으로 따로 보여준다.
$blocks = ['brand_story' => false, 'coffee_philosophy' => true];
foreach ($blocks as $key => $reverse):
    if (empty($sections[$key])) {
        continue;
    }
    $sec = $sections[$key]; ?>
<section class="about-block" aria-labelledby="<?= $key ?>">
  <div class="wrap split<?= $reverse ? ' split--rev' : '' ?>">
    <div class="split-media"><?= media_img($sec, '(max-width: 860px) 100vw, 50vw') ?></div>
    <div class="split-text">
      <?php if ($sec['eyebrow']): ?><p class="eyebrow"><?= esc($sec['eyebrow']) ?></p><?php endif ?>
      <h2 id="<?= $key ?>"><?= esc($sec['title']) ?></h2>
      <div class="body"><?= paragraphs($sec['body']) ?></div>
    </div>
  </div>
</section>
<?php endforeach ?>

<?php if ($img = $sections['brand_image'] ?? null): ?>
<section class="about-wide" aria-labelledby="brand_image">
  <figure><?= media_img($img, '100vw') ?></figure>
  <div class="wrap caption">
    <div>
      <?php if ($img['eyebrow']): ?><p class="eyebrow"><?= esc($img['eyebrow']) ?></p><?php endif ?>
      <h2 id="brand_image"><?= esc($img['title']) ?></h2>
    </div>
    <div class="body"><?= paragraphs($img['body']) ?></div>
  </div>
</section>
<?php endif ?>

<?php if ($sec = $sections['space_concept'] ?? null): ?>
<section class="about-block" aria-labelledby="space_concept">
  <div class="wrap split">
    <div class="split-media"><?= media_img($sec, '(max-width: 860px) 100vw, 50vw') ?></div>
    <div class="split-text">
      <?php if ($sec['eyebrow']): ?><p class="eyebrow"><?= esc($sec['eyebrow']) ?></p><?php endif ?>
      <h2 id="space_concept"><?= esc($sec['title']) ?></h2>
      <div class="body"><?= paragraphs($sec['body']) ?></div>
      <a class="link-more" href="<?= site_url('space') ?>">공간 사진 보기</a>
    </div>
  </div>
</section>
<?php endif ?>

<?= $this->endSection() ?>
