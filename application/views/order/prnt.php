<?php
include_once APPPATH . 'views/_common/header.php';

$list = '';
foreach ($data['order'] as $key => $val) {
	$list .= <<<HTML
<tr>
<td>{$key}</td>
HTML;

	$t_cnt = 0;
	foreach ($val as $k => $i) {
		$list .= <<<HTML
<td>{$i}</td>
HTML;
		$t_cnt += $i;
	}
	$list .= '</tr>';
}
?>
<body>
<h4 style="text-align: center">주문번호 <?= $data['ordnum'] ?></h4>
<table class="table table-bordered" style="width: 80%; margin: auto; margin-top: 20px;">
	<thead>
	<tr>
		<th>메뉴</th>
		<th>Tall</th>
		<th>Grande</th>
		<th>Venti</th>
	</tr>
	</thead>
	<tbody>
			<?= $list ?>
	<tr class="info">
		<td style="text-align: center">총</td>
		<td colspan="3" style="text-align: center"><?= $data['total'] ?> 개</td>
	</tr>
	</tbody>
</table>
</body>
