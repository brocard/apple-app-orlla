<?php if ($site->errors):?>
<div style="margin: 20px 0; background-color: #c00; color: #fff; padding: 5px">
<?php foreach ($site->errors as $error): ?>
<?=$error['msg']?><br />
<?php endforeach; ?>
</div>
<?php endif; ?>