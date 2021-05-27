<?php
include_once APPPATH . 'views/_common/header.php';
include_once APPPATH . 'views/_common/top.php';

$team = $this->config->item('dept');

?>
<script>
	$(document).ready(function () {
		$('#insert').click(function () {
			let name = $('input[name=name]').val();
			let pos = $('select[name=pos]').val();
			let dept = $('select[name=dept]').val();
			let team = $('select[name=team]').val();
			let part = $('select[name=part]').val();

			if (name === '' || typeof name === 'undefined') {
				alert('이름이 없습니다.');
				return false;
			}
			if (pos === '' || typeof pos === 'undefined') {
				alert('직급이 없습니다.');
				return false;
			}
			if (dept === '' || typeof dept === 'undefined') {
				alert('부서가 없습니다.');
				return false;
			}
			if (team === '' || typeof team === 'undefined') {
				alert('팀이 없습니다.');
				return false;
			}
			if (part === '' || typeof part === 'undefined') {
				alert('파트가 없습니다.');
				return false;
			}

			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/member/insert',
				data: {
					'name' : name,
					'pos' : pos,
					'dept' : dept,
					'team' : team,
					'part' : part
				},
				success: function (request) {
					alert(request);
					location.reload();
				},
				error: function (request, status, error) {
					alert(JSON.parse(request.responseText));
					console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
				}
			});
		});
	});
</script>
<form name="insert" method="post">
	<div class="bg-info form-inline" style="width: 80%; margin: auto; margin-top: 20px; padding-bottom: 20px; text-align: center;">
		<div class="text-center"><strong>사원 추가</strong></div>
		<div class="form-group">
			<input type="text" id="name" name="name" class="form-control" placeholder="이름" title="사원 이름">
		</div>
		<div class="form-group">
			<select id="pos" name="pos" class="form-control" title="직급" data-original-title="직급">
				<option value="사원">사원</option>
				<option value="대리">대리</option>
				<option value="과장">과장</option>
				<option value="차장">차장</option>
				<option value="부장">부장</option>
				<option value="이사">이사</option>
			</select>
		</div>
		<div class="form-group">
			<select id="dept" name="dept" class="form-control" title="부서" data-original-title="부서">
				<option value="플랫폼혁신본부">플랫폼혁신본부</option>
			</select>
		</div>
		<div class="form-group">
			<select id="team" name="team" class="form-control" title="team" data-original-title="team">
				<option value="R&D센터_개발팀">R&D센터_개발팀</option>
				<option value="R&D센터_데이터플랫폼개발팀">R&D센터_데이터플랫폼개발팀</option>
				<option value="R&D센터_앱개발팀">R&D센터_앱개발팀</option>
				<option value="R&D센터_인프라팀">R&D센터_인프라팀</option>
				<option value="R&D센터_플랫폼개발팀">R&D센터_플랫폼개발팀</option>
				<option value="UX디자인팀">UX디자인팀</option>
				<option value="QA팀">QA팀</option>
				<option value="백엔드기획팀">백엔드기획팀</option>
				<option value="서비스기획팀">서비스기획팀</option>
			</select>
		</div>
		<div class="form-group">
			<select id="part" name="part" class="form-control" title="part" data-original-title="part">
				<option value="페이먼트개발파트">페이먼트개발파트</option>
				<option value="서비스개발파트">서비스개발파트</option>
				<option value="솔루션개발파트">솔루션개발파트</option>
				<option value="데이터인프라파트">데이터인프라파트</option>
				<option value="인프라운영파트">인프라운영파트</option>
				<option value="앱개발파트">앱개발파트</option>
				<option value="플랫폼개발파트">플랫폼개발파트</option>
				<option value="UX디자인파트">UX디자인파트</option>
				<option value="QA파트">QA파트</option>
				<option value="백엔드기획파트">백엔드기획파트</option>
				<option value="프론트기획파트">프론트기획파트</option>
			</select>
		</div>

		<div class="form-group">
			<button id="insert" class="btn btn-info glyphicon glyphicon-plus"></button>
		</div>
	</div>
</form>
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
			foreach ($data as $item) {?>
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
		}?>
		</tbody>
	</table>



<?php
include_once APPPATH . 'views/_common/footer.php';



