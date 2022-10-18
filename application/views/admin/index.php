<?php
include_once APPPATH . 'views/_common/header.php';
include_once APPPATH . 'views/_common/top.php';
$config = $this->config->item('cafe');
?>
<body>
<script>
	$(document).ready(function () {

		var invite_check = function() {
			if ($("#all:checked").length > 0) {
				$(".team").each(function () {
					$(this).prop('disabled', true);
					$(this).prop('checked', false);
				});
			} else {
				$(".team").each(function () {
					$(this).prop('disabled', false);
				});
			}
		}

		invite_check();

		$("#all:checked").change(function() {
			invite_check();
		});

		$(".team+label").on("click", function() {
			var all = $("#all");
			$("input.team:disabled").each(function () {
				$(this).prop('disabled', false);
			});
			if (all.length > 0){
				all.prop('checked', false);
			}
		});

		$('#create').click(function () {
			var invite = [];
			$('input[type=checkbox]:checked').each(function () {
				invite.push($(this).val());
			});
			var end_time = $('#end_time').val();
			var comment = $('#comment').val();
			var option = $('#option').val();
			var cafe = $('#cafe').val();
			var cafeName = $('#cafe option:checked').text();

			if (name === [] || typeof name === "undefined") {
				alert('주문 대상을 입력해 주세요.')
				return false;
			}
			if (end_time === '' || typeof end_time === "undefined") {
				alert('주문 종료 시간을 입력해 주세요.')
				return false;
			}
			if (comment === '' || typeof comment === "undefined") {
				alert('코멘트를 입력해주세요.')
				return false;
			}

			if (!confirm(cafeName + " 카페로 생성하시겠습니까?")) {
				return false;
			}

			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/order/start',
				data: {
					'invite': invite,
					'end_time': end_time,
					'comment': comment,
					'option': option,
					'cafe': cafe,
				},
				success: function (request) {
					alert(request.replace("\\n", "\n"));
					location.reload();
				},
				beforeSend: function () {
					$('.wrap-loading').show();
				},
				complete: function () {
					$('.wrap-loading').hide();
				},
				error: function (request, status, error) {
					alert(JSON.parse(request.responseText));
					console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
				}
			});
		});

		//주문 삭제
		$('.delete').click(function () {
			var ordnum = $(this).data('ordnum');
			if (confirm('주문번호 ' + ordnum + ' 주문을 삭제하시겠습니까?')) {
				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/order/delete',
					data: {
						'ordnum': ordnum,
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
			}
		});

		$('.btn-edit').click(function () {
			var form = $(this).closest('tr');
			var ordnum = $(this).data('ordnum');
			var name = typeof form.find('input[name="name"]').val() === "undefined" ? '' : form.find('input[name="name"]').val();
			var comment = typeof form.find('input[name="comment"]').val() === "undefined" ? '' : form.find('input[name="comment"]').val();
			var start = typeof form.find('input[name="start"]').val() === "undefined" ? '' : form.find('input[name="start"]').val();
			var end = typeof form.find('input[name="end"]').val() === "undefined" ? '' : form.find('input[name="end"]').val();

			if (name + comment + start + end === '') {
				return form.find('.edit').trigger('click');
			}

			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/order/edit',
				data: {
					'ordnum': ordnum,
					"name": name,
					"comment": comment,
					"start": start,
					"end": end
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

		$('.edit').click(function () {
			if ($(this).text() === '') {
				return false;
			}
			var text = $(this).text();
			var type = $(this).data('type');

			var form = '<input type="text" class="form-control form-control-sm" name="' + type + '" value="' + text + '">';
			if (type === 'time') {
				var time = text.split(' ~ ');
				form = '<input type="text" class="form-control form-control-sm" name="start" value="' + time[0].trim() + '">';
				form += '<input type="text" class="form-control form-control-sm" name="end" value="' + time[1].trim() + '">';
			}
			$(this).html(form);
		});

	});
</script>
<div class="container">
	<div class="accordion accordion-flush" id="addMember">
		<div class="accordion-item">
			<h2 class="accordion-header ttip" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-offset="0,0" title="주문서 생성" id="flush-headingOne">
				<button class="btn btn-primary btn-sm collapsed" type="button" data-bs-toggle="collapse"
						data-bs-target="#collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
					<i class="bi bi-bag-plus"></i>
				</button>
			</h2>
			<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
				 data-bs-parent="#accordionExample">
				<div class="accordion-body">
					<div class="row g-1">
						<div class="col">
							<div class="form-check ttip" data-bs-toggle="tooltip" data-bs-placement="top" title="초대 그룹" style="padding-left: 0.1em; font-size: medium">
								<div class="row row-cols-2">
									<div class="col">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" value="all" id="all" name="team" checked>
											<label class="form-check-label" for="all">
												전체
											</label>
										</div>
									</div>
								<?php for ($i = 0; $i < count($data['team']); $i++) : ?>
									<div class="col">
										<div class="form-check form-check-inline">
											<input class="form-check-input team" type="checkbox" value="<?= $data['team'][$i] ?>" id="checked<?= $i ?>" name="team">
											<label class="form-check-label" for="checked<?= $i ?>">
												<?= isset(explode('_', $data['team'][$i])[1]) ? explode('_', $data['team'][$i])[1] : $data['team'][$i] ?>
											</label>
										</div>
									</div>
								<?php endfor ?>
								</div>
							</div>
						</div>
						<div class="col-auto">
							<select id="cafe" class="form-select form-select-sm ttip" data-bs-toggle="tooltip" data-bs-placement="top" title="카페명">
								<?php foreach ($config as $cafe_cd => $item) : ?>
								<option value="<?= $cafe_cd ?>"><?=$item['name']?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col">
							<input type="text" id="comment" class="form-control form-control-sm ttip" data-bs-toggle="tooltip" data-bs-placement="top" placeholder="ex)11시까지 주문해주세요." title="코멘트">
						</div>
						<div class="col-auto">
							<input type="time" id="end_time" class="form-control form-control-sm ttip" data-bs-toggle="tooltip" data-bs-placement="top" title="주문 종료 시간">
						</div>
						<div class="col-auto">
							<select id="option" class="form-select form-select-sm ttip" data-bs-toggle="tooltip" data-bs-placement="top" title="주문서에 코멘트 입력란을 추가합니다.">
								<option value="0">옵션 안 받기</option>
								<option value="1">옵션 받기</option>
							</select>
						</div>
						<div class="col-auto">
							<button id="create" class="btn btn-primary btn-sm ttip" data-bs-toggle="tooltip"
									data-bs-placement="top" title="저장"><i class="bi bi-check-lg"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="text-center wrap-loading" style="display: none;">
		<div class="spinner-border text-success" role="status">
			<span class="visually-hidden">Loading...</span>
		</div>
	</div>
	<div style="margin-top: 10px">
		<p class="font-size">총 <?= $pagination['total_rows'] ?> 건</p>
	</div>
		<table class="table table-bordered table-striped table-hover table-sm font-size">
			<thead>
			<tr>
				<th class="col-md-1">주문번호</th>
				<th class="col-md-2">주문대상</th>
				<th class="col-md-1">카페</th>
				<th>코멘트</th>
				<th>유효기간</th>
				<th>출력</th>
				<th>픽업</th>
				<th>수정/삭제</th>
			</tr>
			</thead>
			<tbody>
			<?php
			if (!empty($data['buyer'])) {
				foreach ($data['buyer'] as $key => $item) {
					?>
					<tr>
						<td><a href="/order?ordnum=<?= $item['ordnum'] ?>" class="link-success"><?= $item['ordnum'] ?></a>
							<input type="hidden" name="ordnum" value="<?= $item['ordnum'] ?>"</td>
						<td class="edit" data-type="name"><?= $item['invite'] ?></td>
						<td data-type="cafe"><?= $config[$item['cafe']]['name'] ?></td>
						<td class="edit" data-type="comment"><?= $item['comment'] ?></td>
						<td class="edit" data-type="time"><?= $item['start'] . ' ~ ' . $item['end'] ?></td>
						<td>
							<a class="btn btn-secondary btn-sm" onclick="print_popup('/order/mprnt?ordnum=<?= $item['ordnum'] ?>')">
								<i class="bi bi-printer"></i>
							</a>
							<a class="btn btn-info btn-sm" onclick="print_popup('/order/orderprint?ordnum=<?= $item['ordnum'] ?>')">
								<i class="bi bi-printer"></i>
							</a>
						</td>
						<td>
							<a class="btn btn-outline-primary btn-sm" onclick="print_popup('/pickup/pick?ordnum=<?= $item['ordnum'] ?>')">
								<i class="bi bi-shop"></i>
							</a>
						</td>
						<td>
							<a data-ordnum="<?= $item['ordnum'] ?>"
							   class="btn btn-warning btn-sm btn-edit"><i class="bi bi-pencil"></i></a>
							<a data-ordnum="<?= $item['ordnum'] ?>" class="btn btn-outline-danger btn-sm delete"><i
										class="bi bi-trash"></i></a>
						</td>
					</tr>
					<?php
				}
			}
			?>
			</tbody>
		</table>
	<div>
		<?= $this->pagination->create_links() ?>
	</div>
</body>

