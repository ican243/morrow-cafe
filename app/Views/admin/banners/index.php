<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title">
  <div><h1>메인 배너</h1><p>HOME 첫 화면의 큰 사진과 문구입니다. 여러 개면 위에서부터 순서대로 넘겨볼 수 있습니다.</p></div>
  <a class="btn" href="<?= site_url('admin/banners/new') ?>">+ 배너 추가</a>
</div>
<div class="card table-wrap">
  <table class="list list--banner">
    <thead><tr><th>순서</th><th>사진</th><th>문구</th><th>노출 기간</th><th>상태</th><th></th></tr></thead>
    <tbody data-sortable="<?= site_url('admin/banners/sort') ?>">
    <?php foreach ($banners as $i => $b): ?>
    <tr data-id="<?= $b['id'] ?>">
      <td>
        <div class="actions">
          <span class="drag-handle" title="끌어서 순서 바꾸기" aria-hidden="true"></span>
          <?php foreach (['up' => '▲', 'down' => '▼'] as $dir => $arrow): ?>
          <form class="inline" action="<?= site_url("admin/banners/{$b['id']}/move/{$dir}") ?>" method="post"><?= csrf_field() ?>
            <button class="btn btn--line btn--sm btn--icon" type="submit" data-move="<?= $dir ?>" aria-label="<?= $dir === 'up' ? '위로' : '아래로' ?>"<?= ($dir === 'up' && $i === 0) || ($dir === 'down' && $i === count($banners) - 1) ? ' disabled' : '' ?>><?= $arrow ?></button>
          </form>
          <?php endforeach ?>
        </div>
      </td>
      <td><img class="thumb thumb--wide" src="<?= media_url($b['media_path'], 'sm') ?>" alt=""></td>
      <td><strong><?= esc($b['title']) ?></strong><div class="muted"><?= esc($b['subtitle']) ?></div></td>
      <td class="muted"><?= $b['starts_at'] || $b['ends_at'] ? ko_date($b['starts_at']) . ' ~ ' . ko_date($b['ends_at']) : '항상' ?></td>
      <td>
        <?php if (in_array($b['id'], $current)): ?><span class="badge badge--ok">노출 중</span>
        <?php elseif (! $b['is_active']): ?><span class="badge badge--off">꺼짐</span>
        <?php else: ?><span class="badge">기간 외</span><?php endif ?>
      </td>
      <td class="num">
        <div class="actions actions--end">
          <a class="btn btn--line btn--sm" href="<?= site_url("admin/banners/{$b['id']}/edit") ?>">수정</a>
          <form class="inline" action="<?= site_url("admin/banners/{$b['id']}/delete") ?>" method="post" data-confirm="이 배너를 삭제할까요?">
            <?= csrf_field() ?><button class="btn btn--danger btn--sm" type="submit">삭제</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach ?>
    <?php if (! $banners): ?><tr><td colspan="6" class="muted">등록된 배너가 없습니다. 배너가 없으면 HOME 상단에 매장 이름만 표시됩니다.</td></tr><?php endif ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
