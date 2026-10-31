<?= $this->extend('layouts/site') ?>
<?= $this->section('content') ?>
<?php
$links    = map_links($s);
$address  = trim(($s['address'] ?? '') . ' ' . ($s['address_detail'] ?? ''));
$hasPos   = is_numeric($s['map_lat'] ?? null) && is_numeric($s['map_lng'] ?? null);
?>
<?= $this->setData(['en' => 'Location', 'ko' => '오시는 길', 'lead' => ''])->include('partials/page_head') ?>

<div class="wrap">
  <div class="map" id="map"
    <?php if ($kakaoKey && $hasPos): ?>data-lat="<?= esc($s['map_lat'], 'attr') ?>" data-lng="<?= esc($s['map_lng'], 'attr') ?>" data-name="<?= esc($s['store_name'] ?? '', 'attr') ?>"<?php endif ?>>
    <div class="map-fallback">
      <div>
        <strong><?= esc($s['store_name'] ?? '') ?></strong>
        <?= esc($address) ?>
      </div>
    </div>
  </div>

  <div class="loc-grid">
    <div>
      <div class="loc-block">
        <h2>ADDRESS</h2>
        <address id="addr"><?= esc($address) ?></address>
        <button class="copy-btn" type="button" data-copy="<?= esc($address, 'attr') ?>">주소 복사</button>
        <?php if (! empty($s['address_note'])): ?><div class="note"><?= paragraphs($s['address_note']) ?></div><?php endif ?>
      </div>

      <?php if (! empty($s['phone'])): ?>
      <div class="loc-block">
        <h2>CALL</h2>
        <a class="tel" href="<?= tel_href($s['phone']) ?>"><?= esc($s['phone']) ?></a>
      </div>
      <?php endif ?>

      <div class="loc-block">
        <h2>DIRECTIONS · SNS</h2>
        <ul class="link-list">
          <li><a href="<?= esc($links['naver'], 'attr') ?>" target="_blank" rel="noopener"><?= icon('route') ?> 네이버 지도로 길찾기<span class="sr-only"> (새 창)</span></a></li>
          <li><a href="<?= esc($links['kakao'], 'attr') ?>" target="_blank" rel="noopener"><?= icon('route') ?> 카카오맵으로 길찾기<span class="sr-only"> (새 창)</span></a></li>
          <?php if ($u = safe_url($s['kakao_channel_url'] ?? '')): ?>
          <li><a href="<?= esc($u, 'attr') ?>" target="_blank" rel="noopener"><?= icon('chat') ?> 카카오톡 채널 문의<span class="sr-only"> (새 창)</span></a></li>
          <?php endif ?>
          <?php if ($u = safe_url($s['instagram_url'] ?? '')): ?>
          <li><a href="<?= esc($u, 'attr') ?>" target="_blank" rel="noopener"><?= icon('camera') ?> 인스타그램<span class="sr-only"> (새 창)</span></a></li>
          <?php endif ?>
        </ul>
      </div>
    </div>

    <div>
      <div class="loc-block">
        <?= $this->include('partials/hours_table') ?>
        <?php if (! empty($s['regular_holiday'])): ?><p class="hours-note"><?= esc($s['regular_holiday']) ?></p><?php endif ?>
        <?= $this->include('partials/closures') ?>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?php if ($kakaoKey && $hasPos): ?>
<?= $this->section('scripts') ?>
<script src="https://dapi.kakao.com/v2/maps/sdk.js?appkey=<?= esc($kakaoKey, 'url') ?>&autoload=false" defer></script>
<?= $this->endSection() ?>
<?php endif ?>
