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
			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/member/login_ok',
				data: {
					'user': name
				},
				success: function (request) {
					if (request.name === name) {
						window.location.href = "/order";
					} else {
						alert('로그아웃 하십시오.');
					}
				},
				error: function (request, status, error) {
					console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
				}
			});
		}

		$(document).ready(function () {
			$("#name").focus();
			$(".enter").keypress(function ( event ) {
				let name = $("#name").val();
				if (event.which == 13) {
					if (name === '') {
						alert('이름을 입력해주세요.');
						return $("#name").focus();
					}
					if (jQuery.inArray(name, MEMBERS) < 0) {
						alert('제이슨그룹 사원이 아닙니다.');
						return $("#name").focus();
					}
					loginok(name);
				}
			});

			$("#order").click(function () {
				let name = $("#name").val();
				if (name === '') {
					alert('이름을 입력해주세요.');
					return $("#name").focus();
				}
				if (jQuery.inArray(name, MEMBERS) < 0) {
					alert('제이슨그룹 사원이 아닙니다.');
					return $("#name").focus();
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



