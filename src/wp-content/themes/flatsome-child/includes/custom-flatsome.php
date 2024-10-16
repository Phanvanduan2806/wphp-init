<?php
class custom_flatsome {

    public function __construct() {
        update_option('flatsome_wup_purchase_code', '846b8d75-85b3-4b3e-976c-2e29d386339d');
        update_option('flatsome_wup_supported_until', '01.01.2050');
        update_option('flatsome_wup_buyer', 'Licensed');
        update_option('flatsome_wup_sold_at', time());
        delete_option('flatsome_wup_errors');
        delete_option('flatsome_wupdates');
        
        add_action('admin_head', array($this, 'customCssBackend'));
        
//        define('WP_AUTO_UPDATE_CORE', false);
        add_filter('pre_site_transient_update_core', array($this, 'removeCoreUpdates'));
        add_filter('pre_site_transient_update_plugins', array($this, 'removeCoreUpdates'));
        add_filter('pre_site_transient_update_themes', array($this, 'removeCoreUpdates'));
        
        add_filter('admin_footer_text', array($this, 'removeFooterAdmin'));
        
        add_action('wp_dashboard_setup', array($this, 'customDashboardWidgets'));
        
        add_action('load-index.php', array($this, 'hideWelcomePanel'));
        
        add_action('admin_bar_menu', array($this, 'removeWpLogo'), 999);
    }

    public function customCssBackend() {
        echo '<style>#flatsome-notice {display: none;}</style>';
    }

    public function removeCoreUpdates() {
        global $wp_version;
        return (object) array('last_checked' => time(), 'version_checked' => $wp_version,);
    }

    public function removeFooterAdmin() {
        return 'Phát triển bởi TOR</p>';
    }

    public function customDashboardWidgets() {
        global $wp_meta_boxes;
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
    }

    public function hideWelcomePanel() {
        $user_id = get_current_user_id();
        if (1 == get_user_meta($user_id, 'show_welcome_panel', true))
            update_user_meta($user_id, 'show_welcome_panel', 0);
    }

    public function removeWpLogo($wp_admin_bar) {
        $wp_admin_bar->remove_node('wp-logo');
    }
}

new custom_flatsome();
