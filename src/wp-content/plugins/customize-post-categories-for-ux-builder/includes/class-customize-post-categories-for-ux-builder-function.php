<?php

class Customize_Post_Categories_For_Ux_Builder_Function {

	private static $instance = null;
	private $preview_image;
	const POST_TYPE = 'post_cat_uxbuilder';
	public function __construct() {
		$this->preview_image = POST_CATEGORY_UXBUILDER_URL . 'admin/images/template.png';
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// GET POST TYPE TEMPLATE
	public function get_post_type() {
		return self::POST_TYPE;
	}
	// REGISTER POST TYPE
	public function register_post_type() {
		$add_edit_template = isset( $_GET['edit'] ) ? esc_html__( 'Edit Template', 'customize-post-categories-for-ux-builder' ) : esc_html__( 'Add New Template', 'customize-post-categories-for-ux-builder' );
		$label             = array(
			'name'          => esc_html__( 'Posts Categories Templates', 'customize-post-categories-for-ux-builder' ),
			'singular_name' => esc_html__( 'Categories Templates', 'customize-post-categories-for-ux-builder' ),
			'add_new'       => esc_html( $add_edit_template ),
			'add_new_item'  => esc_html( $add_edit_template ),
			'new_item'      => esc_html__( 'New Template', 'customize-post-categories-for-ux-builder' ),
			'edit_item'     => esc_html__( 'Edit Template', 'customize-post-categories-for-ux-builder' ),
		);
		$args              = array(
			'labels'              => $label,
			'description'         => '',
			'supports'            => array(
				'title',
				'editor',
			),
			'public'              => true,
			'rewrite'             => array( 'slug' => self::POST_TYPE ),
			'menu_icon'           => 'dashicons-admin-page',
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,

		);
		register_post_type( self::POST_TYPE, $args );
		$set = get_option( 'post_type_rules_flased_' . self::POST_TYPE );
		if ( true !== $set ) {
			flush_rewrite_rules( false );
			update_option( 'post_type_rules_flased_' . self::POST_TYPE, true );
		}
	}

	public function get_term_id() {
		global $wp_query;
		$term_id = $wp_query->get_queried_object_id();
		return $term_id;
	}

	public function get_localize_script() {
		$post_id         = isset( $_GET['post'] ) ? sanitize_text_field( $_GET['post'] ) : false;
		$type_template   = $post_id && get_post_meta( $post_id, 'mst_type', true ) ? get_post_meta( $post_id, 'mst_type', true ) : false;
		$localize_script = array(
			'ajaxurl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'post-category-uxb-nonce' ),
			'is_page'          => $post_id ? 'edit' : 'addnew',
			'url_add_new'      => $post_id ? add_query_arg(
				array(
					'post_type' => self::POST_TYPE,
					'type'      => $type_template,
				),
				esc_url( admin_url( 'post-new.php' ) )
			) : '',
			'home_url'         => home_url(),
			'alert_publish'    => esc_html__( 'To edit template content with UX Builder, You need to publish the template first.', 'customize-post-categories-for-ux-builder' ),
			'active'           => esc_html__( 'Activating...', 'customize-post-categories-for-ux-builder' ),
			'duplicate'        => esc_html__( 'Duplicating...', 'customize-post-categories-for-ux-builder' ),
			'delete'           => esc_html__( 'Deleting...', 'customize-post-categories-for-ux-builder' ),
			'sure'             => esc_html__( 'Are you sure?', 'customize-post-categories-for-ux-builder' ),
			'sure_delete_this' => esc_html__( 'Are you sure will delete this template?', 'customize-post-categories-for-ux-builder' ),
			'language'         => $this->current_language(),
		);
		return $localize_script;
	}
	// CHECK IS PRODUCT ARCHIVE PAGE

	public function is_post_archive( $archive = 'category' ) {
		$is_page = false;
		switch ( $archive ) {
			case 'category':
				$is_page = is_category();
				break;
			case 'tag':
				$is_page = is_tag();
				break;
			default:
				break;
		}
		return $is_page;
	}

	public function check_nonce() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'post-category-uxb-nonce' ) ) {
			wp_send_json_error( array( 'status' => esc_html__( 'Nonce is invalid', 'customize-post-categories-for-ux-builder' ) ) );
		}
	}

	public function get_all_archives( $archive = 'category' ) {
		$archive      = 'category' === $archive ? $archive : 'post_tag';
		$args         = array(
			'taxonomy'     => $archive,
			'orderby'      => 'name',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0,
		);
		$all_archives = get_categories( $args );
		return $all_archives;
	}

	public function excute_active( $id = 0, $language = '', $type = 'category' ) {
		$all_archives = $this->get_all_archives( $type );
		if ( 'category' === $type ) {
			$name = ! empty( $language ) ? 'post_cat_uxb_template_' . $language : 'post_cat_uxb_template';
		} else {
			$name = ! empty( $language ) ? 'post_tag_uxb_template_' . $language : 'post_tag_uxb_template';
		}
		$templates = $this->get_all_templates_by_language( $language, $type );
		if ( 0 != $id ) {
			foreach ( $all_archives as $key => $cat ) {
				$term_meta = get_term_meta( $cat->term_taxonomy_id, $name, true );
				if ( 'active' === $term_meta || empty( $term_meta ) ) {
					update_term_meta( $cat->term_taxonomy_id, $name, 'active' );
				}
			}

			foreach ( $templates as $key => $template ) {
				if ( $template->ID != $id ) {
					update_post_meta( $template->ID, 'mst_active', 0 );
				}
			}
			update_post_meta( $id, 'mst_active', 1 );
		} else {
			foreach ( $all_archives as $key => $cat ) {
				$term_meta = get_term_meta( $cat->term_taxonomy_id, $name, true );
				if ( 'active' === $term_meta ) {
					update_term_meta( $cat->term_taxonomy_id, $name, '' );
				}
			}
			foreach ( $templates as $key => $template ) {
				update_post_meta( $template->ID, 'mst_active', 0 );
			}
		}
	}

	public function save_template( $args = array() ) {
		$date_created = current_time( 'Y-m-d H:i:s' );
		$arr          = array(
			'post_content'  => $args['post_content'],
			'post_date'     => $date_created,
			'post_date_gmt' => $date_created,
			'post_type'     => self::POST_TYPE,
			'post_title'    => $args['post_title'],
			'post_status'   => 'publish',
		);
		if ( false != $args && is_array( $arr ) ) {
			$insert_id = wp_insert_post( $arr );
			if ( $insert_id ) {

				if ( isset( $args['mst_language'] ) ) {
					update_post_meta( $insert_id, 'mst_language', $args['mst_language'] );
				}

				if ( isset( $args['mst_active'] ) ) {
					update_post_meta( $insert_id, 'mst_active', $args['mst_active'] );
				}

				if ( isset( $args['mst_type'] ) && ! empty( $args['mst_type'] ) ) {
					update_post_meta( $insert_id, 'mst_type', $args['mst_type'] );
				}

				if ( isset( $args['template_attribute'] ) ) {
					update_post_meta( $insert_id, 'template_attribute', $args['template_attribute'] );
				}
			}
			return $insert_id;
		}
		return false;
	}

	public function excute_duplicate( $id ) {
		$parent_template = get_post( $id );
		if ( $parent_template ) {
			$this->save_template(
				array(
					'post_title'         => $parent_template->post_title . ' (Copy)',
					'post_content'       => $parent_template->post_content,
					'mst_language'       => get_post_meta( $id, 'mst_language', true ),
					'mst_active'         => 0,
					'mst_type'           => get_post_meta( $id, 'mst_type', true ),
					'template_attribute' => get_post_meta( $id, 'template_attribute', true ),
				)
			);
		}
	}

	public function get_archive_page() {
		$flag = false;
		$page = 'category';
		if ( $this->is_post_archive( 'category' ) ) {
			$flag = true;
		}
		if ( $this->is_post_archive( 'tag' ) ) {
			$flag = true;
			$page = 'post_tag';
		}
		if ( ! $flag ) {
			$post_id = get_the_ID();
			$page    = get_post_meta( $post_id, 'mst_type', true ) ? 'post_tag' : 'category';
		}
		$data = array(
			'flag' => $flag,
			'page' => $page,
		);
		return $data;
	}

	public function get_all_subcategory( $parent_cat_ID ) {
		$args           = array(
			'hierarchical'     => 1,
			'show_option_none' => '',
			'hide_empty'       => 0,
			'parent'           => $parent_cat_ID,
			'taxonomy'         => 'category',
		);
		$sub_categories = get_categories( $args );
		$results        = array();
		foreach ( $sub_categories as $cat ) {
			array_push( $results, $cat->term_id );
		}
		return $results ? implode( ',', $results ) : '';
	}

	public function get_post_archive() {
		$term_id  = false;
		$archive  = $this->get_archive_page();
		$taxonomy = $archive['page'];
		if ( $archive['flag'] ) {
			$term_id = $this->get_term_id();
		} else {
			// Custom Post Type --- Template
			$post_id      = get_the_ID();
			$args         = array(
				'taxonomy'     => $taxonomy,
				'show_count'   => 1,
				'pad_counts'   => 0,
				'hierarchical' => 1,
				'title_li'     => '',
				'hide_empty'   => 1,
				'parent'       => 0,
			);
			$all_archives = get_categories( $args );
			$term_id      = $all_archives ? array_shift( $all_archives )->term_id : false;
		}
		$result = $term_id ? get_term_by( 'id', $term_id, $taxonomy, 'ARRAY_A' ) : false;
		return $result;
	}

	public function show_template_content( $content ) {
		echo do_shortcode( $content );
	}

	public function post_cat_uxb_thumbnail( $name ) {
		return POST_CATEGORY_UXBUILDER_URL . 'admin/images/shortcodes/' . $name . '.svg';
	}

	public function current_language() {
		$checkWpml = $this->site_active_wpml();
		if ( $checkWpml ) {
			return $checkWpml['language_default'] == $checkWpml['language'] ? '' : $checkWpml['language'];
		} else {
			return '';
		}
	}

	public function site_active_wpml() {
		$flag = false;
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			global $sitepress;
			$flag        = true;
			$defaultLang = $sitepress->get_default_language();
			$currentLang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '';
		} elseif ( defined( 'POLYLANG_VERSION' ) ) {
			$flag        = true;
			$defaultLang = pll_default_language( 'slug' );
			$currentLang = pll_current_language( 'slug' );
			$currentLang = ! empty( $currentLang ) ? $currentLang : $defaultLang;
		}
		if ( $flag ) {
			return array(
				'language_default' => $defaultLang,
				'language'         => $currentLang,
			);
		}
		return false;
	}

	public function get_template_attributes() {
		$args = array(
			'default'                                 => esc_html__( 'Default', 'customize-post-categories-for-ux-builder' ),
			'fullwidth'                               => esc_html__( 'Full Width', 'customize-post-categories-for-ux-builder' ),
			'fullwidth-transparent-header'            => esc_html__( 'Full Width - Transparent Header', 'customize-post-categories-for-ux-builder' ),
			'fullwidth-transparent-header-light-text' => esc_html__( 'Full Width - Transparent Header - Light Text', 'customize-post-categories-for-ux-builder' ),
			'fullwidth-header-on-scroll'              => esc_html__( 'Full Width - Header on Scroll', 'customize-post-categories-for-ux-builder' ),
			'left-sidebar'                            => esc_html__( 'Left Sidebar', 'customize-post-categories-for-ux-builder' ),
			'right-sidebar'                           => esc_html__( 'Right Sidebar', 'customize-post-categories-for-ux-builder' ),
		);
		return $args;
	}

	public function get_template_active( $templates ) {
		$result = array();
		foreach ( $templates as $key => $template ) {
			if ( get_post_meta( $template->ID, 'mst_active', true ) == 1 ) {
				$result = $template;
				break;
			}
		}
		return $result;
	}

	public function get_data_type( $type = 'category' ) {
		if ( 'category' === $type ) {
			$args_type = array(
				'key'     => 'mst_type',
				'compare' => 'NOT EXISTS',
			);
		} else {
			$args_type = array(
				'key'     => 'mst_type',
				'value'   => $type,
				'compare' => '=',
			);
		}

		return $args_type;
	}

	public function get_meta_name( $type = 'category', $language = '' ) {
		$name = '';
		switch ( $type ) {
			case 'tag':
				$name = ! empty( $language ) ? 'post_tag_uxb_template_' . $language : 'post_tag_uxb_template';
				break;
			default: // category
				$name = ! empty( $language ) ? 'post_cat_uxb_template_' . $language : 'post_cat_uxb_template';
				break;
		}

		return $name;
	}

	public function get_template_active_by_language( $language = '', $type = 'category' ) {
		$args      = array(
			'post_type'      => self::POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => array( 'publish', 'pending', 'future' ),
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'mst_active',
					'value'   => 1,
					'compare' => '=',
				),
				array(
					'key'     => 'mst_language',
					'value'   => $language,
					'compare' => '=',
				),
				$this->get_data_type( $type ),
			),
		);
		$results   = array();
		$templates = get_posts( $args );
		foreach ( $templates as $template ) {
			if ( get_post_meta( $template->ID, 'mst_active', true ) == 1 ) {
				$results = array(
					'postid'   => $template->ID,
					'title'    => $template->post_title,
					'content'  => $template->post_content,
					'language' => get_post_meta( $template->ID, 'mst_language', true ),
					'active'   => get_post_meta( $template->ID, 'mst_active', true ),
				);
				break;
			}
		}
		return $results;
	}

	public function get_all_templates_by_language( $language, $type = 'category' ) {
		$args      = array(
			'post_type'      => self::POST_TYPE,
			'posts_per_page' => -1,
			'post_status'    => array( 'publish', 'pending', 'future' ),
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'mst_language',
					'value'   => $language,
					'compare' => '=',
				),
				$this->get_data_type( $type ),
			),
		);
		$templates = get_posts( $args );
		return $templates;
	}

	public function template_active_by_term_id( $term_id, $language, $type = 'category' ) {
		$id        = false;
		$flag      = false;
		$checkWpml = $this->site_active_wpml();
		if ( $checkWpml ) {
			if ( $checkWpml['language_default'] === $checkWpml['language'] ) {
				$id = get_term_meta( $term_id, $this->get_meta_name( $type ), true );
			} else {
				$id = get_term_meta( $term_id, $this->get_meta_name( $type, $checkWpml['language'] ), true );
			}
		} else {
			$id = get_term_meta( $term_id, $this->get_meta_name( $type ), true );
		}
		$template_active = $this->get_template_active_by_language( $language, $type );
		if ( ! empty( $id ) ) {
			if ( 'active' === $id ) {
				if ( $template_active ) {
					$flag = true;
				}
			} else {
				$template_by_id = get_post( intval( $id ) );
				if ( $template_by_id ) {
					$results         = array(
						'postid'   => $id,
						'title'    => $template_by_id->post_title,
						'content'  => $template_by_id->post_content,
						'language' => get_post_meta( $id, 'mst_language', true ),
						'active'   => get_post_meta( $id, 'mst_active', true ),
					);
					$template_active = $results;
				}
				$flag = $template_by_id ? true : false;
			}
		}

		return array(
			'active'   => $flag,
			'id'       => $id,
			'template' => $template_active,
		);
	}

	public function get_import_form_html( $language ) {
		$html  = '<button data-language="' . esc_attr( $language ) . '" class="button button-primary btn-show-form-import-post-category" title="' . esc_attr__( 'Import All Template', 'customize-post-categories-for-ux-builder' ) . '">' . esc_html__( 'Import Template', 'customize-post-categories-for-ux-builder' ) . '</button> ';
		$html .= '<button data-language="' . esc_attr( $language ) . '" class="button button-primary btn-export-post-category" title="' . esc_attr__( 'Export All Template', 'customize-post-categories-for-ux-builder' ) . '">' . esc_html__( 'Export Template', 'customize-post-categories-for-ux-builder' ) . '</button>';
		$html .= '<div class="form-import-post-category">';
		$html .= '<form id="frm-import-post-category" method="post" enctype="multipart/form-data">';
		$html .= '<input id="file-import-post-category" type="file" />';
		$html .= '<button type="submit" data-language="' . esc_attr( $language ) . '" class="button button-primary btn-import-post-category">' . esc_html__( 'Import Template', 'customize-post-categories-for-ux-builder' ) . '</button>';
		$html .= '</form>';
		$html .= '</div><hr/>';
		return $html;
	}

	public function get_template_active_html( $template ) {
		$html = '';
		if ( $template ) {
			$urledit = add_query_arg(
				array(
					'post'   => $template->ID,
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			);
			$html   .= '<div class="theme active">';
			$html   .= '<div class="theme-screenshot">';
			$html   .= '<img src="' . esc_url( $this->preview_image ) . '"/>';
			$html   .= '</div>';
			$html   .= '<a href="' . esc_url( $urledit ) . '"><span class="more-details">' . esc_html__( 'Edit this template', 'customize-post-categories-for-ux-builder' ) . '</span></a>';
			$html   .= '<div class="theme-id-container">';
			$html   .= '<h2 class="theme-name"><span>' . esc_html__( 'Active', 'customize-post-categories-for-ux-builder' ) . ':</span> ' . esc_html( $template->post_title ) . '</h2>';
			$html   .= '<div class="theme-actions">';
			$html   .= '<a class="post-category-uxb-duplicate-template button button-primary" href="javascript:void(0)" data-template-id="' . esc_attr( $template->ID ) . '">' . esc_html__( 'Duplicate', 'customize-post-categories-for-ux-builder' ) . '</a>';
			$html   .= '</div>';
			$html   .= '</div>';
			$html   .= '</div>';
		}
		return $html;
	}

	public function get_all_templates_html( $templates, $language ) {
		$html = '';
		foreach ( $templates as $key => $template ) {
			if ( get_post_meta( $template->ID, 'mst_active', true ) != 1 ) {
				$urledit = add_query_arg(
					array(
						'post'   => $template->ID,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				);
				$html   .= '<div class="theme post-archive-templates">';
				$html   .= '<div class="theme-screenshot">';
				$html   .= '<img src="' . esc_url( $this->preview_image ) . '"/>';
				$html   .= '</div>';
				$html   .= '<div class="notice inline notice-warning updating-message archive-template-action"></div>';
				$html   .= '<a href="' . esc_url( $urledit ) . '"><span class="more-details">' . esc_html__( 'Edit this template', 'customize-post-categories-for-ux-builder' ) . '</span></a>';
				$html   .= '<div class="theme-id-container">';
				$html   .= '<h2 class="theme-name">' . esc_html( $template->post_title ) . '</h2>';
				$html   .= '<div class="theme-actions">';
				$html   .= '<a data-language="' . esc_attr( $language ) . '" class="post-category-uxb-active-template button button-primary" href="javascript:void(0)" data-template-id="' . esc_attr( $template->ID ) . '">' . esc_html__( 'Active', 'customize-post-categories-for-ux-builder' ) . '</a>';
				$html   .= '<a class="post-category-uxb-duplicate-template button" href="javascript:void(0)" data-template-id="' . esc_attr( $template->ID ) . '">' . esc_html__( 'Duplicate', 'customize-post-categories-for-ux-builder' ) . '</a>';
				$html   .= '<a data-language="' . esc_attr( $language ) . '" class="post-category-uxb-delete-template button" href="javascript:void(0)" data-template-id="' . esc_attr( $template->ID ) . '">' . esc_html__( 'Delete', 'customize-post-categories-for-ux-builder' ) . '</a>';
				$html   .= '</div>';
				$html   .= '</div>';
				$html   .= '</div>';
			}
		}
		return $html;
	}

	public function get_pagination_html( $loops, $paged ) {
		$prev_arrow = is_rtl() ? get_flatsome_icon( 'icon-angle-right' ) : get_flatsome_icon( 'icon-angle-left' );
		$next_arrow = is_rtl() ? get_flatsome_icon( 'icon-angle-left' ) : get_flatsome_icon( 'icon-angle-right' );
		$total      = $loops->max_num_pages;
		$big        = 999999999; // need an unlikely integer
		if ( $total > 1 ) {
			$current_page = get_query_var( 'paged' );
			if ( ! $current_page ) {
				$current_page = 1;
			}
			if ( get_option( 'permalink_structure' ) ) {
				$format = 'page/%#%/';
			} else {
				$format = '&paged=%#%';
			}
			$pages = paginate_links(
				array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'    => $format,
					'current'   => $paged,
					'total'     => $total,
					'mid_size'  => 3,
					'type'      => 'array',
					'prev_text' => $prev_arrow,
					'next_text' => $next_arrow,
				)
			);

			if ( is_array( $pages ) ) {
				echo '<ul class="post-cat-uxb-pagination page-numbers nav-pagination links text-center">';
				foreach ( $pages as $page ) {
					$page = str_replace( 'page-numbers', 'page-number', $page );
					echo "<li>$page</li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo '</ul>';
			}
		}
	}

	// DEFAULT OPTIONS FOR ARCHIVE POSTS ELEMENT

	public function style_options() {
		return array(
			'none'     => 'None',
			'default'  => 'Default',
			'normal'   => 'Normal',
			'overlay'  => 'Overlay',
			'shade'    => 'Shade',
			'vertical' => 'Vertical',
			'label'    => 'Label',
			'push'     => 'Push',
			'badge'    => 'Badge',
			'bounce'   => 'Bounce',
		);
	}

	public function size_options() {
		return array(
			'xxsmall' => 'XX-Small',
			'xsmall'  => 'X-Small',
			'smaller' => 'Smaller',
			'small'   => 'Small',
			''        => 'Normal',
			'large'   => 'Large',
			'larger'  => 'Larger',
			'xlarge'  => 'X-Large',
			'xxlarge' => 'XX-Large',
		);
	}

	public function color_helper_options() {
		return array(
			array(
				'title' => 'Dark',
				'value' => 'rgb(0,0,0)',
			),
			array(
				'title' => 'White',
				'value' => 'rgb(255,255,255)',
			),
			array(
				'title' => 'Primary',
				'value' => get_theme_mod( 'color_primary', Flatsome_Default::COLOR_PRIMARY ),
			),
			array(
				'title' => 'Secondary',
				'value' => get_theme_mod( 'color_secondary', Flatsome_Default::COLOR_SECONDARY ),
			),
			array(
				'title' => 'Success',
				'value' => get_theme_mod( 'color_success', Flatsome_Default::COLOR_SUCCESS ),
			),
		);
	}

	public function text_align_options() {
		return array(
			''       => array(
				'title' => 'None',
				'icon'  => 'dashicons-no-alt',
			),
			'left'   => array(
				'title' => 'Left',
				'icon'  => 'dashicons-editor-alignleft',
			),
			'center' => array(
				'title' => 'Center',
				'icon'  => 'dashicons-editor-aligncenter',
			),
			'right'  => array(
				'title' => 'Right',
				'icon'  => 'dashicons-editor-alignright',
			),
		);
	}

	public function grid_options() {
		return array(
			'1'  => '1',
			'2'  => '2',
			'3'  => '3',
			'4'  => '4',
			'5'  => '5',
			'6'  => '6',
			'7'  => '7',
			'8'  => '8',
			'9'  => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12',
			'13' => '13',
			'14' => '14',
		);
	}

	public function get_animate_options() {
		return array(
			'none'          => 'None',
			'fadeInLeft'    => 'Fade In Left',
			'fadeInRight'   => 'Fade In Right',
			'fadeInUp'      => 'Fade In Up',
			'fadeInDown'    => 'Fade In Down',
			'bounceIn'      => 'Bounce In',
			'bounceInUp'    => 'Bounce In Up',
			'bounceInDown'  => 'Bounce In Down',
			'bounceInLeft'  => 'Bounce In Left',
			'bounceInRight' => 'Bounce In Right',
			'blurIn'        => 'Blur In',
			'flipInX'       => 'Flip In X',
			'flipInY'       => 'Flip In Y',
		);
	}

	public function get_nav_style_options() {
		return array(
			'simple'      => 'Simple',
			'line'        => 'Line',
			'divided'     => 'Divided',
			'line-grow'   => 'Line Grow',
			'line-bottom' => 'Line Bottom',
			'outline'     => 'Outline',
			'tabs'        => 'Tabs',
			'bold'        => 'Bold',
			'pills'       => 'Pills',
		);
	}

	public function get_orderby_options() {
		return array(
			'ID'            => 'ID',
			'title'         => 'Title',
			'name'          => 'Name',
			'date'          => 'Published Date',
			'modified'      => 'Modified Date',
			'rand'          => 'Random',
			'comment_count' => 'Comment Count',
			'menu_order'    => 'Menu Order',
		);
	}

	public function get_image_hover_options() {
		return array(
			''                  => 'None',
			'zoom'              => 'Zoom',
			'zoom-long'         => 'Zoom Long',
			'zoom-fade'         => 'Zoom Fade',
			'blur'              => 'Blur',
			'fade-in'           => 'Fade In',
			'fade-out'          => 'Fade Out',
			'glow'              => 'Glow',
			'color'             => 'Add Color',
			'grayscale'         => 'Grayscale',
			'overlay-add'       => 'Add Overlay',
			'overlay-remove'    => 'Remove Overlay',
			'overlay-add-50'    => 'Add Overlay (50%)',
			'overlay-remove-50' => 'Remove Overlay (50%)',
		);
	}

	public function advanced_options() {
		return array(
			'type'    => 'group',
			'heading' => esc_html__( 'Advanced', 'customize-post-categories-for-ux-builder' ),
			'options' => array(
				'class'      => array(
					'type'       => 'textfield',
					'heading'    => esc_html__( 'Class', 'customize-post-categories-for-ux-builder' ),
					'param_name' => 'class',
					'default'    => '',
				),
				'visibility' => array(
					'type'    => 'select',
					'heading' => esc_html__( 'Visibility', 'customize-post-categories-for-ux-builder' ),
					'default' => '',
					'options' => array(
						''                               => esc_html__( 'Visible', 'customize-post-categories-for-ux-builder' ),
						'hidden'                         => esc_html__( 'Hidden', 'customize-post-categories-for-ux-builder' ),
						'hide-for-medium'                => esc_html__( 'Only for Desktop', 'customize-post-categories-for-ux-builder' ),
						'show-for-small'                 => esc_html__( 'Only for Mobile', 'customize-post-categories-for-ux-builder' ),
						'show-for-medium hide-for-small' => esc_html__( 'Only for Tablet', 'customize-post-categories-for-ux-builder' ),
						'show-for-medium'                => esc_html__( 'Hide for Desktop', 'customize-post-categories-for-ux-builder' ),
						'hide-for-small'                 => esc_html__( 'Hide for Mobile', 'customize-post-categories-for-ux-builder' ),
					),
				),
			),
		);
	}

	public function images_hover_options() {
		return array(
			''                  => 'None',
			'zoom'              => 'Zoom',
			'zoom-long'         => 'Zoom Long',
			'zoom-fade'         => 'Zoom Fade',
			'blur'              => 'Blur',
			'fade-in'           => 'Fade In',
			'fade-out'          => 'Fade Out',
			'glow'              => 'Glow',
			'color'             => 'Add Color',
			'grayscale'         => 'Grayscale',
			'overlay-add'       => 'Add Overlay',
			'overlay-remove'    => 'Remove Overlay',
			'overlay-add-50'    => 'Add Overlay (50%)',
			'overlay-remove-50' => 'Remove Overlay (50%)',
		);
	}

	public function subcategories_options() {
		$options = array(
			'style_options'         => array(
				'type'    => 'group',
				'heading' => __( 'Style', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'style' => array(
						'type'    => 'select',
						'heading' => __( 'Style', 'customize-post-categories-for-ux-builder' ),
						'default' => 'badge',
						'options' => array(
							'none'     => 'None',
							'default'  => 'Default',
							'normal'   => 'Normal',
							'overlay'  => 'Overlay',
							'shade'    => 'Shade',
							'vertical' => 'Vertical',
							'label'    => 'Label',
							'push'     => 'Push',
							'badge'    => 'Badge',
							'bounce'   => 'Bounce',
						),
					),
				),
			),
			'layout_options'        => array(
				'type'    => 'group',
				'heading' => __( 'Layout', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'type'        => array(
						'type'    => 'select',
						'heading' => 'Type',
						'default' => 'slider',
						'options' => array(
							'slider'      => 'Slider',
							'slider-full' => 'Full Slider',
							'row'         => 'Row',
							'masonry'     => 'Masonry',
							'grid'        => 'Grid',
						),
					),
					'grid'        => array(
						'type'       => 'select',
						'heading'    => 'Grid Layout',
						'conditions' => 'type === "grid"',
						'default'    => '1',
						'options'    => array(
							'1'  => '1',
							'2'  => '2',
							'3'  => '3',
							'4'  => '4',
							'5'  => '5',
							'6'  => '6',
							'7'  => '7',
							'8'  => '8',
							'9'  => '9',
							'10' => '10',
							'11' => '11',
							'12' => '12',
							'13' => '13',
							'14' => '14',
						),
					),
					'grid_height' => array(
						'type'       => 'textfield',
						'heading'    => __( 'Grid Height', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'type === "grid"',
						'default'    => '600px',
						'responsive' => true,
					),
					'width'       => array(
						'type'       => 'select',
						'heading'    => 'Width',
						'conditions' => 'type !== "slider-full"',
						'default'    => '',
						'options'    => array(
							''           => 'Container',
							'full-width' => 'Full Width',
						),
					),
					'col_spacing' => array(
						'type'       => 'select',
						'heading'    => 'Column Spacing',
						'conditions' => 'type !== "slider-full"',
						'default'    => 'normal',
						'options'    => array(
							'collapse' => 'Collapse',
							'xsmall'   => 'X Small',
							'small'    => 'Small',
							'normal'   => 'Normal',
							'large'    => 'Large',
						),
					),
					'columns'     => array(
						'type'       => 'slider',
						'heading'    => 'Columns',
						'conditions' => 'type !== "grid" && type !== "slider-full"',
						'default'    => '4',
						'responsive' => true,
						'max'        => '8',
						'min'        => '1',
					),
					'depth'       => array(
						'type'    => 'slider',
						'heading' => __( 'Depth', 'customize-post-categories-for-ux-builder' ),
						'default' => '0',
						'max'     => '5',
						'min'     => '0',
					),
					'depth_hover' => array(
						'type'    => 'slider',
						'heading' => __( 'Depth Hover', 'customize-post-categories-for-ux-builder' ),
						'default' => '0',
						'max'     => '5',
						'min'     => '0',
					),
					'animate'     => array(
						'type'    => 'select',
						'heading' => __( 'Animate', 'customize-post-categories-for-ux-builder' ),
						'default' => 'none',
						'options' => $this->get_animate_options(),
					),
				),
			),
			'layout_options_slider' => array(
				'type'       => 'group',
				'heading'    => 'Slider',
				'conditions' => 'type === "slider" || type === "slider-full"',
				'options'    => array(
					'slider_nav_style'    => array(
						'type'    => 'select',
						'heading' => 'Nav Style',
						'default' => 'reveal',
						'options' => array(
							'simple' => 'Simple',
							'reveal' => 'Reveal',
							'circle' => 'Circle',
						),
					),
					'slider_nav_color'    => array(
						'type'    => 'select',
						'heading' => 'Nav Color',
						'default' => '',
						'options' => array(
							'light' => 'Light',
							''      => 'Dark',
						),
					),
					'slider_nav_position' => array(
						'type'       => 'select',
						'heading'    => 'Nav Position',
						'conditions' => 'slider_nav_style !== "reveal"',
						'default'    => 'inside',
						'options'    => array(
							'inside'  => 'Inside',
							'outside' => 'Outside',
						),
					),
					'slider_bullets'      => array(
						'type'    => 'select',
						'heading' => 'Bullets',
						'default' => '',
						'options' => array(
							''     => 'Disable',
							'true' => 'Enable',
						),
					),
					'auto_slide'          => array(
						'type'    => 'select',
						'heading' => 'Auto Slide',
						'default' => '',
						'options' => array(
							''     => 'Disabled',
							'2000' => '2 sec.',
							'3000' => '3 sec.',
							'4000' => '4 sec.',
							'5000' => '5 sec.',
							'6000' => '6 sec.',
							'7000' => '7 sec.',
						),
					),
					'infinitive'          => array(
						'type'    => 'select',
						'heading' => 'Infinitive',
						'default' => '',
						'options' => array(
							'false' => 'Disable',
							''      => 'Enable',
						),
					),
				),
			),
			'cat_meta'              => array(
				'type'    => 'group',
				'heading' => __( 'Meta', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'number'     => array(
						'type'    => 'textfield',
						'heading' => 'Total',
						'default' => '',
					),

					'offset'     => array(
						'type'    => 'textfield',
						'heading' => 'Offset',
						'default' => '',
					),

					'orderby'    => array(
						'type'    => 'select',
						'heading' => __( 'Order By', 'customize-post-categories-for-ux-builder' ),
						'default' => 'menu_order',
						'options' => array(
							'name'       => 'Name',
							'date'       => 'Date',
							'menu_order' => 'Menu Order',
						),
					),
					'order'      => array(
						'type'    => 'select',
						'heading' => __( 'Order', 'customize-post-categories-for-ux-builder' ),
						'default' => 'asc',
						'options' => array(
							'asc'  => 'ASC',
							'desc' => 'DESC',
						),
					),
					'show_count' => array(
						'type'    => 'checkbox',
						'heading' => 'Show Count',
						'default' => 'true',
					),
				),
			),
			'image_options'         => array(
				'type'    => 'group',
				'heading' => __( 'Image', 'customize-post-categories-for-ux-builder' ),
				'options' => array(

					'image_height'    => array(
						'type'        => 'scrubfield',
						'heading'     => __( 'Height', 'customize-post-categories-for-ux-builder' ),
						'conditions'  => 'type !== "grid"',
						'default'     => '',
						'placeholder' => __( 'Auto', 'customize-post-categories-for-ux-builder' ),
						'min'         => 0,
						'max'         => 1000,
						'step'        => 1,
						'helpers'     => array(
							array(
								'title' => 'X',
								'value' => '',
							),
							array(
								'title' => '1:1',
								'value' => '100%',
							),
							array(
								'title' => '2:1',
								'value' => '200%',
							),
							array(
								'title' => '4:3',
								'value' => '75%',
							),
							array(
								'title' => '16:9',
								'value' => '56.25%',
							),
							array(
								'title' => '1:2',
								'value' => '50%',
							),
						),
						'on_change'   => array(
							'selector' => '.box-image-inner',
							'style'    => 'padding-top: {{ value }}',
						),
					),

					'image_width'     => array(
						'type'      => 'slider',
						'heading'   => __( 'Width', 'customize-post-categories-for-ux-builder' ),
						'unit'      => '%',
						'default'   => 100,
						'max'       => 100,
						'min'       => 0,
						'on_change' => array(
							'selector' => '.box-image',
							'style'    => 'width: {{ value }}%',
						),
					),

					'image_radius'    => array(
						'type'      => 'slider',
						'heading'   => __( 'Radius', 'customize-post-categories-for-ux-builder' ),
						'unit'      => '%',
						'default'   => 0,
						'max'       => 100,
						'min'       => 0,
						'on_change' => array(
							'selector' => '.box-image-inner',
							'style'    => 'border-radius: {{ value }}%',
						),
					),

					'image_size'      => array(
						'type'    => 'select',
						'heading' => __( 'Size', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							''          => 'Default',
							'large'     => 'Large',
							'medium'    => 'Medium',
							'thumbnail' => 'Thumbnail',
							'original'  => 'Original',
						),
					),

					'image_overlay'   => array(
						'type'      => 'colorpicker',
						'heading'   => __( 'Overlay', 'customize-post-categories-for-ux-builder' ),
						'default'   => '',
						'alpha'     => true,
						'format'    => 'rgb',
						'position'  => 'bottom right',
						'on_change' => array(
							'selector' => '.overlay',
							'style'    => 'background-color: {{ value }}',
						),
					),

					'image_hover'     => array(
						'type'      => 'select',
						'heading'   => __( 'Hover', 'customize-post-categories-for-ux-builder' ),
						'default'   => '',
						'options'   => $this->images_hover_options(),
						'on_change' => array(
							'selector' => '.image-cover',
							'class'    => 'image-{{ value }}',
						),
					),
					'image_hover_alt' => array(
						'type'       => 'select',
						'heading'    => __( 'Hover Alt', 'customize-post-categories-for-ux-builder' ),
						'default'    => '',
						'conditions' => 'image_hover',
						'options'    => $this->images_hover_options(),
						'on_change'  => array(
							'selector' => '.image-cover',
							'class'    => 'image-{{ value }}',
						),
					),
				),
			),
			'text_options'          => array(
				'type'    => 'group',
				'heading' => __( 'Text', 'customize-post-categories-for-ux-builder' ),
				'options' => array(

					'text_pos'     => array(
						'type'       => 'select',
						'heading'    => __( 'Position', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'style === "vertical" || style === "shade" || style === "overlay"',
						'default'    => 'bottom',
						'options'    => array(
							'top'    => 'Top',
							'middle' => 'Middle',
							'bottom' => 'Bottom',
						),

						'on_change'  => array(
							'selector' => '.box',
							'class'    => 'box-text-{{ value }}',
						),
					),

					'text_align'   => array(
						'type'      => 'radio-buttons',
						'heading'   => __( 'Align', 'customize-post-categories-for-ux-builder' ),
						'default'   => 'left',
						'options'   => array(
							'left'   => array(
								'title' => 'Left',
								'icon'  => 'dashicons-editor-alignleft',
							),
							'center' => array(
								'title' => 'Center',
								'icon'  => 'dashicons-editor-aligncenter',
							),
							'right'  => array(
								'title' => 'Right',
								'icon'  => 'dashicons-editor-alignright',
							),
						),
						'on_change' => array(
							'selector' => '.box-text',
							'class'    => 'text-{{ value }}',
						),
					),

					'text_size'    => array(
						'type'      => 'radio-buttons',
						'heading'   => __( 'Size', 'customize-post-categories-for-ux-builder' ),
						'default'   => 'medium',
						'options'   => array(
							'xsmall' => array( 'title' => 'XS' ),
							'small'  => array( 'title' => 'S' ),
							'medium' => array( 'title' => 'M' ),
							'large'  => array( 'title' => 'L' ),
							'xlarge' => array( 'title' => 'XL' ),
						),
						'on_change' => array(
							'selector' => '.box-text',
							'class'    => 'is-{{ value }}',
						),
					),

					'text_hover'   => array(
						'type'    => 'select',
						'heading' => __( 'Hover', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							''         => 'None',
							'fade-out' => 'Fade Out',
							'slide'    => 'Slide In',
							'zoom'     => 'Zoom Out',
							'zoom-in'  => 'Zoom In',
							'blur'     => 'Blur In',
							'bounce'   => 'Bounce',
							'invert'   => 'Invert',
						),
					),

					'text_bg'      => array(
						'type'      => 'colorpicker',
						'heading'   => __( 'Bg Color', 'customize-post-categories-for-ux-builder' ),
						'default'   => '',
						'alpha'     => true,
						'format'    => 'rgb',
						'position'  => 'bottom right',
						'on_change' => array(
							'selector' => '.box-text',
							'style'    => 'background-color:{{ value }}',
						),
					),

					'text_color'   => array(
						'type'       => 'radio-buttons',
						'heading'    => __( 'Color', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'style !== "shade" && style !== "overlay"',
						'default'    => 'light',
						'options'    => array(
							'light' => array( 'title' => 'Dark' ),
							'dark'  => array( 'title' => 'Light' ),
						),
					),
					'text_padding' => array(
						'type'       => 'margins',
						'heading'    => __( 'Padding', 'customize-post-categories-for-ux-builder' ),
						'value'      => '',
						'full_width' => true,
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,

						'on_change'  => array(
							'selector' => '.box-text',
							'style'    => 'padding: {{ value }}',
						),
					),
				),
			),
			'advanced_options'      => $this->advanced_options(),
		);
		return $options;
	}

	public function cat_thumbnail_options() {
		$options = array(
			'image_size'          => array(
				'type'       => 'select',
				'heading'    => 'Image Size',
				'param_name' => 'image_size',
				'default'    => '',
				'options'    => array(
					''          => 'Normal',
					'large'     => 'Large',
					'medium'    => 'Medium',
					'thumbnail' => 'Thumbnail',
					'original'  => 'Original',
				),
			),
			'width'               => array(
				'type'       => 'slider',
				'heading'    => 'Width',
				'responsive' => true,
				'default'    => '100',
				'unit'       => '%',
				'max'        => '100',
				'min'        => '0',
				'on_change'  => array(
					'style' => 'width: {{ value }}%',
				),
			),
			'height'              => array(
				'type'        => 'scrubfield',
				'heading'     => __( 'Height', 'customize-post-categories-for-ux-builder' ),
				'default'     => '',
				'placeholder' => __( 'Auto', 'customize-post-categories-for-ux-builder' ),
				'min'         => 0,
				'max'         => 1000,
				'step'        => 1,
				'helpers'     => array(
					array(
						'title' => 'X',
						'value' => '',
					),
					array(
						'title' => '1:1',
						'value' => '100%',
					),
					array(
						'title' => '2:1',
						'value' => '200%',
					),
					array(
						'title' => '4:3',
						'value' => '75%',
					),
					array(
						'title' => '16:9',
						'value' => '56.25%',
					),
					array(
						'title' => '1:2',
						'value' => '50%',
					),
				),
				'on_change'   => array(
					'selector' => '.image-cover',
					'style'    => 'padding-top: {{ value }}',
				),
			),
			'margin'              => array(
				'type'       => 'margins',
				'heading'    => __( 'Margin', 'customize-post-categories-for-ux-builder' ),
				'value'      => '',
				'full_width' => true,
				'min'        => -100,
				'max'        => 100,
				'step'       => 1,
			),
			'lightbox'            => array(
				'type'    => 'radio-buttons',
				'heading' => __( 'Lightbox', 'customize-post-categories-for-ux-builder' ),
				'default' => '',
				'options' => array(
					''     => array( 'title' => 'Off' ),
					'true' => array( 'title' => 'On' ),
				),
			),
			'lightbox_image_size' => array(
				'type'       => 'select',
				'heading'    => __( 'Lightbox Image Size', 'customize-post-categories-for-ux-builder' ),
				'conditions' => 'lightbox == "true"',
				'default'    => '',
				'options'    => array(
					''          => 'Default',
					'large'     => 'Large',
					'medium'    => 'Medium',
					'thumbnail' => 'Thumbnail',
					'original'  => 'Original',
				),
			),
			'caption'             => array(
				'type'    => 'radio-buttons',
				'heading' => __( 'Caption', 'customize-post-categories-for-ux-builder' ),
				'default' => '',
				'options' => array(
					''     => array( 'title' => 'Off' ),
					'true' => array( 'title' => 'On' ),
				),
			),
			'lightbox_caption'    => array(
				'type'       => 'radio-buttons',
				'heading'    => __( 'Caption on Lightbox', 'customize-post-categories-for-ux-builder' ),
				'conditions' => 'lightbox == "true"',
				'default'    => '',
				'options'    => array(
					''     => array( 'title' => 'Off' ),
					'true' => array( 'title' => 'On' ),
				),
			),
			'image_overlay'       => array(
				'type'      => 'colorpicker',
				'heading'   => __( 'Image Overlay', 'customize-post-categories-for-ux-builder' ),
				'default'   => '',
				'alpha'     => true,
				'format'    => 'rgb',
				'position'  => 'bottom right',
				'on_change' => array(
					'selector' => '.overlay',
					'style'    => 'background-color: {{ value }}',
				),
			),

			'image_hover'         => array(
				'type'      => 'select',
				'heading'   => 'Image Hover',
				'default'   => '',
				'options'   => $this->images_hover_options(),
				'on_change' => array(
					'selector' => '.img-inner',
					'class'    => 'image-{{ value }}',
				),
			),
			'image_hover_alt'     => array(
				'type'      => 'select',
				'heading'   => 'Image Hover Alt',
				'default'   => '',
				'options'   => $this->images_hover_options(),
				'on_change' => array(
					'selector' => '.img-inner',
					'class'    => 'image-{{ value }}',
				),
			),
			'depth'               => array(
				'type'      => 'slider',
				'heading'   => 'Depth',
				'default'   => '0',
				'max'       => '5',
				'min'       => '0',
				'on_change' => array(
					'selector' => '.img-inner',
					'class'    => 'box-shadow-{{ value }}',
				),
			),
			'depth_hover'         => array(
				'type'      => 'slider',
				'heading'   => 'Depth :hover',
				'default'   => '0',
				'max'       => '5',
				'min'       => '0',
				'on_change' => array(
					'selector' => '.img-inner',
					'class'    => 'box-shadow-{{ value }}-hover',
				),
			),
			'parallax'            => array(
				'type'    => 'slider',
				'heading' => 'Parallax',
				'default' => '0',
				'max'     => '10',
				'min'     => '-10',
			),
			'animate'             => array(
				'type'    => 'select',
				'heading' => 'Animate',
				'default' => 'none',
				'options' => $this->get_animate_options(),
			),
			'advanced_options'    => $this->advanced_options(),
		);
		return $options;
	}

	public function posts_by_category_options() {
		$options = array(
			'style_options'         => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Style', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'style' => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Style', 'customize-post-categories-for-ux-builder' ),
						'default' => 'default',
						'options' => $this->style_options(),
					),
				),
			),
			'layout_options'        => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Layout', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'type'        => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Type', 'customize-post-categories-for-ux-builder' ),
						'default' => 'row',
						'options' => array(
							'slider'      => 'Slider',
							'slider-full' => 'Full Slider',
							'row'         => 'Row',
							'masonry'     => 'Masonry',
							'grid'        => 'Grid',
						),
					),
					'grid'        => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Grid Layout', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'type === "grid"',
						'default'    => '1',
						'options'    => $this->grid_options(),
					),
					'grid_height' => array(
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Grid Height', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'type === "grid"',
						'default'    => '600px',
						'responsive' => true,
					),
					'width'       => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Width', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'type !== "slider-full"',
						'default'    => '',
						'options'    => array(
							''           => 'Container',
							'full-width' => 'Full Width',
						),
					),
					'col_spacing' => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Column Spacing', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'type !== "slider-full"',
						'default'    => 'normal',
						'options'    => array(
							'collapse' => 'Collapse',
							'xsmall'   => 'X Small',
							'small'    => 'Small',
							'normal'   => 'Normal',
							'large'    => 'Large',
						),
					),
					'columns'     => array(
						'type'       => 'slider',
						'heading'    => esc_html__( 'Columns', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'type !== "grid" && type !== "slider-full"',
						'default'    => 3,
						'responsive' => true,
						'max'        => '6',
						'min'        => '1',
					),
					'depth'       => array(
						'type'    => 'slider',
						'heading' => esc_html__( 'Depth', 'customize-post-categories-for-ux-builder' ),
						'default' => '0',
						'max'     => '5',
						'min'     => '0',
					),
					'depth_hover' => array(
						'type'    => 'slider',
						'heading' => esc_html__( 'Depth Hover', 'customize-post-categories-for-ux-builder' ),
						'default' => '0',
						'max'     => '5',
						'min'     => '0',
					),
					'animate'     => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Animate', 'customize-post-categories-for-ux-builder' ),
						'default' => 'none',
						'options' => $this->get_animate_options(),
					),
				),
			),
			'layout_options_slider' => array(
				'type'       => 'group',
				'heading'    => esc_html__( 'Slider', 'customize-post-categories-for-ux-builder' ),
				'conditions' => 'type === "slider" || type === "slider-full"',
				'options'    => array(
					'slider_nav_style'    => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Nav Style', 'customize-post-categories-for-ux-builder' ),
						'default' => 'reveal',
						'options' => $this->get_nav_style_options(),
					),
					'slider_nav_color'    => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Nav Color', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							'light' => 'Light',
							''      => 'Dark',
						),
					),
					'slider_nav_position' => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Nav Position', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'slider_nav_style !== "reveal"',
						'default'    => 'inside',
						'options'    => array(
							'inside'  => 'Inside',
							'outside' => 'Outside',
						),
					),
					'slider_bullets'      => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Bullets', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							''     => 'Disable',
							'true' => 'Enable',
						),
					),
					'auto_slide'          => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Auto Slide', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							''     => 'Disabled',
							'2000' => '2 sec.',
							'3000' => '3 sec.',
							'4000' => '4 sec.',
							'5000' => '5 sec.',
							'6000' => '6 sec.',
							'7000' => '7 sec.',
						),
					),
					'infinitive'          => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Infinitive', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							'false' => 'Disable',
							''      => 'Enable',
						),
					),
				),
			),
			'post_options'          => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Posts', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'show_paginate' => array(
						'type'    => 'radio-buttons',
						'heading' => esc_html__( 'Show pagination', 'customize-post-categories-for-ux-builder' ),
						'default' => 'yes',
						'options' => array(
							'yes' => array( 'title' => 'YES' ),
							'no'  => array( 'title' => 'NO' ),
						),
					),
					'posts'         => array(
						'conditions' => 'show_paginate === "no"',
						'type'       => 'textfield',
						'heading'    => esc_html__( 'Total Posts', 'customize-post-categories-for-ux-builder' ),
						'default'    => get_option( 'posts_per_page' ) ? get_option( 'posts_per_page' ) : 10,
					),

					'offset'        => array(
						'type'    => 'textfield',
						'heading' => esc_html__( 'Offset', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
					),

					'orderby'       => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Order by', 'customize-post-categories-for-ux-builder' ),
						'default' => 'date',
						'options' => $this->get_orderby_options(),
					),

					'order'         => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Order', 'customize-post-categories-for-ux-builder' ),
						'default' => 'DESC',
						'options' => array(
							'ASC'  => 'ASC',
							'DESC' => 'DESC',
						),
					),
				),
			),
			'post_title_options'    => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Title', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'title_size'  => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Title Size', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => $this->size_options(),
					),
					'title_style' => array(
						'type'    => 'radio-buttons',
						'heading' => esc_html__( 'Title Style', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							''          => array( 'title' => 'Abc' ),
							'uppercase' => array( 'title' => 'ABC' ),
						),
					),
				),
			),
			'read_more_button'      => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Read More', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'readmore'       => array(
						'type'    => 'textfield',
						'heading' => esc_html__( 'Text', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
					),
					'readmore_color' => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Color', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'readmore',
						'default'    => '',
						'options'    => array(
							''          => 'Default',
							'primary'   => 'Primary',
							'secondary' => 'Secondary',
							'alert'     => 'Alert',
							'success'   => 'Success',
							'white'     => 'White',
						),
					),
					'readmore_style' => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Style', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'readmore',
						'default'    => 'outline',
						'options'    => array(
							''          => 'Default',
							'outline'   => 'Outline',
							'link'      => 'Simple',
							'underline' => 'Underline',
							'shade'     => 'Shade',
							'bevel'     => 'Bevel',
							'gloss'     => 'Gloss',
						),
					),
					'readmore_size'  => array(
						'type'       => 'select',
						'conditions' => 'readmore',
						'heading'    => esc_html__( 'Size', 'customize-post-categories-for-ux-builder' ),
						'default'    => '',
						'options'    => $this->size_options(),
					),
				),
			),
			'post_meta_options'     => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Meta', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'show_date'      => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Date', 'customize-post-categories-for-ux-builder' ),
						'default' => 'badge',
						'options' => array(
							'badge' => 'Badge',
							'text'  => 'Text',
							'false' => 'Hidden',
						),
					),
					'badge_style'    => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Badge Style', 'customize-post-categories-for-ux-builder' ),
						'default'    => '',
						'conditions' => 'show_date == "badge"',
						'options'    => array(
							''              => 'Default',
							'outline'       => 'Outline',
							'square'        => 'Square',
							'circle'        => 'Circle',
							'circle-inside' => 'Circle Inside',
						),
					),
					'excerpt'        => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Excerpt', 'customize-post-categories-for-ux-builder' ),
						'default' => 'visible',
						'options' => array(
							'visible' => 'Visible',
							'fade'    => 'Fade In On Hover',
							'slide'   => 'Slide In On Hover',
							'reveal'  => 'Reveal On Hover',
							'false'   => 'Hidden',
						),
					),
					'excerpt_length' => array(
						'type'    => 'slider',
						'heading' => esc_html__( 'Excerpt Length', 'customize-post-categories-for-ux-builder' ),
						'default' => 15,
						'max'     => 50,
						'min'     => 5,
					),
					'show_category'  => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Category', 'customize-post-categories-for-ux-builder' ),
						'default' => 'false',
						'options' => array(
							'label' => 'Label',
							'text'  => 'Text',
							'false' => 'Hidden',
						),
					),
					'comments'       => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Comments', 'customize-post-categories-for-ux-builder' ),
						'default' => 'visible',
						'options' => array(
							'visible' => 'Visible',
							'false'   => 'Hidden',
						),
					),
				),
			),
		);
		$box_styles = array(
			'image_options'    => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Image', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'image_height'    => array(
						'type'        => 'scrubfield',
						'heading'     => esc_html__( 'Height', 'customize-post-categories-for-ux-builder' ),
						'conditions'  => 'type !== "grid"',
						'default'     => '',
						'placeholder' => esc_html__( 'Auto', 'customize-post-categories-for-ux-builder' ),
						'min'         => 0,
						'max'         => 1000,
						'step'        => 1,
						'helpers'     => array(
							array(
								'title' => 'X',
								'value' => '',
							),
							array(
								'title' => '1:1',
								'value' => '100%',
							),
							array(
								'title' => '2:1',
								'value' => '200%',
							),
							array(
								'title' => '4:3',
								'value' => '75%',
							),
							array(
								'title' => '16:9',
								'value' => '56.25%',
							),
							array(
								'title' => '1:2',
								'value' => '50%',
							),
						),
						'on_change'   => array(
							'selector' => '.box-image-inner',
							'style'    => 'padding-top: {{ value }}',
						),
					),
					'image_width'     => array(
						'type'      => 'slider',
						'heading'   => esc_html__( 'Width', 'customize-post-categories-for-ux-builder' ),
						'unit'      => '%',
						'default'   => 100,
						'max'       => 100,
						'min'       => 0,
						'on_change' => array(
							'selector' => '.box-image',
							'style'    => 'width: {{ value }}%',
						),
					),

					'image_radius'    => array(
						'type'      => 'slider',
						'heading'   => esc_html__( 'Radius', 'customize-post-categories-for-ux-builder' ),
						'unit'      => '%',
						'default'   => 0,
						'max'       => 100,
						'min'       => 0,
						'on_change' => array(
							'selector' => '.box-image-inner',
							'style'    => 'border-radius: {{ value }}%',
						),
					),

					'image_size'      => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Size', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							''          => 'Default',
							'large'     => 'Large',
							'medium'    => 'Medium',
							'thumbnail' => 'Thumbnail',
							'original'  => 'Original',
						),
					),

					'image_overlay'   => array(
						'type'      => 'colorpicker',
						'heading'   => esc_html__( 'Overlay', 'customize-post-categories-for-ux-builder' ),
						'default'   => '',
						'alpha'     => true,
						'format'    => 'rgb',
						'position'  => 'bottom right',
						'on_change' => array(
							'selector' => '.overlay',
							'style'    => 'background-color: {{ value }}',
						),
					),

					'image_hover'     => array(
						'type'      => 'select',
						'heading'   => esc_html__( 'Hover', 'customize-post-categories-for-ux-builder' ),
						'default'   => '',
						'options'   => $this->get_image_hover_options(),
						'on_change' => array(
							'selector' => '.image-cover',
							'class'    => 'image-{{ value }}',
						),
					),
					'image_hover_alt' => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Hover Alt', 'customize-post-categories-for-ux-builder' ),
						'default'    => '',
						'conditions' => 'image_hover',
						'options'    => $this->get_image_hover_options(),
						'on_change'  => array(
							'selector' => '.image-cover',
							'class'    => 'image-{{ value }}',
						),
					),
				),
			),

			'text_options'     => array(
				'type'    => 'group',
				'heading' => esc_html__( 'Text', 'customize-post-categories-for-ux-builder' ),
				'options' => array(
					'text_pos'     => array(
						'type'       => 'select',
						'heading'    => esc_html__( 'Position', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'style === "vertical" || style === "shade" || style === "overlay"',
						'default'    => 'bottom',
						'options'    => array(
							'top'    => 'Top',
							'middle' => 'Middle',
							'bottom' => 'Bottom',
						),
						'on_change'  => array(
							'selector' => '.box',
							'class'    => 'box-text-{{ value }}',
						),
					),

					'text_align'   => array(
						'type'      => 'radio-buttons',
						'heading'   => esc_html__( 'Align', 'customize-post-categories-for-ux-builder' ),
						'default'   => 'center',
						'options'   => $this->text_align_options(),
						'on_change' => array(
							'selector' => '.box-text',
							'class'    => 'text-{{ value }}',
						),
					),

					'text_size'    => array(
						'type'      => 'radio-buttons',
						'heading'   => esc_html__( 'Size', 'customize-post-categories-for-ux-builder' ),
						'default'   => 'medium',
						'options'   => array(
							'xsmall' => array( 'title' => 'XS' ),
							'small'  => array( 'title' => 'S' ),
							'medium' => array( 'title' => 'M' ),
							'large'  => array( 'title' => 'L' ),
							'xlarge' => array( 'title' => 'XL' ),
						),
						'on_change' => array(
							'selector' => '.box-text',
							'class'    => 'is-{{ value }}',
						),
					),

					'text_hover'   => array(
						'type'    => 'select',
						'heading' => esc_html__( 'Hover', 'customize-post-categories-for-ux-builder' ),
						'default' => '',
						'options' => array(
							''         => 'None',
							'fade-out' => 'Fade Out',
							'slide'    => 'Slide In',
							'zoom'     => 'Zoom Out',
							'zoom-in'  => 'Zoom In',
							'blur'     => 'Blur In',
							'bounce'   => 'Bounce',
							'invert'   => 'Invert',
						),
					),

					'text_bg'      => array(
						'type'      => 'colorpicker',
						'heading'   => esc_html__( 'Bg Color', 'customize-post-categories-for-ux-builder' ),
						'default'   => '',
						'alpha'     => true,
						'format'    => 'rgb',
						'position'  => 'bottom right',
						'on_change' => array(
							'selector' => '.box-text',
							'style'    => 'background-color:{{ value }}',
						),
					),

					'text_color'   => array(
						'type'       => 'radio-buttons',
						'heading'    => esc_html__( 'Color', 'customize-post-categories-for-ux-builder' ),
						'conditions' => 'style !== "shade" && style !== "overlay"',
						'default'    => 'light',
						'options'    => array(
							'light' => array( 'title' => 'Dark' ),
							'dark'  => array( 'title' => 'Light' ),
						),
					),
					'text_padding' => array(
						'type'       => 'margins',
						'heading'    => esc_html__( 'Padding', 'customize-post-categories-for-ux-builder' ),
						'value'      => '',
						'full_width' => true,
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'on_change'  => array(
							'selector' => '.box-text',
							'style'    => 'padding: {{ value }}',
						),
					),
				),
			),
			'advanced_options' => $this->advanced_options(),
		);
		$posts_by_category_options = array_merge( $options, $box_styles );
		return $posts_by_category_options;
	}

	public function get_archive_info( $type = 'shop' ) {
		$types = array(
			'category' => array(
				'type'    => 'category',
				'txt-all' => esc_html__( 'All posts categories templates', 'customize-post-categories-for-ux-builder' ),
			),
			'tag'      => array(
				'type'    => 'tag',
				'txt-all' => esc_html__( 'All posts tags templates', 'customize-post-categories-for-ux-builder' ),
			),

		);

		return isset( $types[ $type ] ) ? $types[ $type ] : false;
	}

	public function get_template_info( $type ) {
		$templates = array(
			'category' => array(
				'name'       => esc_html__( 'Posts Categories Templates', 'customize-post-categories-for-ux-builder' ),
				'class'      => 'category' === $type ? 'nav-tab-active' : '',
				'query_args' => array( 'page' => 'categories-uxb-templates' ),
			),
			'tag'      => array(
				'name'       => esc_html__( 'Posts Tags templates', 'customize-post-categories-for-ux-builder' ),
				'class'      => 'tag' === $type ? 'nav-tab-active' : '',
				'query_args' => array(
					'page' => 'categories-uxb-templates',
					'type' => 'tag',
				),
			),

		);
		return $templates;
	}

	public function get_nav_tab_html( $type ) {
		$templates = $this->get_template_info( $type );
		$html      = '<nav class="nav-tab-wrapper">';
		foreach ( $templates as $key => $template ) {
			$html .= '<a title="' . esc_attr( $template['name'] ) . '" href="' . esc_url( add_query_arg( $template['query_args'], admin_url( 'edit.php' ) ) ) . '" class="nav-tab ' . esc_attr( $template['class'] ) . '">' . esc_html( $template['name'] ) . '</a>';
		}
		$html .= '</nav>';
		return $html;
	}

	public function add_classess_header_for_template( $page_attributes = 'default' ) {
		switch ( $page_attributes ) {
			case 'fullwidth-transparent-header':
			case 'fullwidth-transparent-header-light-text':
				add_filter( 'flatsome_header_class', array( $this, 'fullwidth_transparent_header_custom' ) );
				break;
			case 'fullwidth-header-on-scroll':
				add_filter( 'flatsome_header_class', array( $this, 'fullwidth_header_scroll_custom' ) );
				break;
			default:
				break;
		}
	}
	public function fullwidth_transparent_header_custom( $classes ) {
		$classes[] = 'transparent has-transparent';
		return $classes;
	}
	public function fullwidth_header_scroll_custom( $classes ) {
		$classes[] = 'show-on-scroll';
		return $classes;
	}
}

if ( ! function_exists( 'postCategoryUXB' ) ) {
	function postCategoryUXB() {
		return Customize_Post_Categories_For_Ux_Builder_Function::instance();
	}
}
