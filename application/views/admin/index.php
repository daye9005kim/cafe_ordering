<?php
include_once APPPATH . 'views/_common/header.php';
//print_r($data);

$buyer = '진행중인 주문이 없습니다.';
if (!empty($data['buyer'])) {
	$buyer = sprintf('%s - %s - %s <br> %s ~ %s <br> 생성일 %s', $data['buyer']['ordnum'], $data['buyer']['member_name'], $data['buyer']['comment'], $data['buyer']['start'], $data['buyer']['end'], $data['buyer']['regdate']);
}

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
<h3>주문</h3>
<ul>
<li>
	<?=$buyer?>
</li>
</ul>
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
		<button id="create" class="btn btn-info">주문 생성하기</button>
	</div>
</div>
</body>

