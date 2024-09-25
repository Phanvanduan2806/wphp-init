<?php
class Customize_Post_Categories_For_Ux_Builder_Admin {

	private $plugin_name;
	private $version;
	private $post_type;
	const SLUG = 'categories-uxb-templates';

	public function __construct( $plugin_name, $version ) {
		$this->post_type   = postCategoryUXB()->get_post_type();
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles( $page ) {
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
		if ( 'posts_page_' . self::SLUG === $page ) {
			wp_enqueue_style( $this->plugin_name . '-templates', plugin_dir_url( __FILE__ ) . 'css/templates.css', array(), $this->version, 'all' );
		}
	}

	public function enqueue_scripts( $page ) {
		if ( 'term.php' === $page || 'edit-tags.php' === $page ) {
			wp_enqueue_media();
		}
		if ( 'posts_page_' . self::SLUG === $page ) {
			$data_localize_script         = postCategoryUXB()->get_localize_script();
			$data_localize_script['type'] = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : 'category';
			wp_enqueue_script( $this->plugin_name . '-templates', plugin_dir_url( __FILE__ ) . 'js/templates.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name . '-templates',
				'post_cat_uxb_data',
				$data_localize_script
			);
		}
	}

	public function init() {
		if ( ! defined( 'UX_BUILDER_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			return;
		} else {
			postCategoryUXB()->register_post_type();
			add_ux_builder_post_type( $this->post_type );
		}
	}

	public function admin_notices() {
		$plugin = get_plugin_data( POST_CATEGORY_UXBUILDER_FILE );
		echo sprintf(
			'<div class="notice error is-dismissible"><p><strong>%1$s</strong> requires <strong><a href="%2$s" target="_blank">UX Builder</a></strong> plugin to be installed and activated on your site.</p></div>',
			esc_html( $plugin['Name'] ),
			esc_url( '//1.envato.market/AomJKK' )
		);
	}

	public function admin_menu() {
		add_submenu_page(
			'post-new.php',
			esc_html__( 'Archive Templates', 'customize-post-categories-for-ux-builder' ),
			esc_html__( 'Archive Templates', 'customize-post-categories-for-ux-builder' ),
			'edit_posts',
			self::SLUG,
			array( $this, 'callback_templates' )
		);
	}

	public function callback_templates() {
		$type            = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : 'category';
		$language        = postCategoryUXB()->current_language();
		$templates       = postCategoryUXB()->get_all_templates_by_language( $language, $type );
		$template_active = postCategoryUXB()->get_template_active( $templates );
		$query_args      = 'category' === $type ? array( 'post_type' => $this->post_type ) : array(
			'post_type' => $this->post_type,
			'type'      => $type,
		);
		$addnew          = add_query_arg( $query_args, esc_url( admin_url( 'post-new.php' ) ) );
		require_once plugin_dir_path( __FILE__ ) . 'partials/templates.php';
	}

	public function template_include( $template ) {
		global $wp_query;
		if ( $wp_query->query_vars['post_type'] === $this->post_type ) {
			$template_redirect = plugin_dir_path( __FILE__ ) . 'partials/post-type.php';
			if ( file_exists( $template_redirect ) ) {
				return $template_redirect;
			}
		}
		return $template;
	}

	public function add_meta_boxes() {
		add_meta_box(
			$this->plugin_name . '-meta-box',
			esc_html__( 'Template Attributes', 'customize-post-categories-for-ux-builder' ),
			array( $this, 'metabox_callback' ),
			$this->post_type,
			'side',
			'default'
		);
	}

	public function metabox_callback( $post ) {
		$attribute       = get_post_meta( $post->ID, 'template_attribute', true ) ? get_post_meta( $post->ID, 'template_attribute', true ) : 'default';
		$list_attributes = postCategoryUXB()->get_template_attributes();
		echo '<select name="template_attribute" id="template_attribute">';
		foreach ( $list_attributes as $key => $value ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $attribute, $key ) . '>' . esc_html( $value ) . '</option>';
		}
		echo '</select>';

		if ( isset( $_GET['type'] ) ) {
			$type = isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : false;
		} else {
			$type = get_post_meta( $post->ID, 'mst_type', true ) ? get_post_meta( $post->ID, 'mst_type', true ) : false;
		}

		if ( $type ) {
			echo '<input class="post-uxb-type-template" type="text" name="mst_type" value="' . esc_attr( $type ) . '">';
		}
	}

	public function save_post( $post_id, $post, $update ) {
		$post_status = get_post_status();
		if ( ! $post_status || empty( $post_status ) || ! $update ) {
			return;
		}
        // @codingStandardsIgnoreLine
		$template_attribute = isset( $_POST['template_attribute'] ) ? sanitize_text_field( $_POST['template_attribute'] ) : 'default';
		// @codingStandardsIgnoreLine
        if ( false !== strpos( $_POST['_wp_http_referer'], 'post-new.php' ) ) {
			$language = postCategoryUXB()->current_language();
			update_post_meta( $post_id, 'mst_language', $language );
			update_post_meta( $post_id, 'mst_active', 0 );
		}
		update_post_meta( $post_id, 'template_attribute', $template_attribute );
		// @codingStandardsIgnoreLine
        if ( isset( $_POST['mst_type'] ) ) {
            // @codingStandardsIgnoreLine
			update_post_meta( $post_id, 'mst_type', sanitize_text_field( $_POST['mst_type'] ) );
		}
	}

	public function post_updated_messages( $messages ) {
		$post_id = isset( $_GET['post'] ) ? sanitize_text_field( $_GET['post'] ) : false;
		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return $messages;
		}
		$type                         = $post_id ? ( get_post_meta( $post_id, 'mst_type', true ) ? get_post_meta( $post_id, 'mst_type', true ) : 'category' ) : 'category';
		$type_info                    = postCategoryUXB()->get_archive_info( $type );
		$query_args                   = array(
			'page' => self::SLUG,
			'type' => $type,
		);
		$url_templates                = sprintf( ' <a href="%1$s">%2$s</a>', esc_url( add_query_arg( $query_args, esc_url( admin_url( 'edit.php' ) ) ) ), esc_html( $type_info['txt-all'] ) );
		$messages[ $this->post_type ] = array(
			0  => '',
			1  => esc_html__( 'Template updated.', 'customize-post-categories-for-ux-builder' ) . ' ' . $url_templates,
			2  => esc_html__( 'Custom field updated.', 'customize-post-categories-for-ux-builder' ),
			3  => esc_html__( 'Custom field deleted.', 'customize-post-categories-for-ux-builder' ),
			4  => esc_html__( 'Template updated.', 'customize-post-categories-for-ux-builder' ) . ' ' . $url_templates,
			5  => isset( $_GET['revision'] ) ? esc_html__( 'Template restored to revision.', 'customize-post-categories-for-ux-builder' ) : false,
			6  => esc_html__( 'Template published.', 'customize-post-categories-for-ux-builder' ) . ' ' . $url_templates,
			7  => esc_html__( 'Template saved.', 'customize-post-categories-for-ux-builder' ),
			8  => esc_html__( 'Template submitted.', 'customize-post-categories-for-ux-builder' ),
			9  => esc_html__( 'Template scheduled', 'customize-post-categories-for-ux-builder' ),
			10 => esc_html__( 'Template draft updated.', 'customize-post-categories-for-ux-builder' ),
		);
		return $messages;
	}

