<?php

function get_current_category_description() {
    if (is_category()) {
        $category = get_queried_object();
        $description = category_description($category);
        $formatted_description = wpautop($description);
        return $formatted_description;
    }
    return '';
}

function register_category_description_shortcode() {
    add_shortcode('post_cat_uxb_desc', 'get_current_category_description');
}
add_action('init', 'register_category_description_shortcode');
