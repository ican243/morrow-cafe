<?php
/**
 * 사진 입력칸 (미리보기 + 크롭)
 * @var array|null $row       현재 행 (media_path 있으면 현재 사진 표시)
 * @var float      $ratio     크롭 비율 (0 = 자유)
 * @var bool       $required
 * @var bool       $removable 사진 빼기 허용
 * @var string     $label
 */
$required  ??= false;
$removable ??= false;
$label     ??= '사진';
$shape = $ratio > 1.2 ? 'wide' : ($ratio && $ratio < 0.95 ? 'tall' : '');
$ratioText = match (true) {
    ! $ratio            => '자유 비율',
    abs($ratio - 1) < .01 => '정사각형(1:1)',
    abs($ratio - 16 / 9) < .01 => '가로형(16:9)',
    abs($ratio - 0.8) < .01 => '세로형(4:5)',
    abs($ratio - 1.5) < .01 => '가로형(3:2)',
    default => '',
};
?>
<div class="field full">
  <span class="label"><?= esc($label) ?><?= $required ? '<span class="req">*</span>' : '' ?></span>
  <div class="img-field">
    <div class="img-preview"<?= $shape ? ' data-ratio="' . $shape . '"' : '' ?>>
      <?php if (! empty($row['media_path'])): ?>
      <img src="<?= media_url($row['media_path'], 'sm') ?>" alt="">
      <?php else: ?>
      사진 없음
      <?php endif ?>
    </div>
    <div>
      <label class="file-btn">
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-input data-ratio="<?= $ratio ?: '' ?>"<?= $required && empty($row['media_path']) ? ' required' : '' ?>>
        <span class="btn btn--line"><?= empty($row['media_path']) ? '사진 선택' : '다른 사진으로 바꾸기' ?></span>
      </label>
      <p class="muted" data-image-msg role="alert"></p>
      <p class="muted">JPG·PNG·WEBP, 10MB 이하. 고른 뒤 <?= $ratioText ?>로 자를 수 있고, 저장할 때 자동으로 크기를 줄입니다.</p>
      <div class="field alt-field">
        <label for="image_alt">사진 설명 <span class="hint hint--inline">(시각장애인용 화면 낭독·검색에 쓰임)</span></label>
        <input type="text" id="image_alt" name="image_alt" maxlength="200" placeholder="예: 우드 테이블 위 라떼 한 잔" value="<?= esc(old('image_alt', $row['media_alt'] ?? ''), 'attr') ?>">
        <span class="hint">새 사진을 올릴 때 적용됩니다. 기존 사진 설명은 '사진 보관함'에서 고칠 수 있습니다.</span>
      </div>
      <?php if ($removable && ! empty($row['media_path'])): ?>
      <label class="check"><input type="checkbox" name="remove_image" value="1"> 사진 빼기</label>
      <?php endif ?>
    </div>
  </div>
</div>