	public function admin_print_styles_posttype() {
		global $post;
		if ( $post->post_type === $this->post_type ) {
			wp_enqueue_style( $this->plugin_name . '-posttype', plugin_dir_url( __FILE__ ) . 'css/posttype.css', array(), $this->version, 'all' );
		}
	}

	public function admin_print_scripts_posttype() {
		global $post;
		if ( $post->post_type === $this->post_type ) {
			$is_publish                         = isset( $_GET['post_type'] ) && ! empty( $_GET['post_type'] ) ? 'no' : 'yes';
			$data_localize_script               = postCategoryUXB()->get_localize_script();
			$data_localize_script['is_publish'] = $is_publish;
			wp_enqueue_script( $this->plugin_name . '-posttype', plugin_dir_url( __FILE__ ) . 'js/posttype.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name . '-posttype',
				'post_cat_uxb_data',
				$data_localize_script
			);
		}
	}

	public function ux_builder_element() {
		// ARCHIVE SUBCATEGORIES
		add_ux_builder_shortcode(
			'post_cat_uxb_thumbnail',
			array(
				'name'      => esc_html__( 'Thumbnail', 'customize-post-categories-for-ux-builder' ),
				'category'  => esc_html__( 'Post Archive', 'customize-post-categories-for-ux-builder' ),
				'thumbnail' => postCategoryUXB()->post_cat_uxb_thumbnail( 'image' ),
				'options'   => postCategoryUXB()->cat_thumbnail_options(),
			)
		);
		// ARCHIVE SUBCATEGORIES
		add_ux_builder_shortcode(
			'post_cat_uxb_subcategories',
			array(
				'name'      => esc_html__( 'Subcategories', 'customize-post-categories-for-ux-builder' ),
				'category'  => esc_html__( 'Post Archive', 'customize-post-categories-for-ux-builder' ),
				'thumbnail' => postCategoryUXB()->post_cat_uxb_thumbnail( 'archive' ),
				'options'   => postCategoryUXB()->subcategories_options(),
			)
		);
		// ARCHIVE TITLE ELEMENT
		add_ux_builder_shortcode(
			'post_cat_uxb_name',
			array(
				'name'      => esc_html__( 'Archive Title', 'customize-post-categories-for-ux-builder' ),
				'category'  => esc_html__( 'Post Archive', 'customize-post-categories-for-ux-builder' ),
				'wrap'      => false,
				'thumbnail' => postCategoryUXB()->post_cat_uxb_thumbnail( 'title' ),
				'allow'     => array(),
				'presets'   => array(),
				'options'   => array(
					'layout_options'   => array(
						'type'    => 'group',
						'heading' => esc_html__( 'Layout', 'customize-post-categories-for-ux-builder' ),
						'options' => array(
							'style'      => array(
								'type'       => 'select',
								'heading'    => esc_html__( 'Style', 'customize-post-categories-for-ux-builder' ),
								'full_width' => true,
								'default'    => 'default',
								'options'    => array(
									'default'  => 'Default',
									'featured' => 'Featured',
								),
							),
							'bg_color'   => array(
								'type'       => 'colorpicker',
								'heading'    => esc_html__( ' Background color', 'customize-post-categories-for-ux-builder' ),
								'conditions' => 'style === "featured"',
								'default'    => '#446084',
								'format'     => 'rgb',
								'position'   => 'bottom right',
								'helpers'    => postCategoryUXB()->color_helper_options(),
							),
							'title'      => array(
								'type'    => 'radio-buttons',
								'heading' => esc_html__( 'Show Title', 'customize-post-categories-for-ux-builder' ),
								'default' => 'yes',
								'options' => array(
									'yes' => array( 'title' => 'YES' ),
									'no'  => array( 'title' => 'NO' ),
								),
							),
							'breadcrumb' => array(
								'type'    => 'radio-buttons',
								'heading' => esc_html__( 'Show Breadcrumbs', 'customize-post-categories-for-ux-builder' ),
								'default' => 'yes',
								'options' => array(
									'yes' => array( 'title' => 'YES' ),
									'no'  => array( 'title' => 'NO' ),
								),
							),
						),
					),
					'advanced_options' => postCategoryUXB()->advanced_options(),
				),
			)
		);

		// ARCHIVE DESCRIPTION ELEMENT
		add_ux_builder_shortcode(
			'post_cat_uxb_desc',
			array(
				'name'      => esc_html__( 'Description', 'customize-post-categories-for-ux-builder' ),
				'category'  => esc_html__( 'Post Archive', 'customize-post-categories-for-ux-builder' ),
				'thumbnail' => postCategoryUXB()->post_cat_uxb_thumbnail( 'description' ),
				'priority'  => 1,
				'options'   => array(
					'typography_options' => array(
						'type'    => 'group',
						'heading' => esc_html__( 'Typography', 'customize-post-categories-for-ux-builder' ),
						'options' => array(
							'width'       => array(
								'type'    => 'select',
								'heading' => esc_html__( 'Width', 'customize-post-categories-for-ux-builder' ),
								'default' => '',
								'options' => array(
									''           => 'Container',
									'full-width' => 'Full Width',
								),
							),
							'font_size'   => array(
								'type'       => 'slider',
								'heading'    => esc_html__( 'Font size', 'customize-post-categories-for-ux-builder' ),
								'responsive' => true,
								'unit'       => 'rem',
								'max'        => 4,
								'min'        => 0.75,
								'step'       => 0.05,
							),
							'line_height' => array(
								'type'       => 'slider',
								'heading'    => esc_html__( 'Line height', 'customize-post-categories-for-ux-builder' ),
								'responsive' => true,
								'max'        => 3,
								'min'        => 0.75,
								'step'       => 0.05,
							),
							'text_align'  => array(
								'type'       => 'radio-buttons',
								'heading'    => esc_html__( 'Text align', 'customize-post-categories-for-ux-builder' ),
								'responsive' => true,
								'default'    => '',
								'options'    => postCategoryUXB()->text_align_options(),
							),
							'text_color'  => array(
								'type'     => 'colorpicker',
								'heading'  => esc_html__( 'Text color', 'customize-post-categories-for-ux-builder' ),
								'format'   => 'rgb',
								'position' => 'bottom right',
								'helpers'  => postCategoryUXB()->color_helper_options(),
							),
						),
					),
					'advanced_options'   => postCategoryUXB()->advanced_options(),
				),
			)
		);

		// POSTS BY ARCHIVE ELEMENT
		add_ux_builder_shortcode(
			'post_cat_uxb_list',
			array(
				'name'      => esc_html__( 'Archive posts', 'customize-post-categories-for-ux-builder' ),
				'category'  => esc_html__( 'Post Archive', 'customize-post-categories-for-ux-builder' ),
				'thumbnail' => postCategoryUXB()->post_cat_uxb_thumbnail( 'archive' ),
				'options'   => postCategoryUXB()->posts_by_category_options(),
			)
		);
	}

