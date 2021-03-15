<?php
include_once APPPATH . 'views/_common/header.php';
//print_r($data);

?>
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
				};
		});

	});
</script>
<body>
<div class="form-group" style="width: 80%; text-align: right; margin:auto; margin-top: 20px;">
	<button onclick="location.href='/member/logout'" class="btn btn-warning">로그아웃</button>
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
		<th>삭제</th>
	</tr>
	</thead>
	<tbody>
	<?php
	//$item['ordnum'], $item['member_name'], $item['comment'], $item['regdate'], $item['start'], $item['end']
	if (!empty($data['buyer'])) {
		foreach ($data['buyer'] as $key => $item) {
			?>
				<tr>
					<td><a href="/order?ordnum=<?=$item['ordnum']?>"><?=$item['ordnum']?></a></td>
					<td><?=$item['member_name']?></td>
					<td><?=$item['comment']?></td>
					<td><?= $item['start'] . ' ~ ' .  $item['end']?></td>
					<td><a href="/order/prnt?ordnum=<?=$item['ordnum']?>" class="btn btn-primary btn-xs">주문용</a>
						<a href="/order/mprnt?ordnum=<?=$item['ordnum']?>" class="btn btn-info btn-xs">회원별</a>
					</td>
					<td><a data-ordnum="<?=$item['ordnum']?>" class="btn btn-danger btn-xs delete">삭제</a>
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
		<input type="text" id="name" class="form-control" placeholder="구매자 이름" title="구매자 이름">
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

