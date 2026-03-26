<?php
add_shortcode('c_breadcrumbs', function(){
    if (function_exists('rank_math_the_breadcrumbs')) {
        if (is_home() || is_front_page()) return;
        echo '<div class="mt-half container">';
        rank_math_the_breadcrumbs();
        echo '</div>';
    }
});