	public function get_data_form_fields( $term, $type = 'category' ) {
		$language        = postCategoryUXB()->current_language();
		$name            = postCategoryUXB()->get_meta_name( $type, $language );
		$selected        = isset( $term->term_id ) ? get_term_meta( $term->term_id, $name, true ) : '';
		$templates       = postCategoryUXB()->get_all_templates_by_language( $language, $type );
		$template_active = postCategoryUXB()->get_template_active( $templates );
		$term_id         = isset( $term->term_id ) ? $term->term_id : 0;
		require_once plugin_dir_path( __FILE__ ) . 'partials/metabox.php';
	}

	public function save_data_form_fields( $term_id, $type = 'category' ) {
		$language = postCategoryUXB()->current_language();
		$name     = postCategoryUXB()->get_meta_name( $type, $language );
		// @codingStandardsIgnoreLine
        if ( isset( $_POST[ $name ] ) ) {
            // @codingStandardsIgnoreLine
			$selected = sanitize_text_field( $_POST[ $name ] );
			update_term_meta( $term_id, $name, $selected );
		}
		// @codingStandardsIgnoreLine
		if ( isset( $_POST[ 'post_cat_uxb_thumbnail_id' ] ) ) {
			// @codingStandardsIgnoreLine
			$thumbnail_id = intval(sanitize_text_field( $_POST[ 'post_cat_uxb_thumbnail_id' ] ));
			update_term_meta( $term_id, 'post_cat_uxb_thumbnail_id', $thumbnail_id );
		}
	}

