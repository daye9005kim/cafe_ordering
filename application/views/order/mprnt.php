<?php
include_once APPPATH . 'views/_common/header.php';
?>
	<div class="container" id="orderTable" style="margin-top: 1rem">
		<div class="clearfix">
			<div class="float-start">
				<a class="btn alert-secondary btn-sm">주문번호 <?= $data['ordnum'] ?></a>
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
			<?php foreach ($data['order'] as $key => $val) : ?>
				<tr><td><?= $key ?></td>
				<?php foreach ($val as $k => $i) : ?>
					<td><?= $i['cnt'] ?><br>
						<?php foreach ($i['comment'] as $cmt) {
							if (!empty($cmt)) { ?>
							<span><?= $cmt ?> </span><?= mb_strlen($cmt) > 5 ? '<br>' : '' ?>
							<?php }
						} ?>
					</td>
				<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			<tr class="info">
				<td style="text-align: center">총</td>
				<td colspan="3" style="text-align: center"><?= $data['total'] ?> 개</td>
			</tr>
			</tbody>
		</table>
	</div>

<?php
include_once APPPATH . 'views/_common/footer.php';
