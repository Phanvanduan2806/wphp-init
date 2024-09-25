(function () {

	function isGutenbergActive() {
		return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';
	}

	tinymce.create('tinymce.plugins.wpicons', {

		currentIconData: null,
		init: function (ed, url) {

			var t = this;
			t.url = url;

			if (isGutenbergActive()) {

				ed.addButton('wpicons', {
					id: 'wpicons_gut_shorcode',
					classes: 'wpicons_gut_shorcode_btn',
					text: 'Add Icon',
					title: 'Insert Icon',
					cmd: 'mcewpicons_mce',
					image: url + '/images/icon-picker.png'
				});

			}

			//replace shortcode before editor content set
			ed.on('BeforeSetcontent', function (event) {

				event.content = t._set_attr(event.content, t.url);

			});

			//replace shortcode as its inserted into editor (which uses the exec command)
			ed.on('ExecCommand', function (event) {

				if (event.command === 'mceInsertContent') {

					tinyMCE.activeEditor.setContent(t._set_attr(tinyMCE.activeEditor.getContent(), t.url));
				}

			});

			//replace the image back to shortcode on save
			ed.on('PostProcess', function (event) {

				if (event.get)
					event.content = t._set_shortcode(event.content);

			});

			//Handle Icon before clicked
			ed.on('mousedown', function (event) {

				$ = ed.getWin().parent.jQuery;

				if (event.target.className == 'iconsc') {

					$('body').addClass('no_image_resize');

				} else {

					$('body').removeClass('no_image_resize');

				}

			});

			// Handle Icon on Click
			ed.on('Click', function (event) {

				if (event.target.className == 'iconsc') {
					// Set current icon data to global variable
					var scAttr;

					if (event.target.dataset.attr.indexOf('wpiconProps') != -1) {
						scAttr = JSON.parse(decodeURIComponent(event.target.dataset.attr))
						scAttr = scAttr.wpiconProps.join(' ');
					} else {
						scAttr = event.target.dataset.attr;
					}

					scAttr = wp.shortcode.next('wpicon', '[wpicon ' + scAttr + ']').shortcode.attrs.named;

					t.currentIconData = scAttr;
					// Load picker
					$('#wpicons_gut_shorcode-button').trigger('click');

				}

			});

		},

		_set_attr: function (co, t) {
			return co.replace(/\[wpicon([^\]]*)\]/g, function (a, b) {

				if (b) {
					// Convert to array and trim
					b = b.split(" ").map(function (item) {
						return item.trim();
					});
					b = encodeURIComponent(JSON.stringify({
						wpiconProps: b
					}));
				} else {
					b = '';
				}

				return '<img src="' + t + '/images/icon-picker.png' + '" class="iconsc" data-attr="' + b + '" title="CLICK TO EDIT"/>';
				
			});
		},

		_set_shortcode: function (co) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);

				var result;

				if (n[1].indexOf('wpiconProps') != -1) {
					result = JSON.parse(decodeURIComponent(n[1]))
					result = result.wpiconProps.join(' ');
				} else {
					result = tinymce.DOM.decode(n[1]);
				}
				return n ? result : '';
			};

			return co.replace(/(?:<p[^>]*>)*(<img[^>]+>)(?:<\/p>)*/g, function (a, im) {

				var cls = getAttr(im, 'class');

				if (cls.indexOf('iconsc') != -1)
					return '<p class="mce_icon_openner">[wpicon ' + tinymce.trim(getAttr(im, 'data-attr')) + ']</p>';

				return a;
			});
		},

		getInfo: function () {
			return {
				longname: 'Icon Pro',
				author: 'PT. GHOZY LAB LLC',
				authorurl: 'https://ghozylab.com/',
				infourl: '',
				version: "1.0"
			};
		}
	});

	tinymce.PluginManager.add('wpicons', tinymce.plugins.wpicons);

})();