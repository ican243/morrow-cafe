<?php if ($m = session()->getFlashdata('message')): ?>
<div class="flash flash--ok" role="status"><?= esc($m) ?></div>
<?php endif ?>
<?php if ($e = session()->getFlashdata('error')): ?>
<div class="flash flash--err" role="alert"><?= esc($e) ?></div>
<?php endif ?>
<?php if ($errs = session()->getFlashdata('errors')): ?>
<div class="flash flash--err" role="alert">
  <ul><?php foreach ((array) $errs as $err): ?><li><?= esc($err) ?></li><?php endforeach ?></ul>
</div>
<?php endif ?>
