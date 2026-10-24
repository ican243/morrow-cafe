<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $old = old('days'); ?>
<div class="page-title"><div><h1>영업시간·휴무일</h1><p>HOME, LOCATION, 바닥글에 같이 반영됩니다.</p></div></div>
<form class="card table-wrap" action="<?= site_url('admin/hours') ?>" method="post">
  <?= csrf_field() ?>
  <h2>요일별 영업시간</h2>
  <table class="list hours-edit">
    <thead><tr><th>요일</th><th>휴무</th><th>여는 시간</th><th>닫는 시간</th><th>라스트 오더</th><th>브레이크 시작</th><th>브레이크 끝</th></tr></thead>
    <tbody>
    <?php foreach ($week as $d => $row):
        $val = static fn ($k) => $old[$d][$k] ?? hm($row[$k] ?? null); ?>
    <tr>
      <th scope="row"><?= $row['name'] ?></th>
      <td><input type="checkbox" name="days[<?= $d ?>][is_closed]" value="1" data-closed aria-label="<?= $row['name'] ?>요일 휴무"<?= ($old[$d]['is_closed'] ?? $row['is_closed']) ? ' checked' : '' ?>></td>
      <?php foreach (['open_time' => '여는 시간', 'close_time' => '닫는 시간', 'last_order' => '라스트 오더', 'break_start' => '브레이크 시작', 'break_end' => '브레이크 끝'] as $k => $label): ?>
      <td><input type="time" name="days[<?= $d ?>][<?= $k ?>]" value="<?= esc($val($k), 'attr') ?>" aria-label="<?= $row['name'] ?>요일 <?= $label ?>"></td>
      <?php endforeach ?>
    </tr>
    <?php endforeach ?>
    </tbody>
  </table>
  <div class="form-foot"><span class="muted">라스트 오더·브레이크타임은 없으면 비워두세요.</span><button class="btn" type="submit">영업시간 저장</button></div>
</form>

<section class="card">
  <h2>임시 휴무일 (명절, 내부 공사 등)</h2>
  <form class="form-grid filter-grid" action="<?= site_url('admin/closures') ?>" method="post">
    <?= csrf_field() ?>
    <div class="field"><label for="closed_on">날짜</label><input type="date" id="closed_on" name="closed_on" required min="<?= date('Y-m-d') ?>"></div>
    <div class="field"><label for="reason">사유</label><input type="text" id="reason" name="reason" maxlength="100" placeholder="예: 추석 연휴"></div>
    <span></span>
    <button class="btn" type="submit">추가</button>
  </form>
  <?php if ($closures): ?>
  <table class="list note-top"><tbody>
    <?php foreach ($closures as $c): ?>
    <tr>
      <td><?= ko_date($c['closed_on']) ?> (<?= \App\Models\BusinessHourModel::DAY_NAMES[(int) date('w', strtotime($c['closed_on']))] ?>)</td>
      <td><?= esc($c['reason']) ?></td>
      <td class="num">
        <form class="inline" action="<?= site_url("admin/closures/{$c['id']}/delete") ?>" method="post" data-confirm="이 휴무일을 삭제할까요?"><?= csrf_field() ?><button class="btn btn--danger btn--sm" type="submit">삭제</button></form>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody></table>
  <?php else: ?>
  <p class="muted note-top">예정된 임시 휴무일이 없습니다.</p>
  <?php endif ?>
</section>
<?= $this->endSection() ?>
