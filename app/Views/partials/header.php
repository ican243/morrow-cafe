<?php
$nav = [
    'home'     => ['', 'HOME', '홈'],
    'about'    => ['about', 'ABOUT', '브랜드 소개'],
    'menu'     => ['menu', 'MENU', '메뉴'],
    'space'    => ['space', 'SPACE', '공간'],
    'news'     => ['news', 'NEWS', '소식'],
    'location' => ['location', 'LOCATION', '오시는 길'],
];
?>
<header class="site-header" id="siteHeader">
  <div class="wrap">
    <a class="brand" href="<?= site_url('/') ?>" aria-label="<?= esc($s['store_name'] ?? 'MORROW COFFEE', 'attr') ?> 홈">
      <b>MORROW</b><span>COFFEE</span>
    </a>
    <button class="nav-toggle" type="button" aria-controls="gnb" aria-expanded="false">
      <span></span><span></span><span></span><i class="sr-only">메뉴 열기</i>
    </button>
    <nav class="gnb" id="gnb" aria-label="주 메뉴">
      <ul>
        <?php foreach ($nav as $key => [$path, $en, $ko]): ?>
        <li><a href="<?= site_url($path) ?>"<?= $navActive === $key ? ' aria-current="page"' : '' ?>><?= $en ?> <small><?= $ko ?></small></a></li>
        <?php endforeach ?>
      </ul>
      <div class="gnb-extra">
        <?php if (! empty($s['address'])): ?><p><?= esc($s['address']) ?></p><?php endif ?>
        <?php if (! empty($s['phone'])): ?><p><a href="<?= tel_href($s['phone']) ?>"><?= esc($s['phone']) ?></a></p><?php endif ?>
      </div>
    </nav>
  </div>
</header>
