<?php
include_once APPPATH . 'views/_common/header.php';

$option = '';
foreach ($data['menu'] as $key => $val) {
	$option .= <<<HTML
<option value="{$val['product_cd']}">{$val['product_nm']}</option>\n
HTML;
}

?>
	<style>
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
			background: white!important;
		}
		.ui-widget {
			margin : 10%;
		}
	</style>
	<script>
		$( function() {
			$.widget( "custom.combobox", {
				_create: function() {
					this.wrapper = $( "<span>" )
						.addClass( "custom-combobox" )
						.insertAfter( this.element );

					this.element.hide();
					this._createAutocomplete();
					this._createShowAllButton();
				},

				_createAutocomplete: function() {
					var selected = this.element.children( ":selected" ),
						value = selected.val() ? selected.text() : "";

					this.input = $( "<input>" )
						.appendTo( this.wrapper )
						.val( value )
						.attr( "placeholder", "메뉴를 입력해주세요." )
						.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left form-control" )
						.autocomplete({
							delay: 0,
							minLength: 0,
							source: $.proxy( this, "_source" )
						});

					this._on( this.input, {
						autocompleteselect: function( event, ui ) {
							ui.item.option.selected = true;
							this._trigger( "select", event, {
								item: ui.item.option
							});
						},

						autocompletechange: "_removeIfInvalid"
					});
				},

				_createShowAllButton: function() {
					var input = this.input,
						wasOpen = false;

					$( "<a>" )
						.attr( "tabIndex", -1 )
						.attr( "title", "Show All Items" )
						.tooltip()
						.appendTo( this.wrapper )
						.button({
							icons: {
								primary: "ui-icon-triangle-1-s"
							},
							text: false
						})
						.removeClass( "ui-corner-all" )
						.addClass( "custom-combobox-toggle ui-corner-right" )
						.on( "mousedown", function() {
							wasOpen = input.autocomplete( "widget" ).is( ":visible" );
						})
						.on( "click", function() {
							input.trigger( "focus" );

							// Close if already visible
							if ( wasOpen ) {
								return;
							}

							// Pass empty string as value to search for, displaying all results
							input.autocomplete( "search", "" );
						});
				},

				_source: function( request, response ) {
					var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
					response( this.element.children( "option" ).map(function() {
						var text = $( this ).text();
						if ( this.value && ( !request.term || matcher.test(text) ) )
							return {
								label: text,
								value: text,
								option: this
							};
					}) );
				},

				_removeIfInvalid: function( event, ui ) {

					// Selected an item, nothing to do
					if ( ui.item ) {
						return;
					}

					// Search for a match (case-insensitive)
					var value = this.input.val(),
						valueLowerCase = value.toLowerCase(),
						valid = false;
					this.element.children( "option" ).each(function() {
						if ( $( this ).text().toLowerCase() === valueLowerCase ) {
							this.selected = valid = true;
							return false;
						}
					});

					// Found a match, nothing to do
					if ( valid ) {
						return;
					}

					// Remove invalid value
					this.input
						.val( "" )
						.attr( "title", value + " didn't match any item" )
						.tooltip( "open" );
					this.element.val( "" );
					this._delay(function() {
						this.input.tooltip( "close" ).attr( "title", "" );
					}, 2500 );
					this.input.autocomplete( "instance" ).term = "";
				},

				_destroy: function() {
					this.wrapper.remove();
					this.element.show();
				}
			});

			$( "#combobox" ).combobox();
			$( "#toggle" ).on( "click", function() {
				$( "#combobox" ).toggle();
			});
		} );
	</script>
	</head>

	<div class="user_info"></div>
	<div class="ui-widget">
		<select id="combobox">
			<option value>메뉴를 선택하세요.</option>
			<?= $option ?>
		</select>
	</div>
	<button id="toggle">Show underlying select</button>
	</body>

<?php
include_once APPPATH . 'views/_common/footer.php';
