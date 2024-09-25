jQuery(document).ready(function ($) {

	var icon_list, icon_W = 550,
		icon_H = 510,
		myOptions = {
			hide: true,
			palettes: true
		};

	$('body').delegate('#wpicons_gut_shorcode-button', 'click', function () {

		setTimeout(function () {

			tb_show('<img src="' + icon_picker_opt.icon_icon + '" alt="Icon Picker" class="icon_picker_ttl_ico">Icon Picker<span class="cp_version">v' + icon_picker_opt.icon_version + '</span>', '#TB_inline?height=' + icon_H + '&width=' + icon_W + '&inlineId=iconmarkup');

			$('#TB_window').addClass('TB_icon_window').css('z-index', '999999');
			$('#TB_ajaxContent').addClass('TB_icon_ajaxContent');
			$('.TB_icon_ajaxContent').css('height', 'auto');
			$('select#icon_select_icon_source, select#icon_select_icon_bg_shape').val('none');
			$('select#icon_icon_align').val('alignnone');
			$('#icon_insert_icon').attr('value', 'Insert Icon');
			$('select#icon_icon_size').val('nm');
			$('#TB_ajaxContent').css('overflow', 'visible');
			$('.selector-popup').css('display', 'none');
			$('#icon_list, #is_edit, #icon_sc_id').val('');
			$('#icon_select_icon_source').prop('disabled', false);
			$('.icon_bg_shape_cont').hide();
			$('body').addClass('no_image_resize');

			icon_list = $('#icon_list').fontIconPicker({
				emptyIcon: true,
				iconsPerPage: 25,
				theme: 'fip-bootstrap'
			}).on('change', function () {
				$('#icon_list').attr('value', $(this).val());
			});

			icon_list.destroyPicker();
			icon_list.refreshPicker();

			$('.selector-button').unbind('click');
			$('#icon_set_color, #icon_bg_shape_col').wpColorPicker(myOptions);

			$('#TB_closeWindowButton').replaceWith($('<div class="closetb" id="TB_closeWindowButton"><span class="screen-reader-text">Close</span><span class="tb-close-icon"></span></div>'));

			iconTbReposition();
			// Load icon data into form fields
			fillIconForm();

		}, 300);

	});

	// Close Thickbox
	$('body').delegate('.closetb', 'click', function () {
		tb_remove();
	});

	// Select Icon Library
	$('#icon_select_icon_source').on('change', function () {

		if ($('option:selected', this).attr('data-lib') == 'none') {
			$('.selector-button').unbind('click');
			$('.selected-icon').html('<i class="fip-icon-block"></i>');
			$('#icon_list').val('');
		} else {
			$('.selector-button').bind('click');
			$(this).prop('disabled', true);
			$('.icon_loader').addClass('animate_spin');

			getIcon($(this));
		}

	});

	// Select Background Shape
	$('#icon_select_icon_bg_shape').on('change', function () {
		if ($(this).val() == 'none') {

			$('.icon_bg_shape_cont').fadeOut(100);

		} else {
			$('.icon_bg_shape_cont').fadeIn(100);
		}

	});

	// Get Icon via AJAX
	function getIcon(el) {

		$.ajax({
			url: ajaxurl,
			data: {
				'action': 'icon_get_icons_ajax',
				'library': el.val(),
				'security': el.attr('data-icnnonce'),
			},
			dataType: 'json',
			type: 'POST',
			success: function (response) {

				if (response) {

					var all_icons = [];

					icon_list.destroyPicker();
					icon_list.refreshPicker();

					switch (el.val()) {

						case 'fontawesome':
						case 'fontello':
						case 'dashicons':
						case 'openiconic':
						case 'justvector':
						case 'paymentfont':
						case 'icomoon':

							$.each(response.glyphs, function (i, v) {
								all_icons.push(response.css_prefix_text + v.css);
							});

							break;

						case 'gmaterialicons':

							$.each(response.glyphs, function (i, v) {
								all_icons.push(response.css_prefix_text + v.css);
							});

							icon_list.refreshPicker({
								needTitle: true,
								theme: 'fip-bootstrap'
							});

							break;

						default:

					}

					// Set new List
					icon_list.setIcons(all_icons);

					if ($('#is_edit').val() != 'edit') {
						$('.selector-button').trigger('click');
					} else {
						$('#is_edit').attr('value', '');
					}

					$('#icon_select_icon_source').prop('disabled', false);
					$('.icon_loader').removeClass('animate_spin');

				} else {

					$('#icon_select_icon_source').prop('disabled', false);
					$('.icon_loader').removeClass('animate_spin');

				}

			},
			error: function (errorThrown) {

				$('#icon_select_icon_source').prop('disabled', false);
				$('.icon_loader').removeClass('animate_spin');

			}

		}); // End Grab	

	}

	$('#icon_insert_icon').on('click', function () {

		if (!!$('#icon_list').val()) {

			var sccode, scAttr = [];

			$('.iconval').each(function (i, each) {

				var eachValue = $(each).val(),
					eachAttr = $(each).data('sc-param');

				//icon lib & type correction
				if (eachAttr == 'lib') eachValue = $('option:selected', $(each)).attr('data-lib');
				if (eachValue == 'type') eachValue = eachValue.replace(/^fa |dashicons |material-icons |pf |oi\s/i, '');
				// Assign to array
				scAttr.push(eachAttr + '="' + eachValue + '"');

			});

			// Set shortcode
			sccode = '[wpicon ' + scAttr.join(' ') + ']';

			if ($('#wp-content-editor-container > textarea').is(':visible')) {
				var val = $('#wp-content-editor-container > textarea').val() + sccode;
				$('#wp-content-editor-container > textarea').val(val);
			} else {
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, sccode);
			}

			tb_remove();

		} else {

			alert('Please select icon first!');

		}

	});

	// Reposition Thickbox
	function iconTbReposition() {

		$('.TB_icon_window').css({
			'top': (($(window).height() - icon_H) / 9) + 'px',
			'left': (($(window).width() - icon_W) / 4) + 'px',
			'margin-top': (($(window).height() - icon_H) / 9) + 'px',
			'margin-left': (($(window).width() - icon_W) / 4) + 'px',
			'height': 'auto',
		});

	}

	$(window).resize(function () {

		iconTbReposition();

	});

	function fillIconForm() {

		var editorID = tinymce.activeEditor.id,
			myArray = tinymce.get(editorID).plugins.wpicons.currentIconData,
			defaultOpt = {
				lib: 'fa',
				type: '',
				color: '#1e73be',
				size: 'nm',
				align: 'alignnone',
				shape: 'none',
				shapecol: '#ddd',
				customtext: '',
				id: generateID()
			};

		if (myArray === null) myArray = defaultOpt;

		$('#is_edit').attr('value', 'edit');
		$('#icon_insert_icon').attr('value', 'Update Icon');

		$('.iconval').each(function (index, item) {

			var val, scParam = $(item).data('sc-param');
			// Prevent empty/unset data
			if (!myArray.hasOwnProperty(scParam)) myArray[scParam] = defaultOpt[scParam];

			val = myArray[scParam];

			if ($(item).hasClass('wp-color-picker')) {

				$(item).wpColorPicker('color', val);

			} else if ($(item).is('select')) {

				if ($(item).attr('id') == 'icon_select_icon_source') {
					$(item).val($('option[data-lib="' + val + '"]', this).val());
					$('option[data-lib="' + val + '"]', this).attr('selected', 'selected').trigger('change');
				} else {
					$(item).val(val);
					$('option[value="' + val + '"]', this).attr('selected', 'selected').trigger('change');
				}

			} else {

				if ($(item).hasClass('selected_icon')) {

					if (val == 'fa') {
						prefix = 'fa ';
					} else if (val == 'di') {
						prefix = 'dashicons ';
					} else if (val == 'oi') {
						prefix = 'oi ';
					} else if (val == 'gmi') {
						prefix = 'material-icons ';
					} else if (val == 'pf') {
						prefix = 'pf ';
					} else {
						prefix = '';
					}

					$(item).attr('value', prefix + '' + val).val(prefix + '' + val).trigger('change');
					$('.selected-icon').html('<i class="' + prefix + '' + val + '"></i>');

				} else {

					$(item).attr('value', val).val(val).trigger('change');

				}

			}

		});

	}

	function generateID() {

		var text = "";
		var possible = "abcdefghijklmnopqrstuvwxyz0123456789";

		for (var i = 0; i < 10; i++)
			text += possible.charAt(Math.floor(Math.random() * possible.length));

		return text;

	}

	// Thickbox Close Callback
	var old_tb_remove = window.tb_remove;

	var tb_remove = function () {
		old_tb_remove();
		setTimeout(function () {
			var editorID = tinymce.activeEditor.id;
			// Reset icon data
			tinymce.get(editorID).plugins.wpicons.currentIconData = null;

			$('body').removeClass('no_image_resize');
			$('.mce-resizehandle, .mce-inline-toolbar-grp').hide();
			$('#TB_overlay, #TB_window').css('z-index', '100050');
		}, 500);
	};

});