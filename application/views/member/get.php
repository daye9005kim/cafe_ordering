<?php
include_once APPPATH . 'views/_common/header.php';
include_once APPPATH . 'views/_common/top.php';

?>

	<h3 style="text-align: center">사원 목록</h3>
	<table class="table table-bordered table-hover"  style="width: 80%; margin: auto; margin-top: 20px;">
		<thead>
		<tr>
			<th>이름</th>
			<th>직급</th>
			<th>부서</th>
			<th>팀</th>
			<th>파트</th>
			<th>수정/삭제</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (!empty($data)) {
			foreach ($data as $item) {
				?>
				<tr>
					<td><?=$item['name']?></a>
					<td><?=$item['pos']?></td>
					<td><?=$item['dept']?></td>
					<td><?= $item['team']?></td>
					<td><?= $item['part']?></td>
					<td>
						<a class="glyphicon glyphicon-pencil btn btn-warning btn-xs"></a>
						<a class="glyphicon glyphicon-trash btn btn-danger btn-xs"></a>
					</td>
				</tr>
				<?php
			}
		}
		?>
		</tbody>
	</table>



<?php
include_once APPPATH . 'views/_common/footer.php';



