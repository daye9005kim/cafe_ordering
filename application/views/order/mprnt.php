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
				$str = '<span>' . $cmt . '</span>';
				$list .= mb_strlen($cmt) > 5 ? '<p>' . $str . '</p>' : $str;
			}
		}
		$list .= '</td>';
		$t_cnt += $i['cnt'];
	}
	$list .= '</tr>';
}
?>
	<div class="container" id="orderTable">
		<div class="clearfix">
			<div class="float-start">
				<a href="/order?ordnum=<?= $data['ordnum'] ?>" class="btn alert-secondary btn-sm">주문번호 <?= $data['ordnum'] ?></a>
			</div>
			<div class="float-end">
				<button id="printing" class="btn btn-outline-secondary btn-sm ttip" aria-label="Print" data-bs-toggle="tooltip"
						data-bs-placement="top" title="출력하기">
					<span><i class="bi bi-printer-fill"></i></span>
				</button>
			</div>
		</div>
	<table class="table table-bordered table-striped table-hover table-sm"
			   style="margin-top: 10px; font-size: small;">
			<thead>
			<tr>
				<th>메뉴</th>
				<?php foreach ($data['size'] as $size => $arr) : ?>
					<th><?= $size ?></th>
				<?php endforeach; ?>
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
	</div>

<?php
include_once APPPATH . 'views/_common/footer.php';
