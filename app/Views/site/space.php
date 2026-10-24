<?= $this->extend('layouts/site') ?>
<?= $this->section('content') ?>
<?= $this->setData(['en' => 'Space', 'ko' => '공간', 'lead' => '사진을 누르면 크게 볼 수 있습니다.'])->include('partials/page_head') ?>

<div class="wrap">
  <?php if (! $images): ?>
  <p class="empty">공간 사진을 준비하고 있습니다.</p>
  <?php else: ?>
  <?php $usedAreas = array_unique(array_column($images, 'area')); ?>
  <?php if (count($usedAreas) > 1): ?>
  <div class="filter" role="group" aria-label="사진 분류">
    <button type="button" data-filter="all" aria-pressed="true">전체</button>
    <?php foreach ($areas as $key => $label): if (in_array($key, $usedAreas, true)): ?>
    <button type="button" data-filter="<?= $key ?>" aria-pressed="false"><?= $label ?></button>
    <?php endif; endforeach ?>
  </div>
  <?php endif ?>

  <ul class="gallery" id="gallery">
    <?php foreach ($images as $g): ?>
    <li data-area="<?= esc($g['area'], 'attr') ?>">
      <figure>
        <button type="button" data-full="<?= esc(media_url($g['media_path'], 'lg'), 'attr') ?>" data-caption="<?= esc($g['caption'], 'attr') ?>" aria-label="<?= esc(($g['caption'] ?: $g['media_alt']) . ' 크게 보기', 'attr') ?>">
          <?= media_img($g, '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 400px') ?>
        </button>
        <?php if ($g['caption']): ?><figcaption><?= esc($g['caption']) ?></figcaption><?php endif ?>
      </figure>
    </li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>
</div>

<section class="facility" aria-labelledby="facility-title">
  <div class="wrap">
    <div class="sec-head">
      <div>
        <p class="eyebrow">이용 안내</p>
        <h2 class="display" id="facility-title">Information</h2>
      </div>
    </div>
    <dl>
      <?php
      $info = ['seats' => ['좌석', 'Seats'], 'parking' => ['주차', 'Parking'], 'wifi' => ['와이파이', 'Wi-Fi'], 'pets' => ['반려동물', 'Pets'], 'facilities' => ['편의시설', 'Facilities']];
      foreach ($info as $key => [$ko, $en]):
          if (empty($s[$key])) {
              continue;
          } ?>
      <div>
        <dt><?= $ko ?><span><?= $en ?></span></dt>
        <dd><?= paragraphs($s[$key]) ?></dd>
      </div>
      <?php endforeach ?>
    </dl>
  </div>
</section>

<dialog class="lightbox" id="lightbox" aria-label="사진 크게 보기">
  <figure>
    <figcaption></figcaption>
  </figure>
  <button class="lb-btn lb-close" type="button" data-lb="close" aria-label="닫기"><?= icon('close') ?></button>
  <button class="lb-btn lb-prev" type="button" data-lb="prev" aria-label="이전 사진"><?= icon('prev') ?></button>
  <button class="lb-btn lb-next" type="button" data-lb="next" aria-label="다음 사진"><?= icon('next') ?></button>
</dialog>

<?= $this->endSection() ?>
