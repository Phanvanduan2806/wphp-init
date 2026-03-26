<?php
add_shortcode('c_search_posts', function ($atts, $content = null, $tag = '' ) {
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

//    if ( 'category' === $archive_type['page'] ) {
//        $args['cat'] = $term_id;
//    } else {
//        $args['tag_id'] = $term_id;
//    }
    $search_query = get_search_query();
    $args['s'] = $search_query;

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
        $plugin_file_path = WP_PLUGIN_DIR.'/customize-post-categories-for-ux-builder';
        require $plugin_file_path . '/public/partials/archive-post.php';

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
});