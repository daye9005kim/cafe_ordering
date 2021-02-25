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

			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/order/start',
				data: {
					'name': name,
					'time': time,
					'comment': comment
				},
				success: function (request) {
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
<body>
<div class="form-group">
	<button onclick="location.href='/member/logout'" class="btn btn-warning">로그아웃</button>
	<button onclick="location.href='/order'" class="btn btn-default">주문하기</button>
</div>
<h3>생성된 주문</h3>
<table class="table table-bordered">
	<thead>
	<tr>
		<th>주문번호</th>
		<th>생성자</th>
		<th>코멘트</th>
		<th>유효기간</th>
	</tr>
	</thead>
	<tbody>
	<?php
	//$item['ordnum'], $item['member_name'], $item['comment'], $item['regdate'], $item['start'], $item['end']
	if (!empty($data['buyer'])) {
		foreach ($data['buyer'] as $key => $item) {
			?>
				<tr>
					<td><a href="/order/prnt?ordnum=<?=$item['ordnum']?>"><?=$item['ordnum']?></td>
					<td><?=$item['member_name']?></td>
					<td><?=$item['comment']?></td>
					<td><?= $item['start'] . ' ~ ' .  $item['end']?></td>
				</tr>
			<?php
		}
	}
	?>
	</tbody>
</table>
<div class="form-inline">
	<div class="form-group">
		<input type="text" id="name" class="form-control" placeholder="구매자 이름" title="구매자 이름">
	</div>
	<div class="form-group">
		<input type="text" id="comment" class="form-control" placeholder="코멘트" title="코멘트">
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
		<button id="create" class="btn btn-info">생성하기</button>
	</div>
</div>
</body>

