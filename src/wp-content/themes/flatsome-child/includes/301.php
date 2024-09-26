<?php

function handle_custom_redirect_and_noindex()
{
    $current_url = home_url(add_query_arg(null, null));
    if (strpos($current_url, '/post_cat_uxbuilder/')) {
        wp_redirect('/', 301);
        exit;
    }
    if (strpos($current_url, '/blocks/') && !strpos($current_url, 'uxb_iframe')) {
        wp_redirect('/', 301);
        exit;
    }
//    if (strpos($current_url, 'post_cat_uxbuilder') !== false || strpos($current_url, 'blocks') !== false) {
//        echo '<meta name="robots" content="noindex, follow" />';
//    }
}

add_action('template_redirect', 'handle_custom_redirect_and_noindex');
//add_action('wp_head', 'handle_custom_redirect_and_noindex', 1);