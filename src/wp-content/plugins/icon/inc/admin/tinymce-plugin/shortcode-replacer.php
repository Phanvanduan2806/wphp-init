<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Please do not load this file directly!' );
}

function override_mce_options( $initArray )
{
	
    $opts                                 = '*[*]';
    $initArray['valid_elements']          = $opts;
    $initArray['extended_valid_elements'] = $opts;

    return $initArray;

}

//include the css file to style the graphic that replaces the shortcode
add_action( 'admin_init', 'icon_editor_styles' );

/* Gutenberg Compatibility */
add_filter( 'mce_external_plugins', 'icon_tinymce_plugin_core' );
add_action( 'current_screen', 'icon_gutenberg_editor_test' );

function icon_gutenberg_editor_test()
{

    if ( function_exists( 'get_current_screen' ) ) {

        global $current_screen;

        if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {

            add_filter( 'mce_buttons', 'icon_register_buttons', 0 );
            add_action( 'enqueue_block_editor_assets', 'icon_block_editor_styles' );

        }

    }

}

function icon_register_buttons( $buttons )
{

    array_push( $buttons, 'wpicons' );

    return $buttons;

}

//include the tinymce javascript plugin
function icon_tinymce_plugin_core( $plugin_array )
{

    $plugin_array['wpicons'] = ICON_URL.'/inc/admin/tinymce-plugin/wpicons/editor_plugin.js';

    return $plugin_array;

}

function icon_editor_styles()
{

    add_editor_style( ICON_URL.'/inc/admin/tinymce-plugin/wpicons/custom-editor-style.css' );

}

/**
 * Enqueue block editor style
 */
function icon_block_editor_styles()
{

    wp_enqueue_style( 'icon-editor-styles', ICON_URL.'/inc/admin/tinymce-plugin/wpicons/custom-editor-style.css', false, '1.0', 'all' );

}