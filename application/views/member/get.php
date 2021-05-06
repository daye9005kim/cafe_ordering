<?php
include_once APPPATH . 'views/_common/header.php';
include_once APPPATH . 'views/_common/top.php';

?>

	<div class="bg-info form-inline" style="width: 80%; margin: auto; margin-top: 20px; padding-bottom: 20px; text-align: center;">
		<div class="text-center"><strong>사원 추가</strong></div>
		<div class="form-group">
			<input type="text" id="name" class="form-control" placeholder="이름" title="사원 이름">
		</div>
		<div class="form-group">
			<select id="pos" class="form-control" title="직급" data-original-title="직급">
				<option value="1">직급</option>
				<option value="2">2시간</option>
				<option value="3">3시간</option>
				<option value="4">4시간</option>
				<option value="5">5시간</option>
			</select>
		</div>
		<div class="form-group">
			<select id="dept" class="form-control" title="부서" data-original-title="부서">
				<option value="1">부서</option>
				<option value="2">2시간</option>
				<option value="3">3시간</option>
				<option value="4">4시간</option>
				<option value="5">5시간</option>
			</select>
		</div>
		<div class="form-group">
			<select id="team" class="form-control" title="팀" data-original-title="팀">
				<option value="0">팀</option>
				<option value="1">옵션 받기</option>
			</select>
		</div>
		<div class="form-group">
			<select id="part" class="form-control" title="파트" data-original-title="파트">
				<option value="0">파트</option>
				<option value="1">옵션 받기</option>
			</select>
		</div>

		<div class="form-group">
			<button id="create" class="btn btn-info glyphicon glyphicon-plus"></button>
		</div>
	</div>

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