	public function category_add_edit_form_fields( $term ) {
		$this->get_data_form_fields( $term );
	}

	public function tag_add_edit_form_fields( $term ) {
		$this->get_data_form_fields( $term, 'tag' );
	}

	public function save_add_edit_category_fields( $term_id ) {
		$this->save_data_form_fields( $term_id );
	}

	public function save_add_edit_tag_fields( $term_id ) {
		$this->save_data_form_fields( $term_id, 'tag' );
	}

	public function delete_template() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'post-category-uxb-nonce' ) ) {
				wp_send_json_error( array( 'status' => esc_html__( 'Nonce is invalid', 'customize-post-categories-for-ux-builder' ) ) );
			}
			if ( isset( $_POST['id'] ) ) {
				$id = sanitize_text_field( $_POST['id'] );
				$id = intval( $id );
				wp_delete_post( $id );
				wp_send_json_success( array( 'mess' => esc_html__( 'successfully', 'customize-post-categories-for-ux-builder' ) ) );
			}
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		} catch ( Exception $ex ) {
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		} catch ( Error $ex ) {
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		}
	}

	public function active_template() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'post-category-uxb-nonce' ) ) {
				wp_send_json_error( array( 'status' => esc_html__( 'Nonce is invalid', 'customize-post-categories-for-ux-builder' ) ) );
			}
			if ( isset( $_POST['id'] ) ) {
				$id       = sanitize_text_field( $_POST['id'] );
				$id       = intval( $id );
				$language = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : '';
				$type     = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
				postCategoryUXB()->excute_active( $id, $language, $type );
				wp_send_json_success( array( 'mess' => esc_html__( 'successfully', 'customize-post-categories-for-ux-builder' ) ) );
			}
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		} catch ( Exception $ex ) {
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		} catch ( Error $ex ) {
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		}
	}

	public function duplicate_template() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'post-category-uxb-nonce' ) ) {
				wp_send_json_error( array( 'status' => esc_html__( 'Nonce is invalid', 'customize-post-categories-for-ux-builder' ) ) );
			}
			if ( isset( $_POST['id'] ) ) {
				$id = sanitize_text_field( $_POST['id'] );
				$id = intval( $id );
				postCategoryUXB()->excute_duplicate( $id );
				wp_send_json_success( array( 'mess' => esc_html__( 'successfully', 'customize-post-categories-for-ux-builder' ) ) );
			}
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		} catch ( Exception $ex ) {
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		} catch ( Error $ex ) {
			wp_send_json_error( array( 'mess' => esc_html__( 'error', 'customize-post-categories-for-ux-builder' ) ) );
		}
	}
}
