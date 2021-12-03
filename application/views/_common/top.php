<?php
$admin = $this->config->item('admin');
$user = $this->session->user;

?>
<body>
<div class="container clearfix" style="margin-top: 20px; margin-bottom: 20px;">
	<div class="float-start">
		<button onclick="history.back()" class="btn btn-sm btn-success ttip" data-bs-toggle="tooltip" data-bs-placement="bottom" title="뒤로가기"><i class="bi bi-arrow-left"></i></button>
	</div>
	<div class="float-end">
		<?php if (!empty($user['name'])) : ?>
		<span style="font-size: small;"><?= $user['name'] . ' ' . $user['pos'] . '님 환영 합니다.' ?></span>
		<?php endif; ?>
		<?php if (in_array($user['name'], $admin['member'])) : ?>
			<button onclick="location.href='/member/get'" class="btn btn-sm btn-success ttip" data-bs-toggle="tooltip" data-bs-placement="bottom" title="사원목록"><i class="bi bi-people"></i></button>
			<button onclick="location.href='/admin/index'" class="btn btn-sm btn-danger ttip" data-bs-toggle="tooltip" data-bs-placement="bottom" title="관리자"><i class="bi bi-gear-wide-connected"></i></button>
		<?php endif; ?>
		<button onclick="location.href='/member/logout'" class="btn btn-sm btn-warning ttip" data-bs-toggle="tooltip" data-bs-placement="bottom" title="로그아웃"><i class="bi bi-door-open-fill"></i></button>
	</div>
</div>

