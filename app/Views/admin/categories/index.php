<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title">
  <div><h1>메뉴 카테고리</h1><p>MENU 페이지 상단 탭 순서대로 보입니다. ⋮⋮ 를 끌어 순서를 바꿉니다.</p></div>
  <a class="btn" href="<?= site_url('admin/categories/new') ?>">+ 카테고리 추가</a>
</div>
<div class="card table-wrap">
  <table class="list">
    <thead><tr><th>순서</th><th>이름</th><th>영문</th><th class="num">메뉴 수</th><th>상태</th><th></th></tr></thead>
    <tbody data-sortable="<?= site_url('admin/categories/sort') ?>">
    <?php foreach ($categories as $i => $c): $n = (int) ($counts[$c['id']] ?? 0); ?>
      <tr data-id="<?= $c['id'] ?>" class="<?= $c['is_visible'] ? '' : 'is-hidden-row' ?>">
        <td>
          <div class="actions">
            <span class="drag-handle" title="끌어서 순서 바꾸기" aria-hidden="true"></span>
          </div>
        </td>
        <td><strong><?= esc($c['name_ko']) ?></strong></td>
        <td><?= esc($c['name_en']) ?></td>
        <td class="num"><a href="<?= site_url('admin/menus?category=' . $c['id']) ?>"><?= $n ?>개</a></td>
        <td><?= $c['is_visible'] ? '<span class="badge badge--ok">표시</span>' : '<span class="badge badge--off">숨김</span>' ?></td>
        <td class="num">
          <div class="actions actions--end">
            <a class="btn btn--line btn--sm" href="<?= site_url("admin/categories/{$c['id']}/edit") ?>">수정</a>
            <form class="inline" action="<?= site_url("admin/categories/{$c['id']}/delete") ?>" method="post"
              data-confirm="<?= $n ? "이 카테고리에 메뉴가 {$n}개 있어 삭제할 수 없습니다. 메뉴를 먼저 옮겨 주세요." : "'" . esc($c['name_ko'], 'attr') . "' 카테고리를 삭제할까요?" ?>">
              <?= csrf_field() ?><button class="btn btn--danger btn--sm" type="submit"<?= $n ? ' disabled title="메뉴가 있는 카테고리는 삭제할 수 없습니다"' : '' ?>>삭제</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach ?>
    <?php if (! $categories): ?><tr><td colspan="6" class="muted">카테고리가 없습니다. 먼저 카테고리를 추가해 주세요.</td></tr><?php endif ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
