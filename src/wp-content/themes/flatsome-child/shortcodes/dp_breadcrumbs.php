<?php
add_shortcode('dp_breadcrumbs', function(){

    if (is_home() || is_front_page()) return '';

    if (!function_exists('rank_math_the_breadcrumbs')) return '';

    ob_start();

    echo '<div class="container">';
    rank_math_the_breadcrumbs();
    echo '</div>';

    return ob_get_clean();
});