<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://seomotop.com/
 * @since      1.0.0
 *
 * @package    Wp_Site_Manager
 * @subpackage Wp_Site_Manager/admin
 */

class Wp_Site_Manager_Admin
{
	private $plugin_name;

	private $version;

	private $packages;

	private $installed;

	public function __construct($plugin_name, $version)
	{

		if (!function_exists('wp_create_nonce')) {
			require_once(ABSPATH . 'wp-includes/pluggable.php');
		}

		if (!function_exists('wp_clean_plugins_cache')) {
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// installed
		$this->installed = $this->get_installed();

		// available
		$this->packages = $this->get_packages();
		foreach ($this->packages as &$item) {
			$item['buttons'] = $this->get_buttons($item);
			$item['latest'] = $this->get_largest_version(array_keys($item['versions'] ?? []));
		}
	}

	/* -------------------------------------------------------------------------- */
	/*                                  css / js                                  */
	/* -------------------------------------------------------------------------- */

	public function enqueue_styles()
	{
		wp_enqueue_style('datatables', '//cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css', array(), '2.0.3', 'all');

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-site-manager-admin.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script('datatables', '//cdn.datatables.net/2.0.3/js/dataTables.min.js', array('jquery'), '2.0.3', false);

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-site-manager-admin.js', array('jquery'), $this->version, false);

		wp_localize_script($this->plugin_name, 'wp_site_manager_data', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'plugin_success' => __('Plugin installed successfully.'),
			'theme_success' => __('Theme installed successfully.'),
		]);
	}

	/* -------------------------------------------------------------------------- */
	/*                                 admin menu                                 */
	/* -------------------------------------------------------------------------- */

	public function wp_site_manager_menu()
	{
		add_management_page(
			'WP Site Manager',
			'WP Site Manager',
			'manage_options',
			'wp-site-manager',
			[$this, 'wp_site_manager_options_page']
		);
	}

	/* -------------------------------------------------------------------------- */
	/*                                    utils                                   */
	/* -------------------------------------------------------------------------- */

	public function get_buttons($slug_item)
	{
		$item = false;
		if (is_array($slug_item)) {
			$item = $slug_item;
		} elseif (is_string($slug_item)) {
			foreach ($this->packages as $k => $v) {
				if ($v['slug'] == $slug_item) {
					$item = $v;
					break;
				}
			}
		}

		if (!$item) return '';

		$key = false;
		switch ($item['type']) {
			case 'plugin':
				$key = $item['file'];
				break;
			case 'theme':
				$key = $item['slug'];
				break;
		}
		if (!$key) return '';

		$buttons = '';
		$latest_version = $this->get_largest_version(array_keys($item['versions']));

		// install
		if (!array_key_exists($key, $this->installed)) {
			$buttons .= ' <a class="button" title="Install" href="' . wp_nonce_url(
				add_query_arg(
					[
						'page' => 'wp-site-manager',
						'action' => 'install',
						'slug' => $item['slug'],
						'ver' => $latest_version
					],
					admin_url('tools.php')
				),
				'install',
				'nonce'
			) . '"><span class="dashicons dashicons-arrow-down-alt"></span></a>';
		} else {
			$current_version = $this->installed[$key];
			// plugin activate
			if ($item['type'] == 'plugin') {
				if (is_plugin_active($key)) {
					$buttons .= ' <a class="button button-danger" title="Deactivate" href="' . wp_nonce_url(
						add_query_arg(
							[
								'page' => 'wp-site-manager',
								'action' => 'deactivate',
								'slug' => $item['slug'],
								'ver' => $latest_version
							],
							admin_url('tools.php')
						),
						'deactivate',
						'nonce'
					)  . '"><span class="dashicons dashicons-no-alt"></span></a>';
				} else {
					$buttons .= ' <a class="button" title="Activate" href="' . wp_nonce_url(
						add_query_arg(
							[
								'page' => 'wp-site-manager',
								'action' => 'activate',
								'slug' => $item['slug'],
								'ver' => $latest_version
							],
							admin_url('tools.php')
						),
						'activate',
						'nonce'
					)  . '"><span class="dashicons dashicons-saved"></span></a>';
				}
			} else {
				// theme activate
				$active_theme = wp_get_theme();
				if ($active_theme->get_stylesheet() !== $item['slug']) {
					$buttons .= ' <a class="button" title="Activate" href="' . wp_nonce_url(
						add_query_arg(
							[
								'page' => 'wp-site-manager',
								'action' => 'activate',
								'slug' => $item['slug'],
								'ver' => $latest_version
							],
							admin_url('tools.php')
						),
						'activate',
						'nonce'
					)  . '"><span class="dashicons dashicons-saved"></span></a>';
				}
			}

			// reinstall
			foreach ($item['versions'] as $k => $v) {
				if (version_compare($k, $current_version) == 0) {
					$buttons .= ' <a class="button button-primary" title="Reinstall" href="' . wp_nonce_url(
						add_query_arg(
							[
								'page' => 'wp-site-manager',
								'action' => 'install',
								'slug' => $item['slug'],
								'ver' => $current_version
							],
							admin_url('tools.php')
						),
						'install',
						'nonce'
					) . '"><span class="dashicons dashicons-undo"></span></a>';
					break;
				}
			}

			// update
			if (version_compare($current_version, $latest_version) == -1) {
				$buttons .= ' <a class="button button-success" title="Update" href="' . wp_nonce_url(
					add_query_arg(
						[
							'page' => 'wp-site-manager',
							'action' => 'install',
							'slug' => $item['slug'],
							'ver' => $latest_version
						],
						admin_url('tools.php')
					),
					'install',
					'nonce'
				)  . '"><span class="dashicons dashicons-arrow-up-alt"></span></a>';
			}
		}
		return $buttons;
	}

