<?php
class disable_comments {

    public function __construct() {
        add_action('admin_init', array($this, 'disable_comments_on_all_post_types'));
        add_filter('comments_open', array($this, 'disable_comments_status'), 20, 2);
        add_filter('pings_open', array($this, 'disable_comments_status'), 20, 2);
    }

    public function disable_comments_on_all_post_types() {
        $types = get_post_types();
        foreach ($types as $type) {
            if (post_type_supports($type, 'comments')) {
                remove_post_type_support($type, 'comments');
                remove_post_type_support($type, 'trackbacks');
            }
        }
    }

    public function disable_comments_status() {
        return false;
    }
}

new disable_comments();