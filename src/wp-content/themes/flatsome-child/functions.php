<?php
require_once 'includes/index.php';
require_once 'shortcodes/index.php';

define("WP_FLATSOME_ASSET_VERSION", time());

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('a-google', 'https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700&display=swap', [], WP_FLATSOME_ASSET_VERSION);
    wp_enqueue_style('a-style-css', get_stylesheet_directory_uri() . '/assets/css/a-style.css', [], WP_FLATSOME_ASSET_VERSION);
    wp_enqueue_style('a-responsive-css', get_stylesheet_directory_uri() . '/assets/css/a-responsive.css', [], WP_FLATSOME_ASSET_VERSION);
}, 999);

add_action('wp_footer', function () {
    wp_enqueue_script('a-user-js', get_stylesheet_directory_uri() . '/assets/js/a-user.js', [], WP_FLATSOME_ASSET_VERSION);
});

//add_action('admin_enqueue_scripts', function () {
//    wp_enqueue_script('a-admin-js', get_stylesheet_directory_uri() . '/assets/js/a-admin.js', [], WP_FLATSOME_ASSET_VERSION);
//});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script('a-admin-js', get_stylesheet_directory_uri() . '/assets/js/a-admin.js', [], WP_FLATSOME_ASSET_VERSION);
});
