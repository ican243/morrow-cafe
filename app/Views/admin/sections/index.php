<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="page-title"><div><h1>브랜드 소개 글</h1><p>HOME·ABOUT 화면의 문단입니다. 칸은 정해져 있고 내용과 사진만 바꿉니다.</p></div></div>
<div class="card table-wrap">
  <table class="list">
    <thead><tr><th>사진</th><th>위치</th><th>현재 제목</th><th>수정일</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($defs as $key => $def): $sec = $sections[$key] ?? null; ?>
    <tr>
      <td><?= ! empty($sec['media_path']) ? '<img class="thumb" src="' . media_url($sec['media_path'], 'sm') . '" alt="">' : '<div class="thumb"></div>' ?></td>
      <td><strong><?= esc($def['label']) ?></strong></td>
      <td><?= $sec ? esc($sec['title']) : '<span class="badge badge--err">비어 있음</span>' ?></td>
      <td class="muted"><?= $sec ? ko_date($sec['updated_at']) : '' ?></td>
      <td class="num"><a class="btn btn--line btn--sm" href="<?= site_url("admin/sections/{$key}/edit") ?>">수정</a></td>
    </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
