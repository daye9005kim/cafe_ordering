<?php
include_once APPPATH . 'views/_common/header.php';
//print_r($data);

?>
<body>
<div class="form-group" style="width: 80%; text-align: right; margin:auto; margin-top: 20px;">
	<button onclick="location.href='/member/logout'" class="btn btn-warning">로그아웃</button>
	<script>
		$(document).ready(function () {
			$('#create').click(function () {
				var name = $('#name').val();
				var time = $('#time').val();
				var comment = $('#comment').val();
				var option = $('#option').val();

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/order/start',
					data: {
						'name': name,
						'time': time,
						'comment': comment,
						'option': option,
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

			$('#edit').click(function () {
				var form = $(this).closest('tr');
				var ordnum = form.find('input[name="ordnum"]').val();
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
						"name" : name,
						"comment" : comment,
						"start" : start,
						"end" : end
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

				var form = '<input type="text" class="form-control" name="'+ type +'" value="' + text + '">';
				if (type === 'time') {
					var time = text.split(' ~ ');
					form = '<input type="text" class="form-control" name="start" value="' + time[0].trim() + '">';
					form += '<input type="text" class="form-control" name="end" value="' + time[1].trim() + '">';
				}
				$(this).html(form);
			});

		});
	</script>
</div>
<h3 style="text-align: center">생성된 주문</h3>
<table class="table table-bordered table-hover"  style="width: 80%; margin: auto; margin-top: 20px;">
	<thead>
	<tr>
		<th>주문번호</th>
		<th>생성자</th>
		<th>코멘트</th>
		<th>유효기간</th>
		<th>출력</th>
		<th>수정</th>
	</tr>
	</thead>
	<tbody>
	<?php
	//$item['ordnum'], $item['member_name'], $item['comment'], $item['regdate'], $item['start'], $item['end']
	if (!empty($data['buyer'])) {
		foreach ($data['buyer'] as $key => $item) {
			?>
				<tr>
					<td><a href="/order?ordnum=<?=$item['ordnum']?>"><?=$item['ordnum']?></a>
					<input type="hidden" name="ordnum" value="<?=$item['ordnum']?>"</td>
					<td class="edit" data-type="name"><?=$item['member_name']?></td>
					<td class="edit" data-type="comment"><?=$item['comment']?></td>
					<td class="edit" data-type="time"><?= $item['start'] . ' ~ ' .  $item['end']?></td>
					<td>
						<a href="/order/prnt?ordnum=<?=$item['ordnum']?>" class="btn btn-primary btn-xs">주문용</a>
						<a href="/order/mprnt?ordnum=<?=$item['ordnum']?>" class="btn btn-info btn-xs">회원별</a>
					</td>
					<td>
						<a id="edit" data-ordnum="<?=$item['ordnum']?>" class="btn btn-warning btn-xs">수정</a>
						<a data-ordnum="<?=$item['ordnum']?>" class="btn btn-danger btn-xs delete">삭제</a>
					</td>
				</tr>
			<?php
		}
	}
	?>
	</tbody>
</table>
<div class="bg-info form-inline" style="width: 80%; margin: auto; margin-top: 20px; padding-bottom: 20px; text-align: center;">
	<div class="text-center"><strong>주문 생성하기</strong></div>
	<div class="form-group">
		<input type="text" id="name" class="form-control" placeholder="예)개발팀" title="구매자 이름">
	</div>
	<div class="form-group">
		<input type="text" id="comment" class="form-control" placeholder="예)페이먼트 파트 주문서" title="코멘트">
	</div>
	<div class="form-group">
		<select id="time" class="form-control" title="유효기간" data-original-title="유효기간">
			<option value="1">1시간</option>
			<option value="2">2시간</option>
			<option value="3">3시간</option>
			<option value="4">4시간</option>
			<option value="5">5시간</option>
		</select>
	</div>
	<div class="form-group">
		<select id="option" class="form-control" title="주문서에 코멘트(옵션) 입력란 추가합니다." data-original-title="주문서에 코멘트(옵션) 입력란 추가합니다.">
			<option value="0">옵션 안 받기</option>
			<option value="1">옵션 받기</option>
		</select>
	</div>

	<div class="form-group">
		<button id="create" class="btn btn-info">생성하기</button>
	</div>
</div>
</body>

