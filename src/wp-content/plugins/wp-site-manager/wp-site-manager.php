<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://telegram.com/
 * @since             1.0.0
 * @package           Wp_Site_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       WP Site Manager
 * Plugin URI:        https://telegram.com/
 * Description:       Quick install themes and plugins from list.
 * Version:           1.0.4
 * Author:            SEOMOTOP
 * Author URI:        https://seomotop.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-site-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WP_SITE_MANAGER_VERSION', '1.0.4');
define('WP_SITE_MANAGER_SERVER', 'https://wsm.gwdteam.com/updates');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-site-manager-activator.php
 */
function activate_wp_site_manager()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-site-manager-activator.php';
	Wp_Site_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-site-manager-deactivator.php
 */
function deactivate_wp_site_manager()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wp-site-manager-deactivator.php';
	Wp_Site_Manager_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_site_manager');
register_deactivation_hook(__FILE__, 'deactivate_wp_site_manager');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-site-manager.php';

require plugin_dir_path(__FILE__) . 'updater/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	WP_SITE_MANAGER_SERVER . "/?action=get_metadata&slug=wp-site-manager",
	__FILE__,
	'wp-site-manager'
);

add_filter('plugin_action_links', function ($links, $file) {
	if ($file == 'wp-site-manager/wp-site-manager.php') {
		$settings_link = '<a href="' . admin_url('tools.php?page=wp-site-manager') . '">Settings</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}, 10, 2);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_site_manager()
{

	$plugin = new Wp_Site_Manager();
	$plugin->run();
}
run_wp_site_manager();
