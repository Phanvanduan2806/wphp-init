<?php
/*
 * @wordpress-plugin
 * Plugin Name:       Customize Post Categories for UX Builder (Flatsome Theme)
 * Plugin URI:        https://codecanyon.net/user/Mysterious_Team/portfolio
 * Description:       With this plugin, You can customize your post category page to your style by simply dragging and dropping the interface without code, with require must have UX Builder.
 * Version:           1.3.3
 * Author:            Mysterious_Team
 * Author URI:        https://codecanyon.net/user/Mysterious_Team/portfolio
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       customize-post-categories-for-ux-builder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'POST_CATEGORY_UXBUILDER_FILE', __FILE__ );
define( 'POST_CATEGORY_UXBUILDER_URL', plugin_dir_url( __FILE__ ) );
define( 'POST_CATEGORY_UXBUILDER_VERSION', '1.3.3' );

function activate_customize_post_categories_for_ux_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-customize-post-categories-for-ux-builder-activator.php';
	Customize_Post_Categories_For_Ux_Builder_Activator::activate();
}


function deactivate_customize_post_categories_for_ux_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-customize-post-categories-for-ux-builder-deactivator.php';
	Customize_Post_Categories_For_Ux_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_customize_post_categories_for_ux_builder' );
register_deactivation_hook( __FILE__, 'deactivate_customize_post_categories_for_ux_builder' );

require plugin_dir_path( __FILE__ ) . 'includes/class-customize-post-categories-for-ux-builder.php';
if ( ! function_exists( 'post_category_uxb_get_image_default' ) ) {
	function post_category_uxb_get_image_default() {
		$image = POST_CATEGORY_UXBUILDER_URL . '/admin/images/image-default.png';
		return $image;
	}
}

function run_customize_post_categories_for_ux_builder() {
	$plugin = new Customize_Post_Categories_For_Ux_Builder();
	$plugin->run();
}
run_customize_post_categories_for_ux_builder();
