<?php

add_filter('rank_math/frontend/canonical', function($canonical) {
    if (is_category() && is_paged()) {
        $category_url = get_category_link(get_queried_object_id());
        return $category_url;
    }
    return $canonical;
});