<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Please do not load this file directly!' );
}

/*-------------------------------------------------------------------------------*/
/* Enqueue all Scripts and Styles
/*-------------------------------------------------------------------------------*/
function icon_frontend_script()
{

    $is_rtl = ( is_rtl() ? '-rtl' : '' );

    wp_register_style( 'icon-frontend', ICON_URL.'/inc/frontend/assets/css/icon-frontend'.$is_rtl.'.css', false, ICON_VERSION );
    wp_register_style( 'icon-im', ICON_URL.'/inc/global/assets/icons/icomoon/icomoon.css', false, ICON_VERSION );
    wp_register_style( 'icon-ft', ICON_URL.'/inc/global/assets/icons/fontello/css/fontello.css', false, ICON_VERSION );
    wp_register_style( 'icon-di', includes_url( '/css/dashicons.min.css' ), false, ICON_VERSION );
    wp_register_style( 'icon-fa', ICON_URL.'/inc/global/assets/icons/fontawesome/font-awesome.min.css', false, ICON_VERSION );
    wp_register_style( 'icon-oi', ICON_URL.'/inc/global/assets/icons/openiconic/css/open-iconic-bootstrap.min.css', false, ICON_VERSION );
    wp_register_style( 'icon-gmi', 'https://fonts.googleapis.com/icon?family=Material+Icons', false );
    wp_register_style( 'icon-jv', ICON_URL.'/inc/global/assets/icons/justvector/stylesheets/justvector.css', false, ICON_VERSION );
    wp_register_style( 'icon-pf', ICON_URL.'/inc/global/assets/icons/paymentfont/css/paymentfont.min.css', false, ICON_VERSION );

}

add_action( 'wp_enqueue_scripts', 'icon_frontend_script' );