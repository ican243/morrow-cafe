<?php
/**
 * 서브 페이지 상단 제목
 * @var string $en 영문 큰 제목
 * @var string $ko 한글 메뉴명
 * @var string $lead 한 줄 설명
 */
?>
<section class="page-head">
  <div class="wrap">
    <p class="eyebrow"><?= esc($ko) ?></p>
    <h1 class="display"><?= esc($en) ?></h1>
    <?php if (! empty($lead)): ?><p><?= esc($lead) ?></p><?php endif ?>
  </div>
</section>
