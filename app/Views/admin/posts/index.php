<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title">
  <div><h1>공지·이벤트</h1><p>게시일이 미래면 그 시간에 자동으로 공개됩니다.</p></div>
  <a class="btn" href="<?= site_url('admin/posts/new') ?>">+ 글 쓰기</a>
</div>
<nav class="tabs" aria-label="글 분류">
  <a href="<?= site_url('admin/posts') ?>"<?= $type === null ? ' aria-current="page"' : '' ?>>전체</a>
  <?php foreach ($types as $k => $label): ?><a href="<?= site_url('admin/posts?type=' . $k) ?>"<?= $type === $k ? ' aria-current="page"' : '' ?>><?= $label ?></a><?php endforeach ?>
</nav>
<div class="card table-wrap">
  <table class="list">
    <thead><tr><th>분류</th><th>제목</th><th>게시일</th><th>상태</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($posts as $p):
        $scheduled = $p['is_published'] && strtotime($p['published_at']) > time(); ?>
    <tr>
      <td><?= $types[$p['type']] ?></td>
      <td><?= $p['is_pinned'] ? '<span class="badge badge--dark">고정</span> ' : '' ?><a href="<?= site_url("admin/posts/{$p['id']}/edit") ?>"><?= esc($p['title']) ?></a></td>
      <td class="muted"><?= date('Y.m.d H:i', strtotime($p['published_at'])) ?></td>
      <td><?= ! $p['is_published'] ? '<span class="badge badge--off">비공개</span>' : ($scheduled ? '<span class="badge">예약</span>' : '<span class="badge badge--ok">공개</span>') ?></td>
      <td class="num">
        <div class="actions actions--end">
          <?php if ($p['is_published'] && ! $scheduled): ?><a class="btn btn--line btn--sm" href="<?= site_url('news/' . $p['id']) ?>" target="_blank" rel="noopener">보기 ↗</a><?php endif ?>
          <form class="inline" action="<?= site_url("admin/posts/{$p['id']}/delete") ?>" method="post" data-confirm="'<?= esc($p['title'], 'attr') ?>' 글을 삭제할까요?">
            <?= csrf_field() ?><button class="btn btn--danger btn--sm" type="submit">삭제</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endforeach ?>
    <?php if (! $posts): ?><tr><td colspan="5" class="muted">등록된 글이 없습니다.</td></tr><?php endif ?>
    </tbody>
  </table>
  <?php if ($pager->getPageCount() > 1): ?><div class="pager"><?= $pager->links() ?></div><?php endif ?>
</div>
<?= $this->endSection() ?>
