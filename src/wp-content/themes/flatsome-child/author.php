<?php
/**
 * The blog template file.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */

get_header();

$author_id = get_queried_object_id();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$posts_per_page = get_option('posts_per_page');

$args = array(
    'author' => $author_id,
    'posts_per_page' => $posts_per_page,
    'paged' => $paged,
    'post_status' => 'publish',
    'fields' => 'ids'
);
$query = new WP_Query($args);
$post_ids = implode(',', $query->posts);

$author_extra = get_field('field_6669799a7fe6d', 'user_' . $author_id);
$author_name = get_the_author_meta('display_name', $author_id);
$pagination_links = paginate_links(array(
    'total' => $query->max_num_pages,
));

$template = <<<EOF
[section padding="0px"]
[row style="small"]
[col class="pb-0"]
[ux_html]
[c_breadcrumbs]
[/ux_html]
[/col]
[/row]
[/section]
[section label="c-author" class="c-author"]
[row style="small"]
[col span__sm="12" class="pb-0"]
[title text="$author_name" tag_name="h1" class="a-heading"]
[ux_html class="a-author-description"]
$author_extra
[/ux_html]
[gap height="10px"]
<b class='uppercase h3'> BÀI VIẾT CỦA $author_name </b>
[gap height="10px"]
[ux_html]
<div id="at_posts_related">
EOF;

if (!empty($post_ids)) {
    $template .= <<<EOF
[blog_posts type="row" columns__sm="2" columns__md="2" ids="$post_ids" show_date="false" excerpt="false" excerpt_length="10" comments="false" text_align="left"]
EOF;
} else {
    $template .= <<<EOF
<p>Chưa có bài viết nào</p>
EOF;
}

$template .= <<<EOF
</div>
[/ux_html]
[gap height="20px"]
<div class="c-pagination">$pagination_links</div>
[/col]
[/row]
[/section]
EOF;

echo do_shortcode($template);

?>

<style>
    .section-title span {
        text-transform: uppercase !important;
        border-bottom: 0;
    }
    .c-pagination {
        margin-bottom: 1rem;
        display: flex;
        justify-content: center;
    }
    .c-pagination .current {
        border-color: #21487d;
        background-color: #21487d;
        color: #fff;
    }
    .c-pagination a,
    .c-pagination span {
        margin-left: 1rem;
        border: 2px solid;
        border-radius: 99px;
        display: block;
        font-size: 1.1em;
        font-weight: bolder;
        height: 2.25em;
        line-height: 2em;
        width: 2.25em;
        text-align: center;
        transition: all .3s;
        vertical-align: top;
    }
    .c-pagination a:first-child,
    .c-pagination span:first-child {
        margin-left: 0rem;
    }
    .c-pagination .next,
    .c-pagination .prev {
        display: none;
    }
</style>

<?php get_footer(); ?>