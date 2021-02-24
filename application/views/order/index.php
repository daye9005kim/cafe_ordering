<?php
include_once APPPATH . 'views/_common/header.php';
$option = '';
foreach ($data['menu'] as $key => $val) {
	$option .= <<<HTML
<option value="{$val['product_cd']}">{$val['product_nm']}</option>\n
HTML;
}
$order = 0;
if (!empty($data['order'])) {
	$order = 1;
}
?>
	<style>
		.custom-combobox-toggle {
			top: 9%;
		}

		.custom-combobox-input {
			margin: 0;
			padding: 5px 10px;
		}

		.ui-autocomplete {
			max-height: 500px;
			overflow-y: auto;
			/* prevent horizontal scrollbar */
			overflow-x: hidden;
		}

		* html .ui-autocomplete {
			height: 100px;
		}

		input {
			background: white !important;
			width: 300px !important;
		}

		img {
			width: 100px;
			height: 100px;
		}

	</style>
	<script>
		const countDownTimer = function (id, data) {
			var _vDate = new Date(data); // 전달 받은 일자
			var _second = 1000;
			var _minute = _second * 60;
			var _hour = _minute * 60;
			var _day = _hour * 60;
			var _timer;

			function showRemaining() {
				var now = new Date();
				var disDt = _vDate - now;
				if (disDt < 0) {
					clearInterval(_timer);
					document.getElementById(id).textContent = '해당 이벤트가 종료 되었습니다.';
					return;
				}

				var days = Math.floor(disDt / _day);
				var hours = Math.floor((disDt % _day) / _hour);
				var minutes = Math.floor((disDt % _hour) / _minute);
				var seconds = Math.floor((disDt % _minute) / _second);

				document.getElementById(id).textContent = days + '일 ';
				document.getElementById(id).textContent += hours + '시간 ';
				document.getElementById(id).textContent += minutes + '분 ';
				document.getElementById(id).textContent += seconds + '초';
			}

			_timer = setInterval(showRemaining, 1000);
		}

		countDownTimer('sample02', '<?=$data['timer']?>'); // countDownTimer('sample02', '02/24/2021 23:59');

		$(function () {
			$.widget("custom.combobox", {
				_create: function () {
					this.wrapper = $("<span>")
							.addClass("custom-combobox")
							.insertAfter(this.element);

					this.element.hide();
					this._createAutocomplete();
					this._createShowAllButton();
				},

				_createAutocomplete: function () {
					var selected = this.element.children(":selected"),
							value = selected.val() ? selected.text() : "";

					this.input = $("<input id='menu_nm'>")
							.appendTo(this.wrapper)
							.val(value)
							.attr("placeholder", "메뉴를 입력해주세요.")
							.addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left form-control")
							.autocomplete({
								delay: 0,
								minLength: 0,
								source: $.proxy(this, "_source")
							});

					this._on(this.input, {
						autocompleteselect: function (event, ui) {
							ui.item.option.selected = true;
							this._trigger("select", event, {
								item: ui.item.option
							});
							$("#code").val(ui.item.code);
							$.ajax({
								type: 'post',
								dataType: 'json',
								url: '/order/menu',
								data: {
									'code': ui.item.code
								},
								success: function (request) {
									$("#thumbnail").attr("src", request.menu.product_img);
									$("#content").text(request.menu.content);
								},
								error: function (request, status, error) {
									console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
								}
							});


						},

						autocompletechange: "_removeIfInvalid"
					});
				},

				_createShowAllButton: function () {
					var input = this.input,
							wasOpen = false;

					$("<a>")
							.attr("tabIndex", -1)
							.attr("title", "전체 메뉴 보기")
							.text("▼")
							.tooltip()
							.appendTo(this.wrapper)
							.removeClass("ui-corner-all")
							.addClass("custom-combobox-toggle text-right btn btn-default")
							.on("mousedown", function () {
								wasOpen = input.autocomplete("widget").is(":visible");
							})
							.on("click", function () {
								input.trigger("focus");

								// Close if already visible
								if (wasOpen) {
									return;
								}

								// Pass empty string as value to search for, displaying all results
								input.autocomplete("search", "");
							});
				},

				_source: function (request, response) {
					var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
					response(this.element.children("option").map(function () {
						var text = $(this).text();
						var code = $(this).val();
						if (this.value && (!request.term || matcher.test(text))) {
							return {
								label: text,
								value: text,
								code: code,
								option: this
							};
						}
					}));
				},

				_removeIfInvalid: function (event, ui) {

					// Selected an item, nothing to do
					if (ui.item) {
						return;
					}

					// Search for a match (case-insensitive)
					var value = this.input.val(),
							valueLowerCase = value.toLowerCase(),
							valid = false;
					this.element.children("option").each(function () {
						if ($(this).text().toLowerCase() === valueLowerCase) {
							this.selected = valid = true;
							return false;
						}
					});

					// Found a match, nothing to do
					if (valid) {
						return $("#code").val("");
					}

					// Remove invalid value
					this.input
							.val("")
							.attr("title", value + " 일치하는 메뉴가 없습니다.")
							.tooltip("open");
					this.element.val("");
					this._delay(function () {
						this.input.tooltip("close").attr("title", "");
					}, 2500);
					this.input.autocomplete("instance").term = "";
				},

				_destroy: function () {
					this.wrapper.remove();
					this.element.show();
				}
			});

			$("#combobox").combobox();

		});

		$(document).ready(function () {
			var ordnum = '<?= $data['buyer']['ordnum'] ?>';
			$("#menu_nm").val('<?=isset($data['order']['product_nm']) ? $data['order']['product_nm'] : ''?>');
			$("#code").val('<?=isset($data['order']['product_cd']) ? $data['order']['product_cd'] : ''?>');
			$("#size").val('<?=isset($data['order']['product_size']) ? $data['order']['product_size'] : 'tall'?>');
			$("#cnt").val('<?=isset($data['order']['product_cnt']) ? $data['order']['product_cnt'] : '1'?>');
			$("#comment").val('<?=isset($data['order']['comment']) ? $data['order']['comment'] : ''?>');


			$("#logout").click(function () {
				window.location.href = "/member/logout";
			});

			$(".reorder").click(function () {
				alert();
			});

			$("#print").click(function () {
				alert('완료된 주문의 총 합계 목록입니다.');
				window.location.href = "/order/prnt?ordnum=" + ordnum;
			});

			$('#myModal').on('shown.bs.modal', function () {
				var ord_date;
				var style;
				var button;

				$('.modal-body').html('');

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/order/get',
					data: {
						'ordnum': ordnum
					},
					success: function (request) {
						var list = [];
						var table = $('<table />', {
							"class": "table table-bordered"
						}).prepend($('<thead/>').prepend(
								$('<tr/>').prepend(
										$('<th/>').text('주문일'),
										$('<th/>').text('메뉴'),
										$('<th/>').text('사이즈'),
										$('<th/>').text('수량'),
										$('<th/>').text('담기')
								)
						), $('<tbody/>'));

						for (var i in request.order) {
							ord_date = request.order[i].regdate.split(' ')[0];
							style = '';
							button = $('<td/>').prepend($('<button />', {
								"class": "btn btn-success btn-xs",
								"data-code": request.order[i].product_cd,
								"data-name": request.order[i].product_nm,
								"data-size": request.order[i].product_size,
								"data-cnt": request.order[i].product_cnt,
								"data-dismiss": "modal",
							}).text('입력').click(function () {
								$('#code').val($(this).attr('data-code'));
								$('#menu_nm').val($(this).attr('data-name'));
								$('#size').val($(this).attr('data-size'));
								$('#cnt').val($(this).attr('data-cnt'));

								alert('주문이 입력 되었습니다. 주문하기를 다시 눌러주세요.');
							}));

							if (ordnum === request.order[i].ordnum) {
								ord_date = '오늘의 주문';
								style = 'info';
								button = $('<td />').text('');
							}
							list.push(
									$('<tr />', {"class": style}).prepend(
											$('<td />').text(ord_date),
											$('<td />').text(request.order[i].product_nm),
											$('<td />').text(request.order[i].product_size),
											$('<td />').text(request.order[i].product_cnt + '개'),
											button)
							);
						}
						$('.modal-body').prepend(table.prepend(list));
					},
					error: function (request, status, error) {
						$('#guide').append(
								$('<div />').text('주문서 불러오기 실패입니다.')
						);
						console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
					}
				});

			});

			$("#order").click(function () {
				var menu_code = $("#code").val();
				var menu_nm = $("#menu_nm").val();
				var size = $("#size").val();
				var cnt = $("#cnt").val();
				var comment = $("#comment").val();

				if (menu_nm === '' && menu_code === '') {
					return alert('메뉴를 입력해 주세요.');
				}

				if (size === '') {
					return alert('사이즈를 입력해 주세요.');
				}

				if (cnt === '') {
					return alert('수량을 입력해 주세요.');
				}

				var str = '주문 하시겠습니까? \n' + menu_nm + ' / ' + size + ' / ' + cnt + '개'
				if (!confirm(str)) {
					return 0;
				}

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/order/set',
					data: {
						'menu_code': menu_code,
						'menu_nm': menu_nm,
						'size': size,
						'cnt': cnt,
						'comment': comment,
						'ordnum': ordnum
					},
					success: function (request) {
						$('#myorder').trigger('click');

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
	<div class="user_info form-inline">
		<div class="form-group" style="margin-left: 60%">
			<span><?= $data['user']['name'] . ' ' . $data['user']['pos'] . '님 환영 합니다.' ?></span>
			<button type="button" class="btn btn-default" id="logout">logout</button>
		</div>
		<div style="text-align: center">
			<h4><?= $data['buyer']['member_name'] . '님 - "' . $data['buyer']['comment'] . '"' ?></h4>
			<h2><span class="label label-success" id="sample02">Timer</span></h2>
		</div>
	</div>
	<br>
	<div class="image" style="text-align: center; margin-bottom: 10px">
		<img src="https://image.istarbucks.co.kr/common/img/main/rewards-logo.png" id="thumbnail">
		<br>
		<span id="content"></span>
	</div>
	<div class="form-inline" style="text-align: center">
		<div class="ui-widget form-group">
			<input type="hidden" id="code">
			<select id="combobox">
				<option value>메뉴를 선택하세요.</option>
				<?= $option ?>
			</select>
		</div>
		<div class="form-group">
			<select id="size" class="form-control" title="사이즈" data-original-title="사이즈">
				<option value="tall">Tall</option>
				<option value="grande">Grande</option>
				<option value="venti">Venti</option>
			</select>
		</div>
		<div class="form-group">
			<select id="cnt" class="form-control" title="수량" data-original-title="수량">
				<option value="1">1개</option>
				<option value="2">2개</option>
				<option value="3">3개</option>
				<option value="4">4개</option>
				<option value="5">5개</option>
			</select>
		</div>
		<!--
		<div class="form-group">
			<input type="text" class="form-control" id="comment" placeholder="품절인 경우 대체 주문할 음료 입력">
		</div>-->
		<div class="form-group">
			<button id="order" class="btn btn-info">주문하기</button>
			<button type="button" id="myorder" class="btn btn-warning" data-toggle="modal" data-target="#myModal" aria-label="List">
				<span class="glyphicon glyphicon-list" aria-hidden="true"></span>
			</button>
			<button id="print" class="btn btn-default" aria-label="Print">
				<span class="glyphicon glyphicon-print" aria-hidden="true"></span>
			</button>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
								aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">내 주문 목록</h4>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<span id="guide">다시 주문하시면 주문이 수정됩니다.</span>
					<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
				</div>
			</div>
		</div>
	</div>
	</body>

<?php
include_once APPPATH . 'views/_common/footer.php';
