<?php
include_once APPPATH . 'views/_common/header.php';
include_once APPPATH . 'views/_common/top.php';
$option = '';
$prdct_img = array();
foreach ($data['menu'] as $key => $val) {
	$option .= <<<HTML
<option value="{$val['product_cd']}">{$val['product_nm']}</option>\n
HTML;
	$prdct_img[$val['product_nm']] = $val['product_img'];
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
		}

		#menu_nm {
			width: 380px !important;
			padding-right: 40px;
		}

		img {
			width: 150px;
			height: 150px;
		}
		.tiger_img {
			width: 202px !important;
			height: 150px !important;
		}

	</style>
	<script src="https://cdn.socket.io/4.5.0/socket.io.min.js" integrity="sha384-7EyYLQZgWBi67fBtVxw60/OWl1kjsfrPFcaU0pp0nAh+i8FD068QogUvg85Ewy1k" crossorigin="anonymous"></script>
	<script>
		const ordnum = '<?= $data['buyer']['ordnum'] ?>';
		const user = '<?= $user['name'] ?>';
		const data_time = '<?= $data['timer'] ?>';
		const makeLists = function (msg, clss = '') {
			$('.socket-list').prepend($('<li />', {"class": "list-group-item " + clss}).text(msg));
		}
		var socket = io("https://ordersocket.run.goorm.io", {query: {"ordnum": ordnum}});
		socket.emit("welcome", user);
		socket.on("welcome",(msg) => {
			$(".toast-body").text(msg);
			$("#liveToast").show();
		});
		socket.on("order",(msg) => {
			makeLists(msg, 'list-group-item-warning');
		});

		var CAFE = '<?= $data['buyer']['cafe'] ?>';
		var PRDCT_IMG = JSON.parse('<?= json_encode($prdct_img) ?>');

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
					var msg = '주문이 종료 되었습니다.';
					document.getElementById(id).textContent = msg;

					setTimeout(function () {
						makeLists(msg, 'list-group-item-danger');
					}, 1500);
					socket.close();

					return;
				}

				var days = Math.floor(disDt / _day);
				var hours = Math.floor((disDt % _day) / _hour);
				var minutes = Math.floor((disDt % _hour) / _minute);
				var seconds = Math.floor((disDt % _minute) / _second);

				document.getElementById(id).textContent = days > 0 ? days + '일 ' : '';
				document.getElementById(id).textContent += hours > 0 ? hours + '시간 ' : '';
				document.getElementById(id).textContent += minutes > 0 ? minutes + '분 ' : '';
				document.getElementById(id).textContent += seconds > 0 ? seconds + '초' : '';
			}

			_timer = setInterval(showRemaining, 1000);
		}

		countDownTimer('sample02', data_time); // countDownTimer('sample02', '02/24/2021 23:59');
		$(function () {
			$.widget("custom.combobox", {
				_create: function () {
					this.wrapper = $("<span>")
							.attr("style", "position: relative; display: inline-block;")
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
									'code': ui.item.code,
									'cafe': CAFE,
								},
								success: function (request) {
									$("#thumbnail").attr("src", request.menu.product_img);
									var content = request.menu.content;
									if (request.menu.content === '') {
										content = request.menu.product_nm;
									}
									$("#content").text(content);
									if (CAFE === '04') {
										$("#drink_view").attr("href", "https://www.starbucks.co.kr/menu/drink_view.do?product_cd=" + request.menu.product_cd)
									}
									if (CAFE === '05') {
										$("#drink_view").attr("href", "https://www.baristapaulbassett.co.kr/menu/View.pb?cid1=" + request.menu.cate_cd + "&dpid=" + request.menu.product_cd)
									}
									if (CAFE === '06') {
										$("#drink_view").removeAttr("target").attr("onclick", "$('#detail').trigger('click')");
										$("#detail").trigger('click');
									}
									if (CAFE === '07') {
										$("#thumbnail").addClass("tiger_img");
									}
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
							.attr("style", "position: absolute; top: 0; right: 0")
							.text("▼")
							.tooltip()
							.appendTo(this.wrapper)
							.removeClass("ui-corner-all")
							.addClass("custom-combobox-toggle text-right btn btn-outline-success")
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

					$('.ui-menu-item').each(function () {
						var name = $(this).children('div').text();
						var height = "100";
						var width = "100";
						if (CAFE === "07") {
							height = 80;
							width = 100;
						}
						$(this).prepend($('<img />', {"src": PRDCT_IMG[name], "loading": "lazy", "height":height, "width":width}));
					});
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
						// console.log($(this).text().toLowerCase());
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
					alert("일치하는 메뉴가 없습니다.");
					this.input.val("");
					this.element.val("");
					// this._delay(function () {
					// 	this.input.tooltip("close").attr("title", "");
					// }, 2500);
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
			$("#menu_nm").val('<?=isset($data['order']['product_nm']) ? $data['order']['product_nm'] : ''?>');
			$("#code").val('<?=isset($data['order']['product_cd']) ? $data['order']['product_cd'] : ''?>');
			$("#size").val('<?=isset($data['order']['product_size']) ? $data['order']['product_size'] : ''?>');
			$("#cnt").val('<?=isset($data['order']['product_cnt']) ? $data['order']['product_cnt'] : '1'?>');
			$("#comment").val('<?=isset($data['order']['comment']) ? $data['order']['comment'] : ''?>');

			if (CAFE === '01') {
				$("#hot").val('<?=isset($data['order']['hot']) ? $data['order']['hot'] : '0'?>');
				$("#ice").val('<?=isset($data['order']['ice']) ? $data['order']['ice'] : 'Regular'?>');
				$("#sweet").val('<?=isset($data['order']['sweet']) ? $data['order']['sweet'] : '50'?>');
			}

			if ($("#size").val() === null) {
				$("#size option:eq(0)").prop("selected", true);
			}

			$("#logout").click(function () {
				window.location.href = "/member/logout";
			});

			$("#print").click(function () {
				print_popup("/order/orderprint?ordnum=" + ordnum);
			});

			//기본 썸네일
			if (CAFE === '01') {
				$("#drink_view").prop("href", "https://www.gong-cha.co.kr/brand/menu/product.php?c=001");
				$("#thumbnail").prop("src", "/static/img/gongcha.jpg");
			} else if (CAFE === '02'){
				$("#drink_view").prop("href", "https://www.caffe-pascucci.co.kr/product/productList.asp?typeCode=00100010");
				$("#thumbnail").prop("src", "/static/img/pascucci.png");
			} else if (CAFE === '03'){
				$("#drink_view").prop("href", "https://paikdabang.com/menu/menu_drink/");
				$("#thumbnail").prop("src", "/static/img/paiks.png");
			} else if (CAFE === '04') {
				$("#drink_view").prop("href", "https://www.starbucks.co.kr/menu/drink_list.do");
				$("#thumbnail").prop("src", "/static/img/starbucks_logo.png");
			} else if (CAFE === '05') {
				$("#drink_view").prop("href", "https://www.baristapaulbassett.co.kr/menu/List.pb?cid1=A");
				$("#thumbnail").prop("src", "/static/img/paulbassett.jpg");
			} else if (CAFE === '06') {
				$("#drink_view").removeAttr("target").attr("onclick", "$('#detail').trigger('click')");
				$("#thumbnail").prop("src", "/static/img/twosome.png");
			} else if (CAFE === '07') {
				$("#drink_view").prop("href", "http://www.tigersugarkr.com/bbs/board.php?bo_table=all_menu");
				$("#thumbnail").prop("src", "/static/img/tigersugar.jpg");
			}

			//주문한 메뉴가 있으면 썸네일 변경
			if ($("#menu_nm").val().length > 1) {
				$("#thumbnail").prop("src", PRDCT_IMG[$("#menu_nm").val()]);
				if (CAFE === '07') {
					$("#thumbnail").addClass("tiger_img");
				}
			}

			$('#detail').on('click', function () {
				$('#myModalLabel').text('메뉴 상세');
				$('.modal-dialog').attr('style', 'width: 380px; height:800px');
				var p_code = String($('#code').val());
				var url = 'https://mo.twosome.co.kr/mn/menuInfoDetail.do?menuCd=' + p_code;
				if (p_code.length < 1) {
					url = 'https://mo.twosome.co.kr/mn/menuInfoList.do?grtCd=1';
				}
				$('.modal-body').html('<iframe id="output-frame-id" src="' + url + '" style="width: 350px; height:700px"></iframe>');
			});

			$('#orderListClear').on('click', function () {
				$('.socket-list').html('');
			});

			$('#toast_close').on('click', function () {
				$("#liveToast").hide();
			});

			$('#myorder').on('click', function () {
				var ord_date;
				var style;
				var button;

				$('.modal-dialog').addClass('modal-lg');
				$('#myModalLabel').text('내 주문 목록');
				$('.modal-footer').html('<span id="guide">입력 선택 후 다시 주문하시면 주문이 수정됩니다.</span>\n' +
				  '<button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">닫기</button>');

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/order/get',
					data: {
						'ordnum': ordnum,
						'cafe': CAFE
					},
					success: function (request) {
						var list = [];
						var table = $('<table />', {"class": "table table-sm table-bordered font-size"}).prepend(
								$('<thead/>').prepend(
										$('<tr/>').prepend(
												$('<th/>').text('주문일'),
												$('<th/>').text('메뉴'),
												$('<th/>').text('사이즈'),
												$('<th/>').text('수량'),
												$('<th/>').text('코멘트'),
												$('<th/>',{"class": "col-md-1"}).text('재주문')
										)
								), $('<tbody/>'));

						for (var i in request.order) {
							ord_date = request.order[i].regdate.split(' ')[0];
							style = '';
							button = $('<td/>').prepend($('<button />', {
								"class": "btn btn-success btn-sm",
								"style" : "font-size: small;",
								"data-code": request.order[i].product_cd,
								"data-name": request.order[i].product_nm,
								"data-size": request.order[i].product_size,
								"data-cnt": request.order[i].product_cnt,
								"data-hot": request.order[i].hot,
								"data-ice": request.order[i].ice,
								"data-sweet": request.order[i].sweet,
								"data-comment": request.order[i].comment,
								"data-dismiss": "modal",
							}).text('입력').click(function () {
								$('#code').val($(this).data('code'));
								$('#menu_nm').val($(this).data('name'));
								$('#size').val($(this).data('size'));
								$('#cnt').val($(this).data('cnt'));
								$('#comment').val($(this).data('comment'));
								$("#thumbnail").prop("src", PRDCT_IMG[$(this).data('name')]);
								if (request.order[i].cafe === '01') {
									$('#hot').val($(this).data('hot'));
									$('#ice').val($(this).data('ice'));
									$('#sweet').val($(this).data('sweet'));
								}
								if (request.order[i].cafe === '06') {
									$('#hot').val($(this).data('hot'));
								}

								alert('주문이 입력 되었습니다. 주문하기를 다시 눌러주세요.');
								$('#myModal').modal("hide"); //닫기
								$('#order').focus();
								var cnt = 1;
								var blink = setInterval(function(){
									$(".blinkEle").toggle();
									cnt++;
									if (cnt > 10) {
										clearInterval(blink);
									}
								}, 500);
							}));

							if (ordnum === request.order[i].ordnum) {
								ord_date = '오늘의 주문';
								style = 'table-danger';
								button = $('<td />').text('');
							}
							list.push(
									$('<tr />', {"class": style}).prepend(
											$('<td />').text(ord_date),
											$('<td />').text(request.order[i].product_nm),
											$('<td />').text(request.order[i].product_size),
											$('<td />').text(request.order[i].product_cnt + '개'),
											$('<td />').text(request.order[i].comment),
											button)
							);
						}
						$('.modal-body').prepend(table.prepend(list));
						$('.modal-body').append($('<span />'),
						$('<i />', {"class": "bi bi-snow2", "style": "font-size:small"}).text('주문 이력은 최근 10개까지 노출됩니다.'));
					},
					error: function (request, status, error) {
						$('#guide').append(
								$('<div />').text('주문서 불러오기 실패입니다.')
						);
						console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
					}
				});
			});


			$('#myModal').on('hidden.bs.modal', function () {
				$('#myModalLabel').text('');
				$('.modal-body').html('');
				$('.modal-footer').html('');
				$('.modal-dialog').removeAttr("style");
			});

			if ($("#hot").val() === '0') {
				$(".ice").show();
			}

			$("#hot").change(function () {
				if ($("#hot").val() === '1') {
					alert('뜨거운 메뉴가 있는 음료인지 확인하세요. 메뉴 상세 참고');
					$(".ice").hide();
				}
				if ($("#hot").val() === '0') {
					$(".ice").show();
				}
			});

			$("#order").click(function () {
				var menu_code = $("#code").val();
				var menu_nm = $("#menu_nm").val();
				var size = $("#size").val();
				var cnt = $("#cnt").val();
				var comment = $("#comment").val();
				var cstm = '';
				var hot  = '0';
				var sweet  = 0;
				var ice  = 'Regular';

				if (menu_nm === '' && menu_code === '') {
					return alert('메뉴를 입력해 주세요.');
				}
				if (size === '') {
					return alert('사이즈를 입력해 주세요.');
				}
				if (cnt === '') {
					return alert('수량을 입력해 주세요.');
				}

				if (CAFE === '01') {
					hot = $("#hot").val();
					sweet = $("#sweet").val();
					var sweet_txt = $("#sweet option:selected").text();
					var ice_txt = '';
					if (hot === '0') {
						ice = $("#ice").val();
						ice_txt = ':' + $("#ice option:selected").text();
					}
					cstm = (hot === '0' ? 'ICED' : 'HOT') + ice_txt + ' / 당도:' + sweet_txt;
				}

				var str = '주문 하시겠습니까? \n' + menu_nm + ' / ' + size + ' / ' + cnt + '개'

				if (CAFE === '06') {
					hot = $("#hot").val();
					str += ' / ' + (hot === '0' ? 'ICED' : 'HOT');
				}

				if (cstm.length > 0) {
					str += '\n' + cstm;
				}
				if ( typeof comment !== 'undefined' && comment.length > 0) {
					str += '\n' + comment;
				}
				if (!confirm(str)) {
					return 0;
				}

				var data = {
					'user': user,
					'menu_code': menu_code,
					'menu_nm': menu_nm,
					'size': size,
					'cnt': cnt,
					'hot': hot,
					'ice': ice,
					'sweet': sweet,
					'comment': comment,
					'ordnum': ordnum
				};

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/order/set',
					data: data,
					success: function (request) {
						$('#myorder').trigger('click');
						socket.emit("order", data);
					},
					error: function (request, status, error) {
						alert(JSON.parse(request.responseText));
						console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
					}
				});
			});

			if ((new Date(data_time) - new Date()) <= 0) {
				$("input[name='pickup']").prop("disabled", true);
			}

			$("input[name='pickup']").click(function () {
				const data = {
					"ordnum": ordnum,
					"pickup_yn": $(this).val()
				}

				console.log(data);

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: '/pickup/set',
					data: data,
					success: function (request) {
						console.log(request);
						alert(request);
					},
					error: function (request, status, error) {
						alert(JSON.parse(request.responseText));
						console.log('code: ' + request.status + "\n" + 'message: ' + JSON.parse(request.responseText) + "\n" + 'error: ' + error);
					}
				});

			})

		});

	</script>
	<body>
	<div class="container bd-content">
		<div class="bd-callout bd-callout-warning" style="text-align: center;">
			<p><?= '주문 대상 : ' . $data['buyer']['invite'] ?></p>
			<p><?= $data['buyer']['comment'] ?></p>
			<div class="alert alert-success" role="alert">
				<h5><strong><span id="sample02">0시 00분 00초</span></strong></h5>
			</div>
		</div>
	</div>
	<div class="container" style="margin-top: 20px;">
		<br>
		<div class="image" style="text-align: center; margin-bottom: 10px">
			<a id="drink_view" href="#" target="_blank">
			<img src="/static/img/logo.png" id="thumbnail" class="ttip" data-bs-toggle="tooltip" data-bs-placement="right" title="홈페이지로 이동"
			style="margin-bottom: 10px">
			</a><br>
			<span id="content" style="margin: 10px"></span>
		</div>
		<div class="row g-2 justify-content-md-center">
			<div class="col-auto">
				<input type="hidden" id="code">
				<select id="combobox">
					<option value>메뉴를 선택하세요.</option>
					<?= $option ?>
				</select>
			</div>
			<div class="col-auto">
				<select id="size" class="form-select ttip" data-bs-toggle="tooltip" data-bs-placement="top" title="사이즈">
					<?php if ($data['buyer']['cafe'] === GONGCHA) : ?>
						<option value="large">Large</option>
						<option value="jumbo">Jumbo</option>
					<?php elseif ($data['buyer']['cafe'] === PASCUCCI || $data['buyer']['cafe'] === TWOSOME) : ?>
						<option value="regular">Regular</option>
						<option value="large">Large</option>
					<?php elseif ($data['buyer']['cafe'] === PAIKDABANG) : ?>
						<option value="regular">사이즈는 메뉴에서(빽사이즈)</option>
					<?php elseif ($data['buyer']['cafe'] === STARBUCKS) : ?>
						<option value="tall">Tall</option>
						<option value="grande">Grande</option>
						<option value="venti">Venti</option>
					<?php elseif ($data['buyer']['cafe'] === PAULBASSETT) : ?>
						<option value="standard">Standard</option>
						<option value="grand">Grand</option>
					<?php elseif ($data['buyer']['cafe'] === TIGERSUGAR) : ?>
						<option value="regular">Regular</option>
					<?php endif; ?>
				</select>
			</div>
			<?php if ($data['buyer']['cafe'] === GONGCHA) : ?>
			<div class="col-auto">
				<select id="hot" class="form-select ttip" title="HOT/ICED" data-bs-toggle="tooltip" data-bs-placement="top">
					<option value="0">ICED</option>
					<option value="1">HOT</option>
				</select>
			</div>
			<div class="col-auto ice" style="display: none;">
				<select id="ice" class="form-select ttip" title="얼음량" data-bs-toggle="tooltip" data-bs-placement="top">
					<option value="Less">Less</option>
					<option value="Regular">Regular</option>
					<option value="Full">Full</option>
				</select>
			</div>
			<div class="col-auto">
				<select id="sweet" class="form-select ttip" title="당도" data-bs-toggle="tooltip" data-bs-placement="top">
					<option value="0">0%</option>
					<option value="30">30%</option>
					<option value="50">50%</option>
					<option value="70">70%</option>
					<option value="100">100%</option>
				</select>
			</div>
			<?php elseif ($data['buyer']['cafe'] === TWOSOME) : ?>
			<div class="col-auto">
				<select id="hot" class="form-select ttip" title="HOT/ICED" data-bs-toggle="tooltip" data-bs-placement="top">
					<option value="0">ICED</option>
					<option value="1">HOT</option>
				</select>
			</div>
			<?php endif; ?>

			<div class="col-auto" style="display: none;">
				<select id="cnt" class="form-select ttip" title="수량" data-bs-toggle="tooltip" data-bs-placement="top">
					<option value="1">1개</option>
					<!--<option value="2">2개</option>-->
					<!--<option value="3">3개</option>-->
					<!--<option value="4">4개</option>-->
					<!--<option value="5">5개</option>-->
				</select>
			</div>
			<?php if ($data['buyer']['option'] === '1') : ?>
				<div class="col-4">
					<input type="text" class="form-control ttip" data-bs-toggle="tooltip" data-bs-placement="top" id="comment" placeholder="comment" maxlength='50' title="옵션 추가">
				</div>
				<?php endif; ?>
		</div>

		<div class="order_area" style="margin-top: 20px;">
			<div class="row g-2 justify-content-md-center">
				<div class="col-auto">
					<button id="order" class="btn btn-success position-relative"><strong>주문하기</strong>
						<span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle blinkEle"
							  style="display: none;"></span>
					</button>
				</div>
				<div class="col-auto">
					<button type="button" id="myorder" class="btn btn-warning ttip" data-bs-toggle="modal"
							data-bs-target="#myModal" title="최근 주문 메뉴">
						<span><i class="bi bi-cart4"></i></span>
					</button>
				</div>
				<div class="col-auto">
					<button id="print" class="btn btn-secondary ttip" aria-label="Print" data-bs-toggle="tooltip"
							data-bs-placement="top" title="주문서 출력">
						<span><i class="bi bi-printer"></i></span>
					</button>
				</div>
				<div class="col-auto">
					<button class="btn btn-light ttip" id="btn-chat" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" aria-controls="offcanvas" data-bs-placement="top" title="실시간 주문 현황">
						<i class="bi bi-chat-quote"></i>
					</button>
				</div>
				<div class="col-auto">
					<button type="button" id="detail" class="btn btn-warning ttip" data-bs-toggle="modal"
							data-bs-target="#myModal" title="메뉴 상세" hidden>
						<span>상세</span>
					</button>
				</div>
			</div>
			<p class="text-danger" style="margin: 10px 0 10px; text-align: center; font-size: 12px">다시 주문하시면 주문이 수정됩니다.</p>
			<div class="row justify-content-md-center">
				<div class="card text-center" style="width: 25rem;">
					<div class="card-header bold">
						<strong>잠깐! 픽업 지원자 모집합니다.</strong>
					</div>
					<div class="card-body">
						<p class="card-text">
							픽업자는 지원자 중 랜덤으로 선정됩니다.<br>
							픽업을 원하지면 지원해주세요.<br>
							버튼 선택과 동시에 지원됩니다.
						</p>
						<input type="radio" class="btn-check" name="pickup" id="pickup_y" autocomplete="off" value="Y">
						<label class="btn btn-outline-primary" for="pickup_y">지원</label>
						<input type="radio" class="btn-check" name="pickup" id="pickup_n" autocomplete="off" value="N">
						<label class="btn btn-outline-primary" for="pickup_n">미지원</label>
					</div>
				</div>
			</div>
		</div>
	</div>
		<!-- Modal -->
	<div class="modal" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="myModalLabel"></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
	</body>
<!-- offcanvas -->
	<div class="offcanvas offcanvas-start show" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
		<div class="offcanvas-header">
			<button type="button" id="orderListClear" class="btn btn-default ttip" data-bs-placement="right" title="깨끗하게"><i class="bi bi-eraser-fill"></i></button>
			<h5 id="offcanvasLabel">실시간 주문 현황</h5>
			<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body">
			<ul class="list-group list-group-flush socket-list">
				<li class="list-group-item list-group-item-light">자손 카페에 오신것을 환영합니다.</li>
			</ul>
		</div>
	</div>

	<!-- toasts -->
	<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
		<div id="liveToast" class="toast" role="status" aria-live="polite" aria-atomic="true">
			<div class="d-flex">
				<div class="toast-body">
				</div>
				<button type="button" id="toast_close" class="btn-close me-2 m-auto" data-bs-dismiss="toast" data-bs-target="#my-toast" aria-label="Close"></button>
			</div>
		</div>
	</div>


<?php
include_once APPPATH . 'views/_common/footer.php';
//https://www.baristapaulbassett.co.kr/menu/View.pb?cid1=A&cid2=&dpid=PB179549
	//https://mo.twosome.co.kr/mn/menuInfoDetail.do?menuCd=10300044
