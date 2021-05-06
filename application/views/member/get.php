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
				<option value="사원">사원</option>
				<option value="대리">대리</option>
				<option value="과장">과장</option>
				<option value="차장">차장</option>
				<option value="부장">부장</option>
				<option value="이사">이사</option>
			</select>
		</div>
		<div class="form-group">
			<select id="dept" class="form-control" title="부서" data-original-title="부서">
				<option value="플랫폼혁신본부">플랫폼혁신본부</option>
			</select>
		</div>
		<div class="form-group">
			<input type="text" id="team" class="form-control" placeholder="팀" title="팀">
		</div>
		<div class="form-group">
			<input type="text" id="part" class="form-control" placeholder="part" title="part">
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



