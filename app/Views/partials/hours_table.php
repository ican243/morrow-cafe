<?php
/**
 * 영업시간 표 (HOME, LOCATION 공용)
 * @var array $week  BusinessHourModel::week()
 * @var int   $today 0(일)~6(토)
 */
?>
<table class="hours">
  <caption>Opening Hours</caption>
  <tbody>
  <?php foreach ($week as $d => $row): ?>
    <tr<?= $d === $today ? ' class="is-today"' : '' ?>>
      <th scope="row"><?= $row['name'] ?></th>
      <?php if ($row['is_closed']): ?>
      <td class="closed">정기 휴무</td>
      <?php else: ?>
      <td>
        <?= hm($row['open_time']) ?> – <?= hm($row['close_time']) ?>
        <?php if (! empty($row['last_order']) || ! empty($row['break_start'])): ?>
        <small>
          <?= ! empty($row['last_order']) ? '라스트 오더 ' . hm($row['last_order']) : '' ?>
          <?= ! empty($row['break_start']) ? ' · 브레이크 ' . hm($row['break_start']) . '–' . hm($row['break_end']) : '' ?>
        </small>
        <?php endif ?>
      </td>
      <?php endif ?>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
<?php if (! empty($s['hours_note'])): ?><p class="hours-note"><?= esc($s['hours_note']) ?></p><?php endif ?>
