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
						alert(request.name + ' 계정을 로그아웃 하십시오.');
						history.back();
					}
				},
				error: function (request, status, error) {
					console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
				}
			});
		}

		$(document).ready(function () {
			$("#name").focus();

			$(".enter").keypress(function (event) {
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
	<style>
		.colorgraph {
			height: 5px !important;
			opacity: .8 !important;
			border-top: 0;
			background: #c4e17f;
			border-radius: 5px;
			background-image: -webkit-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
			background-image: -moz-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
			background-image: -o-linear-gradient(left, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
			background-image: linear-gradient(to right, #c4e17f, #c4e17f 12.5%, #f7fdca 12.5%, #f7fdca 25%, #fecf71 25%, #fecf71 37.5%, #f0776c 37.5%, #f0776c 50%, #db9dbe 50%, #db9dbe 62.5%, #c49cde 62.5%, #c49cde 75%, #669ae1 75%, #669ae1 87.5%, #62c2e4 87.5%, #62c2e4);
		}
	</style>
	<body>
	<!--		<div class="position-absolute top-50 start-50 translate-middle">-->
	<div class="container" style="max-width: 600px; margin-top: 20px;">
			<div class="mb-3">
				<div style="margin: 10px;">
					<p class="form-label" style="font-size: medium;"><?= $data['str'] ?></p>
					<hr class="colorgraph">
					<ul class="list-group">
						<?php
						$str_checked = count($data['order_list']) === 1 ? 'checked' : '';
						foreach ($data['order_list'] as $key => $item) {
							if (empty($item['ordnum'])) continue;
							$order_list = $item['invite'] . ' - ' . $item['comment'];
							?>
							<li class="list-group-item">
								<div class="form-check">
									<input type="radio" name="order_list" class="form-check-input orderList"
										   id="<?= $item['ordnum'] ?>" <?= $str_checked ?>
										   value="<?= $item['ordnum'] ?>">
									<label class="form-check-label"
										   for="<?= $item['ordnum'] ?>"><?= $order_list ?></label>
								</div>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
			<div style="margin: 10px;">
				<div class="input-group mb-3">
					<input type="text" id="name" name="name" class="form-control enter" placeholder="이름을 입력해주세요.">
					<button type="button" class="btn btn-primary" id="order">로그인</button>
				</div>
			</div>
			<hr class="colorgraph" style="height: 10px">
	</div>
	<!--		</div>-->
	</body>

<?php
include_once APPPATH . 'views/_common/footer.php';



