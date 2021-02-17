<?php
include_once APPPATH . 'views/_common/header.php';
//print_r($data);
?>
	<script>
		let MEMBERS = JSON.parse('<?= json_encode($data['member'])?>');
		$(function () {
			$("#name").autocomplete({
				source: MEMBERS,
				select: function (event, ui) {
					if (ui.item.value === '') {
						return alert('이름을 입력해주세요.');
					}
					console.log(ui.item);
				},
				focus: function (event, ui) {
					return false;
					// event.preventDefault();
				}
			});
		});

		var loginok = function loginok(name = '') {
			alert(name);
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/member/login_ok',
				data: {
					'name': name
				},
				success: function (request) {
					alert(request.msg);
				},
				error: function (request, status, error) {
					console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
				}
			});
		}

		$(document).ready(function () {
			$(".enter").keypress(function ( event ) {
				if (event.which == 13) {
					let name = $("#name").val();
					if (name === '') {
						return alert('이름을 입력해주세요.');
					}
					console.log(jQuery.inArray(name, MEMBERS));
					return;
					if (!jQuery.inArray(name, MEMBERS)) {
						return alert('제이슨그룹 사원이 아닙니다.');
					}
					loginok(name);
				}

			});

			$("#order").click(function () {
				let name = $("#name").val();
				if (name === '') {
					return alert('이름을 입력해주세요.');
				}
				loginok(name);
			});
		})
	</script>

	<form class="form-inline" name="order" method="post">
		<div class="ui-widget form-group">
			<input id="name" name="name" class="form-control enter" placeholder="이름을 입력해주세요.">
			<button type="button" class="btn btn-primary" id="order">주문하기</button>
		</div>
	</form>


<?php
include_once APPPATH . 'views/_common/footer.php';



