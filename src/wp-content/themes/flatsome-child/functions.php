<?php
require_once 'includes/index.php';
require_once 'shortcodes/index.php';

define("WP_FLATSOME_ASSET_VERSION", time());

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('a-google', 'https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700&display=swap', [], WP_FLATSOME_ASSET_VERSION);
    wp_enqueue_style('a-style-css', get_stylesheet_directory_uri() . '/assets/css/dp-style.css', [], WP_FLATSOME_ASSET_VERSION);
    wp_enqueue_style('a-responsive-css', get_stylesheet_directory_uri() . '/assets/css/dp-devices.css', [], WP_FLATSOME_ASSET_VERSION);
}, 999);

add_action('wp_footer', function () {
    wp_enqueue_script('js-js', get_stylesheet_directory_uri() . '/assets/js/js.js', [], WP_FLATSOME_ASSET_VERSION);
});