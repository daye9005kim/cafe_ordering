<?php
include_once APPPATH . 'views/_common/header.php';
include_once APPPATH . 'views/_common/top.php';

$list = '';
$comment = '';
foreach ($data['order'] as $key => $val) {
	$list .= <<<HTML
<tr><td>{$key}</td>
HTML;

	$t_cnt = 0;
	foreach ($val as $k => $i) {
		$list .= <<<HTML
<td>{$i['cnt']}<br>
HTML;
		foreach ($i['comment'] as $cmt) {
			if (!empty($cmt)) {
				$list .= mb_strlen($cmt) > 5 ? '<p><span>' . $cmt . '</span></p>' : '<span>' . $cmt . ' </span>';
			}
		}
		$list .= '</td>';
		$t_cnt += $i['cnt'];
	}
	$list .= '</tr>';
}
?>
<style>
	body {
		font-size: 13px;
	}
	p {
		margin: 0 0 0;
	}
</style>
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

<?php
include_once APPPATH . 'views/_common/footer.php';
