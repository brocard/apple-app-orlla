<?php include "header.tpl.php"; ?>
<?php include "top.tpl.php"; ?>

<h3>Error</h3>
<p>
<?=$site->errors[0]['msg']?>
<p>
<a href="javascript: history.go(-1)">&laquo; 返回</a>
</p>

<?php include "bottom.tpl.php"; ?>
<?php include "footer.tpl.php"; ?>