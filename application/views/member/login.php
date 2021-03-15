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

		let loginok = function loginok(name = '') {
			var ordnum = $("input[name='order_list']:checked").val();

			if ($('.orderList').length > 0 && typeof ordnum == 'undefined') {
				alert('주문서를 선택해주세요.');
				return;
			}
			if (name === '') {
				alert('이름을 입력해주세요.');
				return $("#name").focus();
			}
			if (jQuery.inArray(name, MEMBERS) < 0) {
				alert('제이슨그룹 사원이 아닙니다.');
				return $("#name").focus();
			}

			$.ajax({
				type: 'post',
				dataType: 'json',
				url: '/member/login_ok',
				data: {
					'user': name
				},
				success: function (request) {
					if (request.name === name) {
						window.location.href = "/order?ordnum=" + ordnum;
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
				var name = $("#name").val();
				if (event.which == 13) {
					loginok(name);
				}
			});

			$("#order").click(function () {
				var name = $("#name").val();
				loginok(name);
			});
		})
	</script>
<body>
	<div class="form-inline">
		<div>
			<ul class="list-unstyled">
			<?php
			foreach ($data['order_list'] as $item) {
				if (empty($item['ordnum']))	continue;
				$order_list =   $item['member_name'] . ' - ' . $item['comment'] . ' (' . substr($item['start'], 0, -3) . ' ~ ' . substr($item['end'], 11, 5) . ')';
				?>
					<li>
						<label class="radio-inline">
							<input type="radio" name="order_list" class="orderList" value="<?= $item['ordnum'] ?>"> <?= $order_list ?>
						</label>
					</li>
			<?php
			}
			?>
			</ul>
		</div>
		<div class="ui-widget form-group">
			<input type="text" id="name" name="name" class="form-control enter" placeholder="이름을 입력해주세요.">
			<button type="button" class="btn btn-primary" id="order">로그인</button>
		</div>
	</div>
</body>

<?php
include_once APPPATH . 'views/_common/footer.php';



