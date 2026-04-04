<?php
require_once 'includes/index.php';
require_once 'shortcodes/index.php';

define("WP_FLATSOME_ASSET_VERSION", time());

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('dp-style-css', get_stylesheet_directory_uri() . '/assets/css/dp-style.css', [], WP_FLATSOME_ASSET_VERSION);
    wp_enqueue_style('dp-devices-css', get_stylesheet_directory_uri() . '/assets/css/dp-devices.css', [], WP_FLATSOME_ASSET_VERSION);
}, 999);

add_action('wp_footer', function () {
    wp_enqueue_script('js-js', get_stylesheet_directory_uri() . '/assets/js/js.js', [], WP_FLATSOME_ASSET_VERSION);
});