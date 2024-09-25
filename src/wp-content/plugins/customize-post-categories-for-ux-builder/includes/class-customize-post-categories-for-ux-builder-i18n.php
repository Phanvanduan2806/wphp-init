<?php
// @codingStandardsIgnoreLine
class Customize_Post_Categories_For_Ux_Builder_i18n {
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'customize-post-categories-for-ux-builder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
