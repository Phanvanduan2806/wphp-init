<?php
function list_users_with_posts() {
    $users = get_users();
    $user_names = array();

    foreach ($users as $user) {
        $user_posts = new WP_Query(array(
            'author' => $user->ID,
            'post_type' => 'post',
            'posts_per_page' => 1
        ));
        
        if ($user_posts->have_posts()) {
            $user_names[] = esc_html($user->display_name);
        }
    }
    $output = implode(', ', $user_names);

    return $output;
}
add_shortcode('users_with_posts', 'list_users_with_posts');