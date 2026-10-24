<?php $links = map_links($s); ?>
<footer class="site-footer">
  <div class="wrap">
    <div class="footer-top">
      <div>
        <a class="brand" href="<?= site_url('/') ?>"><b>MORROW</b><span>COFFEE</span></a>
        <?php if (! empty($s['tagline'])): ?><p><?= esc($s['tagline']) ?></p><?php endif ?>
        <div class="footer-sns">
          <?php if ($u = safe_url($s['instagram_url'] ?? '')): ?>
          <a href="<?= esc($u, 'attr') ?>" target="_blank" rel="noopener" aria-label="인스타그램 (새 창)"><?= icon('camera') ?></a>
          <?php endif ?>
          <?php if ($u = safe_url($s['kakao_channel_url'] ?? '')): ?>
          <a href="<?= esc($u, 'attr') ?>" target="_blank" rel="noopener" aria-label="카카오톡 채널 (새 창)"><?= icon('chat') ?></a>
          <?php endif ?>
          <a href="<?= esc($links['naver'], 'attr') ?>" target="_blank" rel="noopener" aria-label="네이버 지도 (새 창)"><?= icon('pin') ?></a>
        </div>
      </div>
      <div>
        <h2>VISIT</h2>
        <ul>
          <li><?= esc(trim(($s['address'] ?? '') . ' ' . ($s['address_detail'] ?? ''))) ?></li>
          <?php if (! empty($s['phone'])): ?><li><a href="<?= tel_href($s['phone']) ?>"><?= esc($s['phone']) ?></a></li><?php endif ?>
        </ul>
      </div>
      <div>
        <h2>HOURS</h2>
        <ul>
          <?php
          // 같은 시간대인 요일끼리 묶어서 짧게 표시 (예: 월–금 08:00–21:00)
          $groups = [];
          foreach ($week as $row) {
              $label = $row['is_closed'] ? '휴무' : hm($row['open_time']) . ' – ' . hm($row['close_time']);
              $last  = array_key_last($groups);
              if ($last !== null && $groups[$last]['label'] === $label) {
                  $groups[$last]['to'] = $row['name'];
              } else {
                  $groups[] = ['from' => $row['name'], 'to' => null, 'label' => $label];
              }
          }
          foreach ($groups as $g): ?>
          <li><?= $g['from'] . ($g['to'] ? '–' . $g['to'] : '') ?> <?= $g['label'] ?></li>
          <?php endforeach ?>
          <?php if (! empty($s['regular_holiday'])): ?><li><?= esc($s['regular_holiday']) ?></li><?php endif ?>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= esc($s['store_name_en'] ?? 'MORROW COFFEE') ?></p>
      <p class="demo-note">포트폴리오용 가상 매장입니다. 사진은 무료 라이선스 이미지이며 실제 매장이 아닙니다.</p>
    </div>
  </div>
</footer>

<nav class="quick-bar" aria-label="빠른 연결">
  <?php if (! empty($s['phone'])): ?><a href="<?= tel_href($s['phone']) ?>"><?= icon('phone') ?> 전화하기</a><?php endif ?>
  <a href="<?= site_url('location') ?>"><?= icon('route') ?> 오시는 길</a>
</nav>
