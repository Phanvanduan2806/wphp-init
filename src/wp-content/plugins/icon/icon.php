<?php
/*
Plugin Name: Icon
Plugin URI: http://ghozylab.com/plugins/
Description: Icon - Display icon anywhere you like. You can quickly customize any icons to look exactly the way you want them to look.
Author: GhozyLab, Inc.
Text Domain: icon
Domain Path: /languages
Version: 1.0.0.10
Author URI: https://ghozylab.com/
 */

if ( ! defined( 'ABSPATH' ) ) {die( 'Please do not load this file directly!' );}

// Plugin Name
if ( ! defined( 'ICON_PLUGIN_NAME' ) ) {define( 'ICON_PLUGIN_NAME', 'Icon' );}

// Plugin Version
if ( ! defined( 'ICON_VERSION' ) ) {define( 'ICON_VERSION', '1.0.0.10' );}

// Plugin Folder Path
if ( ! defined( 'ICON_PLUGIN_DIR' ) ) {define( 'ICON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );}

// plugin url
if ( ! defined( 'ICON_URL' ) ) {
    $gifeed_plugin_url = substr( plugin_dir_url( __FILE__ ), 0, -1 );
    define( 'ICON_URL', $gifeed_plugin_url );
}

// Filters & Actions
add_filter( 'widget_text', 'do_shortcode', 11 );
add_filter( 'the_excerpt', 'shortcode_unautop' );
add_filter( 'the_excerpt', 'do_shortcode' );
add_action( 'init', 'icon_global_init' );
register_activation_hook( __FILE__, 'icon_welcome' );

/*-------------------------------------------------------------------------------*/
/*   Includes
/*-------------------------------------------------------------------------------*/
function icon_global_init()
{

    //  I18N - LOCALIZATION
    load_plugin_textdomain( 'icon', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );

	//  Includes
    if ( is_admin() ) {

        include_once dirname( __FILE__ ).'/inc/admin/functions/icon-functions.php';
        include_once dirname( __FILE__ ).'/inc/admin/functions/helpers.php';
        require_once dirname( __FILE__ ).'/inc/admin/tinymce-plugin/shortcode-replacer.php';
        include_once dirname( __FILE__ ).'/inc/admin/iconpicker.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-fontello.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-fontawesome.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-icomoon.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-dashicons.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-openiconic.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-material-icons.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-justvector.php';
        require_once dirname( __FILE__ ).'/inc/admin/assets/icons/icon-paymentfont.php';

    } else {

        include_once dirname( __FILE__ ).'/inc/frontend/icon-script.php';
        include_once dirname( __FILE__ ).'/inc/frontend/icon-shortcode.php';

    }

}

/*-------------------------------------------------------------------------------*/
/*   Welcome screen to show HowTo
/*-------------------------------------------------------------------------------*/
function icon_welcome()
{

    set_transient( '_welcome_screen_activation_redirect', true, 30 );

}

add_action( 'admin_init', 'welcome_screen_do_activation_redirect' );