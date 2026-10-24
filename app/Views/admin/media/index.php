<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title">
  <div><h1>사진 보관함</h1><p>올라간 모든 사진 <?= count($items) ?>장 · 약 <?= $totalMb ?>MB. 사진 설명과 출처를 기록합니다. 어디서도 쓰지 않는 사진만 삭제할 수 있습니다.</p></div>
</div>
<nav class="tabs" aria-label="사진 필터">
  <a href="<?= site_url('admin/media') ?>"<?= ! $filter ? ' aria-current="page"' : '' ?>>전체</a>
  <a href="<?= site_url('admin/media?filter=unused') ?>"<?= $filter === 'unused' ? ' aria-current="page"' : '' ?>>안 쓰는 사진</a>
  <a href="<?= site_url('admin/media?filter=noalt') ?>"<?= $filter === 'noalt' ? ' aria-current="page"' : '' ?>>설명 없는 사진</a>
</nav>
<div class="tiles">
  <?php foreach ($items as $m): $used = (int) ($counts[$m['id']] ?? 0); ?>
  <article class="tile" id="m-<?= $m['id'] ?>">
    <img src="<?= media_url($m['path'], 'sm') ?>" alt="<?= esc($m['alt_text'], 'attr') ?>" loading="lazy">
    <form class="tile-body" action="<?= site_url("admin/media/{$m['id']}") ?>" method="post">
      <?= csrf_field() ?>
      <div class="muted">
        <?= $m['width'] ?>×<?= $m['height'] ?> · <?= round($m['file_size'] / 1024) ?>KB
        <?= $used ? "<span class=\"badge badge--ok\">{$used}곳에서 사용</span>" : '<span class="badge badge--off">사용 안 함</span>' ?>
        <?= $m['is_demo'] ? '<span class="badge">데모</span>' : '' ?>
      </div>
      <label class="sr-only" for="alt-<?= $m['id'] ?>">사진 설명</label>
      <input type="text" id="alt-<?= $m['id'] ?>" name="alt_text" maxlength="200" value="<?= esc($m['alt_text'], 'attr') ?>" placeholder="사진 설명 (대체 텍스트)">
      <details>
        <summary class="muted">출처·라이선스</summary>
        <div class="stack">
          <input type="text" name="source" maxlength="50" value="<?= esc($m['source'], 'attr') ?>" placeholder="출처 (예: 직접 촬영, Unsplash)" aria-label="출처">
          <input type="url" name="source_url" maxlength="500" value="<?= esc($m['source_url'], 'attr') ?>" placeholder="출처 주소" aria-label="출처 주소">
          <input type="text" name="author" maxlength="100" value="<?= esc($m['author'], 'attr') ?>" placeholder="촬영자" aria-label="촬영자">
          <input type="text" name="license" maxlength="100" value="<?= esc($m['license'], 'attr') ?>" placeholder="라이선스" aria-label="라이선스">
          <label class="check"><input type="checkbox" name="is_ai_generated" value="1"<?= $m['is_ai_generated'] ? ' checked' : '' ?>> AI 생성 이미지</label>
        </div>
      </details>
      <button class="btn btn--line btn--sm" type="submit">저장</button>
    </form>
    <div class="tile-foot">
      <a class="btn btn--line btn--sm" href="<?= media_url($m['path'], 'lg') ?>" target="_blank" rel="noopener">원본 크기 ↗</a>
      <form class="inline" action="<?= site_url("admin/media/{$m['id']}/delete") ?>" method="post" data-confirm="이 사진 파일을 완전히 삭제할까요?">
        <?= csrf_field() ?><button class="btn btn--danger btn--sm" type="submit"<?= $used ? ' disabled title="사용 중인 사진은 삭제할 수 없습니다"' : '' ?>>삭제</button>
      </form>
    </div>
  </article>
  <?php endforeach ?>
</div>
<?php if (! $items): ?><div class="card muted">사진이 없습니다.</div><?php endif ?>
<?= $this->endSection() ?>
