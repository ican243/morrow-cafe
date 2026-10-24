<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title">
  <div>
    <h1>대시보드</h1>
    <p><?= date('Y년 n월 j일') ?> · 오늘 <?= $today['is_closed'] ? '휴무' : hm($today['open_time']) . ' – ' . hm($today['close_time']) . ' 영업' ?></p>
  </div>
  <a class="btn" href="<?= site_url('admin/menus') ?>">메뉴 품절·가격 바꾸기</a>
</div>

<?php if ($todo): ?>
<section class="card">
  <h2>확인이 필요한 항목</h2>
  <ul class="todo">
    <?php foreach ($todo as [$text, $link]): ?>
    <li><span><?= esc($text) ?></span><?php if ($link): ?><a class="btn btn--line btn--sm" href="<?= site_url($link) ?>">바로가기</a><?php endif ?></li>
    <?php endforeach ?>
  </ul>
</section>
<?php endif ?>

<div class="stats">
  <?php foreach ($stats as [$label, $count, $link]): ?>
  <a class="stat" href="<?= site_url($link) ?>"><span><?= $label ?></span><b><?= number_format($count) ?></b></a>
  <?php endforeach ?>
</div>

<div class="two-col">
  <section class="card">
    <div class="card-head"><h2>지금 품절인 메뉴</h2></div>
    <?php if ($soldOut): ?>
    <table class="list"><tbody>
      <?php foreach ($soldOut as $m): ?>
      <tr>
        <td><?= esc($m['name_ko']) ?></td>
        <td class="num">
          <form class="inline" action="<?= site_url('admin/menus/' . $m['id'] . '/quick') ?>" method="post">
            <?= csrf_field() ?><input type="hidden" name="field" value="is_sold_out">
            <button class="btn btn--line btn--sm" type="submit">판매 재개</button>
          </form>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody></table>
    <?php else: ?>
    <p class="muted">품절인 메뉴가 없습니다.</p>
    <?php endif ?>
  </section>

  <section class="card">
    <div class="card-head"><h2>최근 공지·이벤트</h2><a class="btn btn--line btn--sm" href="<?= site_url('admin/posts/new') ?>">글 쓰기</a></div>
    <?php if ($posts): ?>
    <table class="list"><tbody>
      <?php foreach ($posts as $p): ?>
      <tr>
        <td><a href="<?= site_url('admin/posts/' . $p['id'] . '/edit') ?>"><?= esc($p['title']) ?></a> <?= $p['is_published'] ? '' : '<span class="badge badge--off">비공개</span>' ?></td>
        <td class="num muted"><?= ko_date($p['published_at']) ?></td>
      </tr>
      <?php endforeach ?>
    </tbody></table>
    <?php else: ?>
    <p class="muted">등록된 글이 없습니다.</p>
    <?php endif ?>
  </section>
</div>

<section class="card">
  <div class="card-head"><h2>다가오는 임시 휴무일</h2><a class="btn btn--line btn--sm" href="<?= site_url('admin/hours') ?>">휴무일 관리</a></div>
  <?php if ($closures): ?>
  <ul><?php foreach ($closures as $c): ?><li><?= ko_date($c['closed_on']) ?> <?= esc($c['reason']) ?></li><?php endforeach ?></ul>
  <?php else: ?>
  <p class="muted">예정된 임시 휴무일이 없습니다.</p>
  <?php endif ?>
</section>
<?= $this->endSection() ?>
