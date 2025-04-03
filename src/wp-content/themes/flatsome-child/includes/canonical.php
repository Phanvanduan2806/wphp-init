<?php

add_filter('rank_math/frontend/canonical', function($canonical) {
    if (is_category() && is_paged()) {
        $category_url = get_category_link(get_queried_object_id());
        return $category_url;
    }
    return $canonical;
});

add_action('wp_head', 'add_custom_canonical_for_search', 1);
function add_custom_canonical_for_search() {
    if (is_search()) {
        $search_query = get_search_query();
        $canonical_url = home_url('/?s=' . urlencode($search_query));
        echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
    }
}

add_filter('rank_math/frontend/title', function($title) {
    if (is_search()) {
        return 'Tìm kiếm - ' . get_bloginfo('name');
    }
    return $title;
});
