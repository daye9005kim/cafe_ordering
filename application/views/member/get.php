<?php
include_once APPPATH . 'views/_common/header.php';
include_once APPPATH . 'views/_common/top.php';
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
						'name': name,
						'pos': pos,
						'dept': dept,
						'team': team,
						'part': part
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

			$('.delete').on('click', function () {
				var key = $(this).data('name');
				if (key === '' || typeof key === 'undefined') {
					alert('대상이 없습니다.');
					return false;
				}

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/member/delete',
					data: {
						'name': key,
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
	<div class="container" style="width: 80%; margin-top: 20px;">
		<div class="accordion accordion-flush" id="addMember">
			<div class="accordion-item">
				<h2 class="accordion-header ttip" data-bs-toggle="tooltip" data-bs-placement="left" title="사원 추가/검색"
					id="flush-headingOne">
					<button class="btn btn-outline-primary btn-sm collapsed" type="button" data-bs-toggle="collapse"
							data-bs-target="#collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
						<i class="bi bi-person-plus-fill"></i>
					</button>
				</h2>
				<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
					 data-bs-parent="#accordionExample">
					<div class="accordion-body">
						<form name="search" method="get" class="row g-3" action="/member/get">
							<div class="col-auto">
								<input type="text" id="name" name="name" class="form-control ttip"
									   data-bs-toggle="tooltip" data-bs-placement="top"
									   placeholder="이름" title="사원 이름">
							</div>
							<div class="col-auto">
								<select id="pos" name="pos" class="form-select ttip" data-bs-toggle="tooltip"
										data-bs-placement="top" title="직급">
									<option value="">선택안함</option>
									<option value="사원">사원</option>
									<option value="대리">대리</option>
									<option value="과장">과장</option>
									<option value="차장">차장</option>
									<option value="부장">부장</option>
									<option value="이사">이사</option>
								</select>
							</div>
							<div class="col-auto">
								<select id="dept" name="dept" class="form-select ttip" data-bs-toggle="tooltip"
										data-bs-placement="top" title="부서">
									<option value="">선택안함</option>
									<option value="플랫폼혁신본부">플랫폼혁신본부</option>
								</select>
							</div>
							<div class="col-auto">
								<select id="team" name="team" class="form-select ttip" data-bs-toggle="tooltip"
										data-bs-placement="top" title="팀">
									<option value="">선택안함</option>
									<?php foreach ($team as $value) : ?>
										<option value="<?= $value ?>"><?= $value ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-auto">
								<select id="part" name="part" class="form-select ttip" data-bs-toggle="tooltip"
										data-bs-placement="top" title="파트">
									<option value="">선택안함</option>
									<?php foreach ($part as $value) : ?>
										<option value="<?= $value ?>"><?= $value ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-auto">
								<button id="search" class="btn btn-warning ttip" data-bs-toggle="tooltip"
										data-bs-placement="top" title="검색">
									<i class="bi bi-search"></i>
								</button>
							</div>
							<div class="col-auto">
								<a id="insert" class="btn btn-primary ttip" data-bs-toggle="tooltip"
								   data-bs-placement="top" title="추가">
									<i class="bi bi-plus-lg"></i>
								</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div style="margin-top: 10px">
			<p>총 <?= $pagination['total_rows'] ?> 건</p>
		</div>
		<div>
			<table class="table table-bordered table-hover table-sm">
				<thead>
				<tr>
					<th>이름</th>
					<th>직급</th>
					<th>부서</th>
					<th>팀</th>
					<th>파트</th>
					<th>삭제</th>
				</tr>
				</thead>
				<tbody>
				<?php
				if (!empty($data)) {
					foreach ($data as $item) { ?>
						<tr>
							<td><?= $item['name'] ?></a>
							<td><?= $item['pos'] ?></td>
							<td><?= $item['dept'] ?></td>
							<td><?= $item['team'] ?></td>
							<td><?= $item['part'] ?></td>
							<td>
								<!--								<a class="btn btn-sm alert-warning btn-xs"><i class="bi bi-pencil"></i></a>-->
								<a class="btn btn-sm alert-danger btn-xs delete" data-name="<?= $item['name'] ?>"><i
											class="bi bi-trash"></i></a>
							</td>
						</tr>
						<?php
					}
				} ?>
				</tbody>
			</table>
		</div>
		<div>
			<?= $this->pagination->create_links() ?>
		</div>
	</div>


<?php
include_once APPPATH . 'views/_common/footer.php';



