<?php /** 다가오는 임시 휴무일 목록 (HOME·LOCATION) @var array $closures */ ?>
<?php if (! empty($closures)): ?>
<ul class="closures" aria-label="임시 휴무일">
  <?php foreach ($closures as $c): ?>
  <li><strong><?= ko_date($c['closed_on']) ?> (<?= \App\Models\BusinessHourModel::DAY_NAMES[(int) date('w', strtotime($c['closed_on']))] ?>)</strong> 휴무<?= $c['reason'] ? ' · ' . esc($c['reason']) : '' ?></li>
  <?php endforeach ?>
</ul>
<?php endif ?>
