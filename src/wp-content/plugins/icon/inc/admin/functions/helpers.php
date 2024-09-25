<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Please do not load this file directly!' );
}

/*-------------------------------------------------------------------------------*/
/* Enqueue all Admin Scripts and Styles
/*-------------------------------------------------------------------------------*/
function icon_admin_script( $hook )
{

    $inpost = array( 'post-new.php', 'post.php' );

    if ( ! in_array( $hook, $inpost ) ) {
        return;
    }

    wp_enqueue_style( 'thickbox' );
    wp_enqueue_script( 'thickbox' );

    $is_rtl = ( is_rtl() ? '-rtl' : '' );

    // CSS
    wp_enqueue_style( 'icon-fonticonpicker-css', ICON_URL.'/inc/admin/assets/css/jquery.fonticonpicker'.$is_rtl.'.min.css', false, ICON_VERSION );
    wp_enqueue_style( 'icon-iconpicker', ICON_URL.'/inc/admin/assets/css/iconpicker'.$is_rtl.'.css', false, ICON_VERSION );

    // CSS ( Themes )
    wp_enqueue_style( 'icon-fonticonpicker-bootstrap-theme', ICON_URL.'/inc/admin/assets/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap'.$is_rtl.'.min.css', false, ICON_VERSION );

    // CSS ( Icons )
    wp_enqueue_style( 'icon-fonticonpicker-icomoon', ICON_URL.'/inc/global/assets/icons/icomoon/icomoon.css', false, ICON_VERSION );
    wp_enqueue_style( 'icon-fonticonpicker-fontello', ICON_URL.'/inc/global/assets/icons/fontello/css/fontello.css', false, ICON_VERSION );
    wp_enqueue_style( 'icon-fonticonpicker-openiconic', ICON_URL.'/inc/global/assets/icons/openiconic/css/open-iconic-bootstrap.min.css', false, ICON_VERSION );
    wp_enqueue_style( 'icon-fonticonpicker-dashicons', includes_url( '/css/dashicons.min.css' ), false, ICON_VERSION );
    wp_enqueue_style( 'icon-fonticonpicker-fontawesome', ICON_URL.'/inc/global/assets/icons/fontawesome/font-awesome.min.css', false, ICON_VERSION );
    wp_enqueue_style( 'icon-fonticonpicker-materialicons', 'https://fonts.googleapis.com/icon?family=Material+Icons', false );
    wp_enqueue_style( 'icon-fonticonpicker-justvector', ICON_URL.'/inc/global/assets/icons/justvector/stylesheets/justvector.css', false, ICON_VERSION );
    wp_enqueue_style( 'icon-fonticonpicker-paymentfont', ICON_URL.'/inc/global/assets/icons/paymentfont/css/paymentfont.min.css', false, ICON_VERSION );

    // JS
    wp_enqueue_script( 'icon-fonticonpicker-js', ICON_URL.'/inc/admin/assets/js/jquery.fonticonpicker.min.js', array(), ICON_VERSION, true );
    wp_enqueue_script( 'icon-main-js', ICON_URL.'/inc/admin/assets/js/icon-script.js', array( 'wp-color-picker' ), ICON_VERSION, true );
    // ::admin/iconpicker.php
    add_action( 'media_buttons', 'icon_icon_picker_button' );
    wp_enqueue_style( 'wp-color-picker' );

    $data = array(
        'icon_icon'    => ICON_URL.'/inc/admin/assets/img/icon-picker.png',
        'icon_version' => ICON_VERSION,
    );

    wp_localize_script( 'icon-main-js', 'icon_picker_opt', $data );

}

add_action( 'admin_enqueue_scripts', 'icon_admin_script' );