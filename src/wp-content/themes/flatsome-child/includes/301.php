<?php

add_action('template_redirect', function () {
    $current_url = home_url(add_query_arg(null, null));
    if (!strpos($current_url, 'uxb_iframe')) {
        if (strpos($current_url, 'post_cat_uxbuilder')) {
            wp_redirect('/', 301);
            exit;
        }
        if (strpos($current_url, 'blocks')) {
            wp_redirect('/', 301);
            exit;
        }
    }
});