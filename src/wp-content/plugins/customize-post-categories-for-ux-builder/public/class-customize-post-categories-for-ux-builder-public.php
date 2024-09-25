<?php

class Customize_Post_Categories_For_Ux_Builder_Public {


	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$shortcodes        = array(
			'post_cat_uxb_thumbnail'     => 'archive_thumbnail',
			'post_cat_uxb_subcategories' => 'archive_subcategories',
			'post_cat_uxb_name'          => 'archive_title',
			'post_cat_uxb_desc'          => 'archive_description',
			'post_cat_uxb_list'          => 'archive_posts',
		);

		foreach ( $shortcodes as $shortcode => $shorecode_name_function ) {
			add_shortcode( $shortcode, array( &$this, $shorecode_name_function ) );
		}
	}


	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/frontend.css', array(), $this->version, 'all' );
	}

	public function archive_thumbnail( $atts, $content = null ) {
		// @codingStandardsIgnoreLine
		extract(
			shortcode_atts(
				array(
					'_id'                 => 'image_' . rand(),
					'class'               => '',
					'visibility'          => '',
					'id'                  => '',
					'org_img'             => '',
					'caption'             => '',
					'animate'             => '',
					'animate_delay'       => '',
					'lightbox'            => '',
					'lightbox_image_size' => 'large',
					'lightbox_caption'    => '',
					'height'              => '',
					'image_overlay'       => '',
					'image_hover'         => '',
					'image_hover_alt'     => '',
					'image_size'          => 'large',
					'icon'                => '',
					'width'               => '',
					'margin'              => '',
					'position_x'          => '',
					'position_x__sm'      => '',
					'position_x__md'      => '',
					'position_y'          => '',
					'position_y__sm'      => '',
					'position_y__md'      => '',
					'depth'               => '',
					'parallax'            => '',
					'depth_hover'         => '',
					'link'                => '',
					'target'              => '_self',
					'rel'                 => '',
				),
				$atts
			)
		);
		$archive = postCategoryUXB()->get_post_archive();
		if ( isset( $archive['taxonomy'] ) && 'category' !== $archive['taxonomy'] ) {
			return;
		}
		$term_id = isset( $archive['term_id'] ) ? $archive['term_id'] : 0;
		$id      = get_term_meta( $term_id, 'post_cat_uxb_thumbnail_id', true );
		if ( 0 == $id ) {
			return;
		}
		$classes = array();
		if ( $class ) {
			$classes[] = $class;
		}
		if ( $visibility ) {
			$classes[] = $visibility;
		}

		$classes_inner = array( 'img-inner' );
		$classes_img   = array();
		$image_meta    = wp_prepare_attachment_for_js( $id );
		$link_atts     = array(
			'target' => $target,
			'rel'    => array( $rel ),
		);

		if ( is_numeric( $id ) ) {
			if ( ! $org_img ) {
				$org_img = wp_get_attachment_image_src( $id, $lightbox_image_size );
				$org_img = $org_img ? $org_img[0] : '';
			}
			if ( $caption && 'true' === $caption ) {
				$caption = is_array( $image_meta ) ? $image_meta['caption'] : '';
			}
		} else {
			if ( ! $org_img ) {
				$org_img = $id;
			}
		}

		// If caption is enabled.
		$link_start = '';
		$link_end   = '';
		$link_class = '';

		if ( $link ) {
			if ( strpos( $link, 'watch?v=' ) !== false ) {
				$icon       = 'icon-play';
				$link_class = 'open-video';
				if ( ! $image_overlay ) {
					$image_overlay = 'rgba(0,0,0,.2)';
				}
			}
			$link_start = '<a class="' . $link_class . '" href="' . $link . '"' . flatsome_parse_target_rel( $link_atts ) . '>';
			$link_end   = '</a>';
		} elseif ( $lightbox ) {
			$title      = $lightbox_caption ? $image_meta['caption'] : '';
			$link_start = '<a class="image-lightbox lightbox-gallery" title="' . esc_attr( $title ) . '" href="' . $org_img . '">';
			$link_end   = '</a>';
		}

		// Set positions
		if ( function_exists( 'ux_builder_is_active' ) && ux_builder_is_active() ) {
			// Do not add positions if builder is active.
			// They will be set by the onChange handler.
		} else {
			$classes[] = flatsome_position_classes( 'x', $position_x, $position_x__sm, $position_x__md );
			$classes[] = flatsome_position_classes( 'y', $position_y, $position_y__sm, $position_y__md );
		}

		if ( $image_hover ) {
			$classes_inner[] = 'image-' . $image_hover;
		}
		if ( $image_hover_alt ) {
			$classes_inner[] = 'image-' . $image_hover_alt;
		}
		if ( $height ) {
			$classes_inner[] = 'image-cover';
		}
		if ( $depth ) {
			$classes_inner[] = 'box-shadow-' . $depth;
		}
		if ( $depth_hover ) {
			$classes_inner[] = 'box-shadow-' . $depth_hover . '-hover';
		}

		// Add Parallax Attribute.
		if ( $parallax ) {
			$parallax = 'data-parallax-fade="true" data-parallax="' . $parallax . '"';
		}

		// Set image height.
		$css_image_height = array(
			array(
				'attribute' => 'padding-top',
				'value'     => $height,
			),
			array(
				'attribute' => 'margin',
				'value'     => $margin,
			),
		);

		$classes       = implode( ' ', $classes );
		$classes_inner = implode( ' ', $classes_inner );
		$classes_img   = implode( ' ', $classes_img );

		ob_start();
		?>
		<div class="img has-hover <?php echo esc_attr( $classes ); ?>" id="<?php echo esc_attr( $_id ); ?>">
			<?php echo $link_start; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php
			if ( $parallax ) {
				echo '<div ' . $parallax . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
			<?php
			if ( $animate ) {
				echo '<div data-animate="' . esc_attr( $animate ) . '">';
			}
			?>
			<div class="<?php echo $classes_inner; ?> dark" <?php echo get_shortcode_inline_css( $css_image_height ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo flatsome_get_image( $id, $image_size, $caption ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $image_overlay ) { ?>
					<div class="overlay" style="background-color: <?php echo esc_attr( $image_overlay ); ?>"></div>
				<?php } ?>
				<?php if ( $icon ) { ?>
					<div class="absolute no-click x50 y50 md-x50 md-y50 lg-x50 lg-y50 text-shadow-2">
						<div class="overlay-icon">
							<i class="icon-play"></i>
						</div>
					</div>
				<?php } ?>
	
				<?php if ( $caption ) { ?>
					<div class="caption"><?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php } ?>
			</div>
			<?php
			if ( $animate ) {
				echo '</div>';}
			?>
			<?php
			if ( $parallax ) {
				echo '</div>';}
			?>
			<?php echo $link_end; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php
			$args = array(
				'width' => array(
					'selector' => '',
					'property' => 'width',
					'unit'     => '%',
				),
			);
			echo ux_builder_element_style_tag( $_id, $args, $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public function archive_subcategories( $atts, $content = null ) {
		$sliderrandomid = rand();
		// @codingStandardsIgnoreLine
		extract(
			shortcode_atts(
				array(
					'number'              => null,
					'_id'                 => 'cats-' . rand(),
					'title'               => '',
					'cat'                 => '',
					'orderby'             => 'menu_order',
					'order'               => 'ASC',
					'hide_empty'          => 1,
					'offset'              => '',
					'show_count'          => 'true',
					'class'               => '',
					'visibility'          => '',
					'style'               => 'badge',
					'columns'             => '4',
					'columns__sm'         => '',
					'columns__md'         => '',
					'col_spacing'         => 'small',
					'type'                => 'slider',
					'width'               => '',
					'grid'                => '1',
					'grid_height'         => '600px',
					'grid_height__md'     => '500px',
					'grid_height__sm'     => '400px',
					'slider_nav_style'    => 'reveal',
					'slider_nav_color'    => '',
					'slider_nav_position' => '',
					'slider_bullets'      => 'false',
					'slider_arrows'       => 'true',
					'auto_slide'          => 'false',
					'infinitive'          => 'true',
					'depth'               => '',
					'depth_hover'         => '',
					'animate'             => '',
					'text_pos'            => '',
					'text_padding'        => '',
					'text_bg'             => '',
					'text_color'          => '',
					'text_hover'          => '',
					'text_align'          => 'center',
					'text_size'           => '',
					'image_size'          => '',
					'image_mask'          => '',
					'image_width'         => '',
					'image_hover'         => '',
					'image_hover_alt'     => '',
					'image_radius'        => '',
					'image_height'        => '',
					'image_overlay'       => '',
					'bg_overlay'          => '#000',
				),
				$atts
			)
		);
		$archive        = postCategoryUXB()->get_post_archive();
		$hide_empty     = ( true == $hide_empty || 1 == $hide_empty ) ? 1 : 0;
		$sub_categories = postCategoryUXB()->get_all_subcategory( $archive['term_id'] );
		if ( empty( $sub_categories ) ) {
			return;
		}

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'offset'     => $offset,
			'hide_empty' => $hide_empty,
			'include'    => $sub_categories,
			'pad_counts' => true,
			'child_of'   => '',
		);

		ob_start();

		$product_categories = get_terms( 'category', $args );

		if ( ! empty( $number ) ) {
			$product_categories = array_slice( $product_categories, 0, $number );
		}

		$classes_box   = array( 'box', 'box-category', 'has-hover' );
		$classes_image = array();
		$classes_text  = array();

		// Create Grid
		if ( 'grid' === $type ) {
			$columns      = 0;
			$current_grid = 0;
			$grid         = flatsome_get_grid( $grid );
			$grid_total   = count( $grid );
			flatsome_get_grid_height( $grid_height, $_id );
		}

		// Add Animations
		if ( $animate ) {
			$animate = 'data-animate="' . $animate . '"';}

		// Set box style
		if ( $style ) {
			$classes_box[] = 'box-' . $style;
		}
		if ( 'overlay' === $style ) {
			$classes_box[] = 'dark';
		}
		if ( 'shade' === $style ) {
			$classes_box[] = 'dark';
		}
		if ( 'badge' === $style ) {
			$classes_box[] = 'hover-dark';
		}
		if ( $text_pos ) {
			$classes_box[] = 'box-text-' . $text_pos;
		}
		if ( 'overlay' === $style && ! $image_overlay ) {
			$image_overlay = true;
		}

		// Set image styles
		if ( $image_hover ) {
			$classes_image[] = 'image-' . $image_hover;
		}
		if ( $image_hover_alt ) {
			$classes_image[] = 'image-' . $image_hover_alt;
		}
		if ( $image_height ) {
			$classes_image[] = 'image-cover';
		}

		// Text classes
		if ( $text_hover ) {
			$classes_text[] = 'show-on-hover hover-' . $text_hover;
		}
		if ( $text_align ) {
			$classes_text[] = 'text-' . $text_align;
		}
		if ( $text_size ) {
			$classes_text[] = 'is-' . $text_size;
		}
		if ( 'dark' === $text_color ) {
			$classes_text[] = 'dark';
		}

		$css_args_img = array(
			array(
				'attribute' => 'border-radius',
				'value'     => $image_radius,
				'unit'      => '%',
			),
			array(
				'attribute' => 'width',
				'value'     => $image_width,
				'unit'      => '%',
			),
		);

		$css_image_height = array(
			array(
				'attribute' => 'padding-top',
				'value'     => $image_height,
			),
		);

		$css_args = array(
			array(
				'attribute' => 'background-color',
				'value'     => $text_bg,
			),
			array(
				'attribute' => 'padding',
				'value'     => $text_padding,
			),
		);

		// Repeater options
		$repeater['id']                  = $_id;
		$repeater['class']               = $class;
		$repeater['visibility']          = $visibility;
		$repeater['type']                = $type;
		$repeater['style']               = $style;
		$repeater['format']              = $image_height;
		$repeater['slider_style']        = $slider_nav_style;
		$repeater['slider_nav_color']    = $slider_nav_color;
		$repeater['slider_nav_position'] = $slider_nav_position;
		$repeater['slider_bullets']      = $slider_bullets;
		$repeater['auto_slide']          = $auto_slide;
		$repeater['infinitive']          = $infinitive;
		$repeater['row_spacing']         = $col_spacing;
		$repeater['row_width']           = $width;
		$repeater['columns']             = $columns;
		$repeater['columns__sm']         = $columns__sm;
		$repeater['columns__md']         = $columns__md;
		$repeater['depth']               = $depth;
		$repeater['depth_hover']         = $depth_hover;

		get_flatsome_repeater_start( $repeater );

		if ( $product_categories ) {
			foreach ( $product_categories as $category ) {

				$classes_col = array( 'product-category', 'col' );

				$thumbnail_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );

				if ( $image_size ) {
					$thumbnail_size = $image_size;
				}

				if ( 'grid' === $type ) {
					if ( $grid_total > $current_grid ) {
						$current_grid++;
					}
					$current       = $current_grid - 1;
					$classes_col[] = 'grid-col';
					if ( $grid[ $current ]['height'] ) {
						$classes_col[] = 'grid-col-' . $grid[ $current ]['height'];
					}
					if ( $grid[ $current ]['span'] ) {
						$classes_col[] = 'large-' . $grid[ $current ]['span'];
					}
					if ( $grid[ $current ]['md'] ) {
						$classes_col[] = 'medium-' . $grid[ $current ]['md'];
					}

					// Set image size
					if ( 'large' === $grid[ $current ]['size'] ) {
						$thumbnail_size = 'large';
					}
					if ( 'medium' === $grid[ $current ]['size'] ) {
						$thumbnail_size = 'medium';
					}
				}

				$thumbnail_id = get_term_meta( $category->term_id, 'post_cat_uxb_thumbnail_id', true );

				if ( $thumbnail_id ) {
					$image = wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size );
					$image = $image ? $image[0] : post_category_uxb_get_image_default();
				} else {
					$image = post_category_uxb_get_image_default();
				}

				?>
				<div class="<?php echo esc_attr( implode( ' ', $classes_col ) ); ?>" <?php echo esc_attr( $animate ); ?>>
			<div class="col-inner">
				<?php do_action( 'woocommerce_before_subcategory', $category ); ?>
				<div class="<?php echo esc_attr( implode( ' ', $classes_box ) ); ?> ">
				<div class="box-image" <?php echo get_shortcode_inline_css( $css_args_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				  <div class="<?php echo esc_attr( implode( ' ', $classes_image ) ); ?>" <?php echo get_shortcode_inline_css( $css_image_height ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				  <?php echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="300" height="300" />'; ?>
				  <?php
					if ( $image_overlay ) {
						?>
						<div class="overlay" style="background-color: <?php echo esc_attr( $image_overlay ); ?>"></div><?php } ?>
				  <?php
					if ( 'shade' === $style ) {
						?>
						<div class="shade"></div><?php } ?>
				  </div>
				</div>
				<div class="box-text <?php echo esc_attr( implode( ' ', $classes_text ) ); ?>" <?php echo get_shortcode_inline_css( $css_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				  <div class="box-text-inner">
					  <h5 class="uppercase header-title">
							  <?php echo esc_html( $category->name ); ?>
					  </h5>
					  <?php if ( $show_count ) { ?>
					  <p class="is-xsmall uppercase count 
							<?php
							if ( 'overlay' === $style ) {
								echo 'show-on-hover hover-reveal reveal-small';}
							?>
						">
							<?php
							if ( $category->count > 0 ) {
								echo apply_filters( 'woocommerce_subcategory_count_html', $category->count . ' ' . ( $category->count > 1 ? __( 'Posts', 'customize-post-categories-for-ux-builder' ) : __( 'Post', 'customize-post-categories-for-ux-builder' ) ), $category );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
					  </p>
					  <?php } ?>
						  <?php
							do_action( 'woocommerce_after_subcategory_title', $category );
							?>

				  </div>
				</div>
				</div>
				<?php do_action( 'woocommerce_after_subcategory', $category ); ?>
			</div>
			</div>
				<?php
			}
		}
		woocommerce_reset_loop();

		get_flatsome_repeater_end( $repeater );

		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function archive_title( $atts, $content = null ) {
        // @codingStandardsIgnoreLine
		extract(
			shortcode_atts(
				array(
					'style'      => 'default',
					'bg_color'   => '#446084',
					'title'      => 'yes',
					'breadcrumb' => 'yes',
					'class'      => '',
					'visibility' => '',
				),
				$atts
			)
		);
		ob_start();
		$classes         = array();
		$classes[]       = 'featured' === $style ? 'archive-header-featured dark featured-title' : 'archive-header-default';
		$header_style    = 'featured' === $style ? '--tooltip-bg-color: ' . $bg_color : '';
		$show_title      = 'yes' === $title ? '' : 'archive-uxb-hidden';
		$show_breadcrumb = 'yes' === $breadcrumb ? '' : 'archive-uxb-hidden';
		$archive         = postCategoryUXB()->get_post_archive();
		$archive_name    = isset( $archive['name'] ) ? $archive['name'] : get_the_title();
		if ( ! empty( $visibility ) ) {
			$classes[] = $visibility;
		}
		if ( ! empty( $class ) ) {
			echo '<div class="' . esc_attr( $class ) . '">';
		}
		?>
		<div style="<?php echo esc_attr( $header_style ); ?>" class="shop-page-title category-page-title page-title <?php echo esc_attr( implode( ' ', $classes ) ); ?> <?php flatsome_header_title_classes(); ?>">
			<?php if ( 'featured' === $style ) : ?>
				<div class="page-title-bg fill">
					<div class="title-bg fill bg-fill" data-parallax-fade="true" data-parallax="-2" data-parallax-background data-parallax-container=".page-title"></div>
					<div class="title-overlay fill"></div>
				</div>
			<?php endif; ?>
			<div class="page-title-inner flex-row container medium-flex-wrap flex-has-center">
				<div class="flex-col">
					&nbsp;
				</div>
				<div class="flex-col flex-center text-center">
					<h1 class="uppercase archive-title-text <?php echo esc_attr( $show_title ); ?>"><?php echo esc_html( $archive_name ); ?></h1>
					<div class="archive-title-breadcrumb <?php echo esc_attr( $show_breadcrumb ); ?>">
						<?php
						flatsome_breadcrumb( 'page-breadcrumbs' );
						?>
					</div>
				</div>
				<div class="flex-col flex-right text-right medium-text-center form-flat"></div>
			</div>
		</div>
		<?php
		if ( ! empty( $class ) ) {
			echo '</div>';
		}
		return ob_get_clean();
	}

	public function archive_description( $atts, $content = null ) {
		// @codingStandardsIgnoreLine
        extract(
			shortcode_atts(
				array(
					'width'           => '',
					'font_size'       => '',
					'font_size__sm'   => '',
					'font_size__md'   => '',
					'line_height'     => '',
					'line_height__sm' => '',
					'line_height__md' => '',
					'text_align'      => '',
					'text_align__sm'  => '',
					'text_align__md'  => '',
					'text_color'      => '',
					'class'           => '',
					'visibility'      => '',
				),
				$atts
			)
		);

		$archive = postCategoryUXB()->get_post_archive();

		if ( ! is_array( $archive ) ) {
			return;
		}

		$desciption = ! empty( $archive['description'] ) ? $archive['description'] : '';
		$elm_id     = 'post-archive-des-' . wp_rand();
		$classes    = ! empty( $width ) ? array( $width ) : array( 'container' );

		if ( ! empty( $class ) ) {
			$classes[] = $class;
		}

		if ( ! empty( $visibility ) ) {
			$classes[] = $visibility;
		}

		ob_start();
		?>
		<div id="<?php echo esc_attr( $elm_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<p><?php echo do_shortcode( $desciption ); ?></p>
			<?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ux_builder_element_style_tag(
				esc_attr( $elm_id ),
				array(
					'font_size'   => array(
						'property' => 'font-size',
						'unit'     => 'rem',
					),
					'line_height' => array(
						'property' => 'line-height',
					),
					'text_align'  => array(
						'property' => 'text-align',
					),
					'text_color'  => array(
						'selector' => ', > *',
						'property' => 'color',
					),
				),
				$atts
			);
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	public function archive_posts( $atts, $content = null, $tag = '' ) {
		$posts_per_page_default = get_option( 'posts_per_page' ) ? get_option( 'posts_per_page' ) : 10;
		// @codingStandardsIgnoreLine
        extract(
			shortcode_atts(
				array(
					'_id'                 => 'row-' . rand(),
					'style'               => 'default',
					'columns'             => '3',
					'columns__sm'         => '1',
					'columns__md'         => '',
					'col_spacing'         => '',
					'type'                => 'row',
					'width'               => '',
					'grid'                => '1',
					'grid_height'         => '600px',
					'grid_height__md'     => '500px',
					'grid_height__sm'     => '400px',
					'slider_nav_style'    => 'reveal',
					'slider_nav_position' => '',
					'slider_nav_color'    => '',
					'slider_bullets'      => 'false',
					'slider_arrows'       => 'true',
					'auto_slide'          => 'false',
					'infinitive'          => 'true',
					'depth'               => '',
					'depth_hover'         => '',
					'show_paginate'       => 'yes',
					'posts'               => $posts_per_page_default,
					'cat'                 => '',
					'category'            => '',
					'excerpt'             => 'visible',
					'excerpt_length'      => 15,
					'offset'              => '',
					'orderby'             => 'date',
					'order'               => 'DESC',
					'readmore'            => '',
					'readmore_color'      => '',
					'readmore_style'      => 'outline',
					'readmore_size'       => 'small',
					'post_icon'           => 'true',
					'comments'            => 'true',
					'show_date'           => 'badge',
					'badge_style'         => '',
					'show_category'       => 'false',
					'title_size'          => 'large',
					'title_style'         => '',
					'animate'             => '',
					'text_pos'            => 'bottom',
					'text_padding'        => '',
					'text_bg'             => '',
					'text_size'           => '',
					'text_color'          => '',
					'text_hover'          => '',
					'text_align'          => 'center',
					'image_size'          => 'medium',
					'image_width'         => '',
					'image_radius'        => '',
					'image_height'        => '56%',
					'image_hover'         => '',
					'image_hover_alt'     => '',
					'image_overlay'       => '',
					'image_depth'         => '',
					'image_depth_hover'   => '',
					'class'               => '',
					'visibility'          => '',

				),
				$atts
			)
		);

		$archive      = postCategoryUXB()->get_post_archive();
		$term_id      = isset( $archive['term_id'] ) ? $archive['term_id'] : 0;
		$archive_type = postCategoryUXB()->get_archive_page();

		ob_start();

		if ( 'hidden' === $visibility ) {
			return;
		}

		$classes       = array();
		$classes_image = array();
		$classes_text  = array();

		if ( 'text-overlay' === $style ) {
			$image_hover = 'zoom';
		}

		$style = str_replace( 'text-', '', $style );

		// Fix grids
		if ( 'grid' === $type ) {
			if ( ! $text_pos ) {
				$text_pos = 'center';
			}
			$columns      = 0;
			$current_grid = 0;
			$grid         = flatsome_get_grid( $grid );
			$grid_total   = count( $grid );
			flatsome_get_grid_height( $grid_height, $_id );
		}

		// Fix overlay
		if ( 'overlay' === $style && ! $image_overlay ) {
			$image_overlay = 'rgba(0,0,0,.25)';
		}

		// Set box style
		if ( $style ) {
			$classes[] = 'box-' . $style;
		}
		if ( 'overlay' === $style ) {
			$classes[] = 'dark';
		}
		if ( 'shade' === $style ) {
			$classes[] = 'dark';
		}
		if ( 'badge' === $style ) {
			$classes[] = 'hover-dark';
		}

		if ( $text_pos ) {
			$classes[] = 'box-text-' . $text_pos;
		}

		if ( $image_hover ) {
			$classes_image[] = 'image-' . $image_hover;
		}
		if ( $image_hover_alt ) {
			$classes_image[] = 'image-' . $image_hover_alt;
		}
		if ( $image_height ) {
			$classes_image[] = 'image-cover';
		}

		// Text classes
		if ( $text_hover ) {
			$classes_text[] = 'show-on-hover hover-' . $text_hover;
		}
		if ( $text_align ) {
			$classes_text[] = 'text-' . $text_align;
		}
		if ( $text_size ) {
			$classes_text[] = 'is-' . $text_size;
		}
		if ( 'dark' === $text_color ) {
			$classes_text[] = 'dark';
		}

		$css_args_img = array(
			array(
				'attribute' => 'border-radius',
				'value'     => $image_radius,
				'unit'      => '%',
			),
			array(
				'attribute' => 'width',
				'value'     => $image_width,
				'unit'      => '%',
			),
		);

		$css_image_height = array(
			array(
				'attribute' => 'padding-top',
				'value'     => $image_height,
			),
		);

		$css_args = array(
			array(
				'attribute' => 'background-color',
				'value'     => $text_bg,
			),
			array(
				'attribute' => 'padding',
				'value'     => $text_padding,
			),
		);

		// Add Animations
		if ( $animate ) {
			$animate = 'data-animate="' . $animate . '"';
		}

		$classes_text  = implode( ' ', $classes_text );
		$classes_image = implode( ' ', $classes_image );
		$classes       = implode( ' ', $classes );

		// Repeater styles
		$repeater = array(
			'id'                  => $_id,
			'tag'                 => $tag,
			'type'                => $type,
			'class'               => $class,
			'visibility'          => $visibility,
			'style'               => $style,
			'slider_style'        => $slider_nav_style,
			'slider_nav_position' => $slider_nav_position,
			'slider_nav_color'    => $slider_nav_color,
			'slider_bullets'      => $slider_bullets,
			'auto_slide'          => $auto_slide,
			'infinitive'          => $infinitive,
			'row_spacing'         => $col_spacing,
			'row_width'           => $width,
			'columns'             => $columns,
			'columns__md'         => $columns__md,
			'columns__sm'         => $columns__sm,
			'depth'               => $depth,
			'depth_hover'         => $depth_hover,

		);
		$show_paginate = 'yes' === $show_paginate ? true : false;
		if ( $show_paginate ) {
			$posts = $posts_per_page_default;
		}

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$args = array(
			'post_status'         => 'publish',
			'post_type'           => 'post',
			'offset'              => $offset,
			'posts_per_page'      => $posts,
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
			'orderby'             => $orderby,
			'order'               => $order,
		);

		if ( 'category' === $archive_type['page'] ) {
			$args['cat'] = $term_id;
		} else {
			$args['tag_id'] = $term_id;
		}

		$result_posts = new WP_Query( $args );
		// Get repeater HTML.
		get_flatsome_repeater_start( $repeater );

		while ( $result_posts->have_posts() ) :
			$result_posts->the_post();
			$col_class    = array( 'post-item' );
			$show_excerpt = $excerpt;

			if ( get_post_format() == 'video' ) {
				$col_class[] = 'has-post-icon';
			}

			if ( 'grid' === $type ) {

				if ( $grid_total > $current_grid ) {
					$current_grid++;
				}

				$current     = $current_grid - 1;
				$col_class[] = 'grid-col';

				if ( $grid[ $current ]['height'] ) {
					$col_class[] = 'grid-col-' . $grid[ $current ]['height'];
				}
				if ( $grid[ $current ]['span'] ) {
					$col_class[] = 'large-' . $grid[ $current ]['span'];
				}
				if ( $grid[ $current ]['md'] ) {
					$col_class[] = 'medium-' . $grid[ $current ]['md'];
				}

				if ( $grid[ $current ]['size'] ) {
					$image_size = $grid[ $current ]['size'];
				}
				// Hide excerpt for small sizes
				if ( 'thumbnail' === $grid[ $current ]['size'] ) {
					$show_excerpt = 'false';
				}
			}

			require plugin_dir_path( __FILE__ ) . 'partials/archive-post.php';

		endwhile;
		wp_reset_query(); // @codingStandardsIgnoreLine
		get_flatsome_repeater_end( $atts );
		// paginate
		if ( $show_paginate ) {
			postCategoryUXB()->get_pagination_html( $result_posts, $paged );
		}
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	public function template_redirect() {
		$term_id  = postCategoryUXB()->get_term_id();
		$language = postCategoryUXB()->current_language();
		if ( postCategoryUXB()->is_post_archive( 'category' ) ) {
			$has_template = postCategoryUXB()->template_active_by_term_id( $term_id, $language, 'category' );
			if ( $has_template && $has_template['active'] ) {
				add_filter( 'template_include', array( $this, 'template_include' ), 9999 );
			}
		}
		if ( postCategoryUXB()->is_post_archive( 'tag' ) ) {
			$has_template = postCategoryUXB()->template_active_by_term_id( $term_id, $language, 'tag' );
			if ( $has_template && $has_template['active'] ) {
				add_filter( 'template_include', array( $this, 'template_include' ), 9999 );
			}
		}
	}

	public function template_include( $template ) {
		$template_redirect = plugin_dir_path( __FILE__ ) . 'partials/archive.php';
		if ( file_exists( $template_redirect ) ) {
			return $template_redirect;
		}
		return $template;
	}
}
