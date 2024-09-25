<?php
/**
 * The blog template file.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

get_header();

// Initialize variables
$title = '';
$content = '';

// the_content and the_title
if ( have_posts() ) : 
    while ( have_posts() ) : the_post();
        ob_start();
        the_title();
        $title = ob_get_clean();

        ob_start();
        the_content();
        $content = ob_get_clean();
    endwhile;
endif;

// Escaping the output
$title = esc_html($title);
$content = wp_kses_post($content);

$result = <<<EOF
[section padding="0px"]

[row style="small"]

[col span__sm="12" class="pb-0"]

[ux_html]

[c_breadcrumbs]
[/ux_html]

[/col]
[/row]
[/section]
[section label="a-content" class="a-content"]
[title text="$title" tag_name="h1" class="a-heading"]
[row]

[col span__sm="12"]

[ux_html]

$content
[/ux_html]

[/col]

[/row]

[/section]
EOF;

echo do_shortcode($result);

get_footer();
