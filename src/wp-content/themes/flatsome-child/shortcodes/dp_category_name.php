<?php
function c_category_name()
{
    $category = get_queried_object();
    return do_shortcode('[title text="' . $category->name . '" class="a-title" tag_name="h1"]');
}

add_shortcode('c_category_name', 'c_category_name');