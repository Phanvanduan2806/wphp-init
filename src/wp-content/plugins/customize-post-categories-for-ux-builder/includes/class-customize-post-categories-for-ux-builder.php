<?php
class Customize_Post_Categories_For_Ux_Builder {


	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'POST_CATEGORY_UXBUILDER_VERSION' ) ) {
			$this->version = POST_CATEGORY_UXBUILDER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'customize-post-categories-for-ux-builder';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-customize-post-categories-for-ux-builder-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-customize-post-categories-for-ux-builder-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-customize-post-categories-for-ux-builder-function.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-customize-post-categories-for-ux-builder-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-customize-post-categories-for-ux-builder-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-customize-post-categories-for-ux-builder-public.php';

		$this->loader = new Customize_Post_Categories_For_Ux_Builder_Loader();
	}


	private function set_locale() {
		$plugin_i18n = new Customize_Post_Categories_For_Ux_Builder_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new Customize_Post_Categories_For_Ux_Builder_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_admin, 'init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// EXCUTE POST TYPE
		$this->loader->add_action( 'template_include', $plugin_admin, 'template_include', 9999 );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes' );
		$this->loader->add_action( 'save_post_post_cat_uxbuilder', $plugin_admin, 'save_post', 10, 3 );
		$this->loader->add_action( 'post_updated_messages', $plugin_admin, 'post_updated_messages' );
		$this->loader->add_action( 'admin_print_styles-post-new.php', $plugin_admin, 'admin_print_styles_posttype' );
		$this->loader->add_action( 'admin_print_styles-post.php', $plugin_admin, 'admin_print_styles_posttype' );

		$this->loader->add_action( 'admin_print_scripts-post-new.php', $plugin_admin, 'admin_print_scripts_posttype', 9999 );
		$this->loader->add_action( 'admin_print_scripts-post.php', $plugin_admin, 'admin_print_scripts_posttype', 9999 );

		// ADD POST CATEGORY ELEMENT
		$this->loader->add_action( 'ux_builder_setup', $plugin_admin, 'ux_builder_element' );

		// METABOX ADD / EDIT CATEGORY
		$this->loader->add_action( 'category_edit_form_fields', $plugin_admin, 'category_add_edit_form_fields' );
		$this->loader->add_action( 'category_add_form_fields', $plugin_admin, 'category_add_edit_form_fields' );
		$this->loader->add_action( 'edited_category', $plugin_admin, 'save_add_edit_category_fields', 10, 2 );
		$this->loader->add_action( 'create_category', $plugin_admin, 'save_add_edit_category_fields', 10, 2 );
		// METABOX ADD / EDIT TAG
		$this->loader->add_action( 'post_tag_add_form_fields', $plugin_admin, 'tag_add_edit_form_fields' );
		$this->loader->add_action( 'post_tag_edit_form_fields', $plugin_admin, 'tag_add_edit_form_fields' );
		$this->loader->add_action( 'created_post_tag', $plugin_admin, 'save_add_edit_tag_fields', 10, 2 );
		$this->loader->add_action( 'edited_post_tag', $plugin_admin, 'save_add_edit_tag_fields', 10, 2 );
		// AJAX
		$this->loader->add_action( 'wp_ajax_post_cat_uxb_delete', $plugin_admin, 'delete_template' );
		$this->loader->add_action( 'wp_ajax_post_cat_uxb_active', $plugin_admin, 'active_template' );
		$this->loader->add_action( 'wp_ajax_post_cat_uxb_duplicate', $plugin_admin, 'duplicate_template' );
	}


	private function define_public_hooks() {
		$plugin_public = new Customize_Post_Categories_For_Ux_Builder_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'template_redirect', 9999 );
	}


	public function run() {
		 $this->loader->run();
	}


	public function get_plugin_name() {
		 return $this->plugin_name;
	}


	public function get_loader() {
		return $this->loader;
	}


	public function get_version() {
		 return $this->version;
	}
}