	public function get_largest_version($versions)
	{
		if (!is_array($versions) || empty($versions)) {
			return false;
		}
		usort($versions, 'version_compare');
		return end($versions);
	}

	public function sanitize_key($key)
	{
		return preg_replace('`[^A-Za-z0-9_-]`', '', $key);
	}

	public function get_download_url($slug, $ver)
	{
		$package  = $this->packages[$slug] ?? [];
		$versions = $package['versions'] ?? [];
		$zipfile  = $versions[$ver] ?? false;
		$zipfile  = $zipfile ? (WP_SITE_MANAGER_SERVER . '/repository/' . $zipfile) : '';
		return $zipfile;
	}

	public function complete_actions($actions)
	{
		return [];
	}

	/* -------------------------------------------------------------------------- */
	/*                              get packages list                             */
	/* -------------------------------------------------------------------------- */

	public function get_installed()
	{
		wp_clean_plugins_cache(true);
		$all_plugins = get_plugins();
		$all_themes = wp_get_themes();
		$installed = [];
		foreach ($all_plugins as $k => $v) {
			$installed[$k] = $v['Version'];
		}
		foreach ($all_themes as $k => $v) {
			$installed[$k] = $v->get('Version');
		}

		return $installed;
	}

	public function get_packages()
	{
		$is_option_page = false;
		if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] == '/wp-admin/tools.php' && isset($_GET['page']) && $_GET['page'] == 'wp-site-manager') {
			$is_option_page = true;
		}

		if (!$is_option_page) {
			$option = get_option('wp_site_manager_fetch_data', []);
			if (!empty($option)) return $option;
		}

		$url = WP_SITE_MANAGER_SERVER . '/?action=list_all';

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // Skip SSL verification
		$response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($http_status == 200) {
			$data = json_decode($response, true);
			if ($data !== null) {
				$options = [];
				foreach ($data as $item) {
					$options[$item['slug']] = $item;
				}
				update_option('wp_site_manager_fetch_data', $options);
				return $data;
			}
		}
		return [];
	}

	public function wp_site_manager_fetch_data()
	{
		wp_send_json_success($this->packages);
	}

	/* -------------------------------------------------------------------------- */
	/*                             actions -> redirect                            */
	/* -------------------------------------------------------------------------- */

	public function wp_loaded()
	{
		if (defined('DOING_AJAX') && DOING_AJAX &&  strpos($_POST['action'], 'wp_site_manager_') === 0) {
			global $wp_filter;
			$wp_filter['admin_init']->remove_all_filters();
		}
	}

	public function admin_init()
	{
		if (isset($_GET['page']) && ($_GET['page'] == 'wp-site-manager')) {
			if (isset($_GET['action'])) {
				switch ($_GET['action']) {
					case 'import':
						$this->do_import_configs($_GET);
						$this->do_redirect();
						break;
				}
			}
		}
	}

	public function do_redirect()
	{
		if (wp_redirect(menu_page_url('wp-site-manager', false))) {
			exit;
		}
	}

	/* -------------------------------------------------------------------------- */
	/*                                ajax actions                                */
	/* -------------------------------------------------------------------------- */


	public function ajax_actions()
	{
		if (!defined('DOING_AJAX') || !DOING_AJAX) {
			wp_die();
		}

		$request = array_merge([], $_POST);
		$request['action'] = str_replace('wp_site_manager_', '', $request['action']);
		switch ($request['action']) {
			case 'install':
				$this->do_install($request);
				$this->installed = $this->get_installed();
				wp_send_json_success([
					'buttons' => $this->get_buttons($request['slug'])
				]);
				break;
			case 'activate':
				$this->do_activate($request);
				wp_send_json_success([
					'buttons' => $this->get_buttons($request['slug'])
				]);
				break;
			case 'deactivate':
				$this->do_deactivate($request);
				wp_send_json_success([
					'buttons' => $this->get_buttons($request['slug'])
				]);
				break;
		}
		wp_die();
	}

	/* -------------------------------------------------------------------------- */
	/*                                   notices                                  */
	/* -------------------------------------------------------------------------- */

	public function admin_notices()
	{
		if (get_transient('wp_site_manager_imported_config')) {
			wp_admin_notice(
				__('LiteSpeed Cache Config imported successfully.'),
				array(
					'additional_classes' => array('updated', 'is-dismissible'),
					'id'                 => 'wp_site_manager_imported_config_message',
				)
			);
			delete_transient('wp_site_manager_imported_config');
		}
	}

	/* -------------------------------------------------------------------------- */
	/*                                 option page                                */
	/* -------------------------------------------------------------------------- */

	public function wp_site_manager_options_page()
	{
		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'install':
					$this->do_install($_GET);
					break;
				case 'activate':
					$this->do_activate($_GET);
					break;
				case 'deactivate':
					$this->do_deactivate($_GET);
					break;
			}
		} else {
			$import_litespeed_url = wp_nonce_url(
				add_query_arg(
					['page' => 'wp-site-manager', 'action' => 'import', 'slug' => 'litespeedcache'],
					admin_url('tools.php')
				),
				'import',
				'nonce'
			);
?>
			<div class="wrap">
				<h2>WP Site Manager</h2>
				<p></p>
				<h3>Install themes / plugins</h3>
				<div id="wp-site-manager-table">
					<img class="loading" src="<?php echo admin_url('images/spinner-2x.gif'); ?>" alt="" /> Loading
				</div>
				<h3>Import configs</h3>
				<a class="button" href="<?php echo $import_litespeed_url; ?>">LiteSpeed Cache</a>
			</div>
<?php
		}
	}

	/* -------------------------------------------------------------------------- */
	/*                                   install                                  */
	/* -------------------------------------------------------------------------- */

	public function do_install($request)
	{
		check_admin_referer('install', 'nonce');

		if (empty($request['slug']) || empty($request['ver'])) {
			return false;
		}

		$item = false;
		foreach ($this->packages as $p) {
			if ($p['slug'] == $request['slug']) {
				$item = $p;
				break;
			}
		}

		if (!$item) {
			return false;
		}

		if ($item['type'] == 'plugin') {
			$this->do_plugin_install($request);
		}

		if ($item['type'] == 'theme') {
			$this->do_theme_install($request);
		}
	}

	public function do_plugin_install($request)
	{
		$slug = $this->sanitize_key(urldecode($request['slug']));
		$ver = urldecode($request['ver']);

		$url = wp_nonce_url(
			add_query_arg(
				[
					'page' => 'wp-site-manager',
					'action' => "install",
					'slug' => $slug,
					'ver' => $ver,
				],
				admin_url('tools.php')
			),
			"install",
			'nonce'
		);

		$method = '';

		if (false === ($creds = request_filesystem_credentials(esc_url_raw($url), $method, false, false, array()))) {
			return true;
		}

		if (!WP_Filesystem($creds)) {
			request_filesystem_credentials(esc_url_raw($url), $method, true, false, array());
			return true;
		}

		$extra         = array();
		$extra['slug'] = $slug;
		$source        = $this->get_download_url($slug, $ver);
		$api           = null;

		$url = add_query_arg(
			array(
				'action' => 'install-plugin',
				'plugin' => urlencode($slug),
			),
			'update.php'
		);

		if (!class_exists('Plugin_Upgrader', false)) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$title     = 'Installing: %s ';
		$skin_args = array(
			'type'   => 'web',
			'title'  => sprintf($title, $this->packages[$slug]['name']),
			'url'    => esc_url_raw($url),
			'nonce'  => 'install-plugin_' . $slug,
			'plugin' => '',
			'api'    => $api,
			'extra'  => $extra,
		);
		unset($title);

		$skin = new Plugin_Installer_Skin($skin_args);

		$upgrader = new Plugin_Upgrader($skin);

		$upgrader->install($source, ['overwrite_package' => true]);

		echo '<p><a href="', esc_url(menu_page_url('wp-site-manager', false)), '" target="_parent">', esc_html(__('Return to WP Site Manager')), '</a></p>';

		return true;
	}

	public function do_theme_install($request)
	{
		$slug = $this->sanitize_key(urldecode($request['slug']));
		$ver = urldecode($request['ver']);

		$url = wp_nonce_url(
			add_query_arg(
				[
					'page' => 'wp-site-manager',
					'action' => "install",
					'slug' => $slug,
					'ver' => $ver,
				],
				admin_url('tools.php')
			),
			"install",
			'nonce'
		);

		$method = '';

		if (false === ($creds = request_filesystem_credentials(esc_url_raw($url), $method, false, false, array()))) {
			return true;
		}

		if (!WP_Filesystem($creds)) {
			request_filesystem_credentials(esc_url_raw($url), $method, true, false, array());
			return true;
		}

		$extra         = array();
		$extra['slug'] = $slug;
		$source        = $this->get_download_url($slug, $ver);
		$api           = null;

		$url = add_query_arg(
			array(
				'action' => 'install-theme',
				'theme' => urlencode($slug),
			),
			'update.php'
		);

		if (!class_exists('Plugin_Upgrader', false)) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$title     = 'Installing: %s ';
		$skin_args = array(
			'type'   => 'web',
			'title'  => sprintf($title, $this->packages[$slug]['name']),
			'url'    => esc_url_raw($url),
			'nonce'  => 'install-theme_' . $slug,
			'plugin' => '',
			'api'    => $api,
			'extra'  => $extra,
		);
		unset($title);

		$skin = new Theme_Installer_Skin($skin_args);

		$upgrader = new Theme_Upgrader($skin);

		$upgrader->install($source, ['overwrite_package' => true]);

		echo '<p><a href="', esc_url(menu_page_url('wp-site-manager', false)), '" target="_parent">', esc_html(__('Return to WP Site Manager')), '</a></p>';

		return true;
	}

	/* -------------------------------------------------------------------------- */
	/*                                  activate                                  */
	/* -------------------------------------------------------------------------- */

	public function do_activate($request)
	{
		check_admin_referer('activate', 'nonce');

		if (empty($request['slug'])) {
			return false;
		}

		$item = false;
		foreach ($this->packages as $p) {
			if ($p['slug'] == $request['slug']) {
				$item = $p;
				break;
			}
		}

		if (!$item) {
			return false;
		}

		if ($item['type'] == 'plugin') {
			$this->do_plugin_activate($item['file']);
		}

		if ($item['type'] == 'theme') {
			$this->do_theme_activate($item['slug']);
		}
	}

	public function do_plugin_activate($plugin)
	{
		if (is_plugin_active($plugin)) {
			return true;
		}

		if (!current_user_can('activate_plugin', $plugin)) {
			return false;
		}

		$is_network_admin = is_network_admin();

		if (is_multisite() && !$is_network_admin && is_network_only_plugin($plugin)) {
			return false;
		}

		$result = activate_plugin($plugin, '', $is_network_admin);
		if (is_wp_error($result)) {
			return false;
		}

		if (!$is_network_admin) {
			$recent = (array) get_option('recently_activated');
			unset($recent[$plugin]);
			update_option('recently_activated', $recent);
		} else {
			$recent = (array) get_site_option('recently_activated');
			unset($recent[$plugin]);
			update_site_option('recently_activated', $recent);
		}



		return true;
	}

	public function do_theme_activate($slug)
	{
		$theme = wp_get_theme($slug);

		if (!$theme->exists() || !$theme->is_allowed()) {
			return false;
		}

		switch_theme($theme->get_stylesheet());
		return true;
	}

	/* -------------------------------------------------------------------------- */
	/*                                 deactivate                                 */
	/* -------------------------------------------------------------------------- */

	public function do_deactivate($request)
	{
		check_admin_referer('deactivate', 'nonce');

		if (empty($request['slug'])) {
			return false;
		}

		$item = false;
		foreach ($this->packages as $p) {
			if ($p['slug'] == $request['slug']) {
				$item = $p;
				break;
			}
		}

		if (!$item) {
			return false;
		}

		// only plugin need deactivate

		$plugin = $item['file'];

		if (!current_user_can('deactivate_plugin', $plugin)) {
			return false;
		}

		$is_network_admin  = is_network_admin();

		if (!$is_network_admin && is_plugin_active_for_network($plugin)) {
			return false;
		}

		deactivate_plugins($plugin, false, $is_network_admin);

		if (!$is_network_admin) {
			update_option('recently_activated', array($plugin => time()) + (array) get_option('recently_activated'));
		} else {
			update_site_option('recently_activated', array($plugin => time()) + (array) get_site_option('recently_activated'));
		}

		return true;
	}

	/* -------------------------------------------------------------------------- */
	/*                               import configs                               */
	/* -------------------------------------------------------------------------- */

	public function do_import_configs($request)
	{
		$action = $request['action'];

		if (empty($request['slug'])) {
			return false;
		}

		$slug2file = [
			'litespeedcache' => 'setuplitespeedcache.data'
		];

		$slug = $this->sanitize_key(urldecode($request['slug']));

		if (!isset($slug2file[$slug])) {
			return false;
		}

		check_admin_referer($action, 'nonce');

		$url = WP_SITE_MANAGER_SERVER . '/configs/' . $slug2file[$slug];
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // Skip SSL verification
		$response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($http_status == 200) {
			switch ($slug) {
				case 'litespeedcache':
					$imported = $this->import_litespeedcache($response);
					set_transient('wp_site_manager_imported_config', $imported, 30);
					break;
			}
		}
	}

	public function import_litespeedcache($data)
	{
		if (class_exists('\LiteSpeed\Import')) {
			$ori_data = array();
			try {
				if (strpos($data, '["_version",') == 0) {
					$data = explode("\n", $data);
					foreach ($data as $v) {
						$v = trim($v);
						if (!$v) {
							continue;
						}
						list($k, $v) = json_decode($v, true);
						$ori_data[$k] = $v;
					}
				} else {
					$ori_data = json_decode(base64_decode($data), true);
				}
			} catch (\Exception $ex) {
				return false;
			}

			if (!$ori_data) {
				return false;
			}

			$conf = new \LiteSpeed\Conf();
			$conf->update_confs($ori_data);

			return true;
		}

		return false;
	}
}
