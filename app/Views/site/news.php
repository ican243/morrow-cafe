<?= $this->extend('layouts/site') ?>
<?= $this->section('content') ?>
<?= $this->setData(['en' => 'News', 'ko' => '소식', 'lead' => '공지사항과 이벤트 소식을 전합니다.'])->include('partials/page_head') ?>

<div class="wrap">
  <div class="filter" role="group" aria-label="글 분류">
    <a class="btn btn--line<?= $type === null ? ' is-on' : '' ?>" href="<?= site_url('news') ?>"<?= $type === null ? ' aria-current="page"' : '' ?>>전체</a>
    <?php foreach ($types as $key => $label): ?>
    <a class="btn btn--line<?= $type === $key ? ' is-on' : '' ?>" href="<?= site_url('news?type=' . $key) ?>"<?= $type === $key ? ' aria-current="page"' : '' ?>><?= $label ?></a>
    <?php endforeach ?>
  </div>

  <?php if (! $posts): ?>
  <p class="empty">등록된 글이 없습니다.</p>
  <?php else: ?>
  <ul class="news-grid">
    <?php foreach ($posts as $p): ?>
    <?php $ev = event_state($p) ?>
    <li class="news-card<?= $ev && $ev['key'] === 'end' ? ' is-ended' : '' ?>">
      <a href="<?= site_url('news/' . $p['id']) ?>">
        <figure><?= media_img($p, '(max-width: 560px) 100vw, (max-width: 1024px) 50vw, 400px') ?></figure>
        <div class="news-meta">
          <?php if ($p['is_pinned']): ?><span class="pin">고정</span><?php endif ?>
          <span class="type"><?= $types[$p['type']] ?></span>
          <?php if ($ev): ?><span class="state state--<?= $ev['key'] ?>"><?= $ev['text'] ?></span><?php endif ?>
          <time datetime="<?= esc($p['published_at'], 'attr') ?>"><?= ko_date($p['published_at']) ?></time>
        </div>
        <h2><?= esc($p['title']) ?></h2>
        <?php if ($ev && $p['event_start']): ?><p class="period">기간 <?= ko_date($p['event_start']) ?><?= $p['event_end'] ? ' – ' . ko_date($p['event_end']) : '' ?></p><?php endif ?>
        <?php if ($p['summary']): ?><p><?= esc($p['summary']) ?></p><?php endif ?>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
  <?php endif ?>

  <?php if ($pager->getPageCount() > 1): ?><div class="pager"><?= $pager->links() ?></div><?php endif ?>
</div>

<?= $this->endSection() ?>
