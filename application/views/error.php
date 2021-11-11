<?php
include_once APPPATH . 'views/_common/header.php';

if (!isset($status)) {
    $status = 404;
}

if (!isset($data)) {
    $data = array();
}

//echo $status;
include_once APPPATH . 'views/_common/top.php';
?>

<div class="container" style="margin-top: 20px">
	<div class="alert alert-danger" role="alert">
		<i class="bi bi-exclamation-octagon-fill"></i>
		<?= $data ?>
	</div>
</div>
