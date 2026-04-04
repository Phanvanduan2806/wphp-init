<?php
/**
 * The blog template file.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

get_header();

$title = '';
$content = '';

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
$title = esc_html($title);
$content = wp_kses_post($content);

$result = <<<EOF
[section class="dp-single pb-0" padding="60px"]
[dp_breadcrumbs]
[title text="$title" tag_name="h1" class="dp-heading"]
[row]
[col span="9" span__sm="12" span__md="12"]
[ux_html]
$content
[/ux_html]
[/col]
[col span="3" span__sm="12" span__md="6" sticky="true"]
[block id="sidebar"]
[/col]

[/row]
[/section]
[section padding="30px" class="pb-0"]
[row]
[col span="12" span__sm="12" span__md="12"]
[related_blog]

[/col]

[/row]
[/section]
EOF;

echo do_shortcode($result);

get_footer();
