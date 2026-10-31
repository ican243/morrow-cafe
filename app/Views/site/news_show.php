<?= $this->extend('layouts/site') ?>
<?= $this->section('content') ?>

<div class="wrap">
  <article class="article">
    <?php $ev = event_state($post) ?>
    <div class="news-meta"><span class="type"><?= $types[$post['type']] ?></span><?php if ($ev): ?><span class="state state--<?= $ev['key'] ?>"><?= $ev['text'] ?></span><?php endif ?></div>
    <h1><?= esc($post['title']) ?></h1>
    <div class="article-meta">
      <span>작성일 <time datetime="<?= esc($post['published_at'], 'attr') ?>"><?= ko_date($post['published_at']) ?></time></span>
      <?php if ($post['type'] === 'event' && $post['event_start']): ?>
      <span>이벤트 기간 <?= ko_date($post['event_start']) ?><?= $post['event_end'] ? ' – ' . ko_date($post['event_end']) : '' ?><?= $ev && $ev['key'] === 'end' ? ' (종료된 이벤트입니다)' : '' ?></span>
      <?php endif ?>
    </div>
    <?php if ($post['media_path']): ?><figure><?= media_img($post, '(max-width: 800px) 100vw, 760px', ['loading' => 'eager']) ?></figure><?php endif ?>
    <div class="article-body"><?= paragraphs($post['body']) ?></div>

    <nav class="article-nav" aria-label="이전 글, 다음 글">
      <?php if ($next): ?><a href="<?= site_url('news/' . $next['id']) ?>"><span>다음 글</span><span><?= esc($next['title']) ?></span></a><?php endif ?>
      <?php if ($prev): ?><a href="<?= site_url('news/' . $prev['id']) ?>"><span>이전 글</span><span><?= esc($prev['title']) ?></span></a><?php endif ?>
    </nav>
    <p class="article-back"><a class="btn btn--line" href="<?= site_url('news') ?>">목록으로</a></p>
  </article>
</div>

<?= $this->endSection() ?>
