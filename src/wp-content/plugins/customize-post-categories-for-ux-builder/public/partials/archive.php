<?php
$term_id  = postCategoryUXB()->get_term_id();
$language = postCategoryUXB()->current_language();
if ( postCategoryUXB()->is_post_archive( 'category' ) ) {
	$archive_type = 'category';
} else {
	$archive_type = 'tag';
}
$result          = postCategoryUXB()->template_active_by_term_id( $term_id, $language, $archive_type );
$content         = isset( $result['template']['content'] ) ? $result['template']['content'] : '';
$template_id     = isset( $result['template']['postid'] ) ? $result['template']['postid'] : 0;
$page_attributes = get_post_meta( $template_id, 'template_attribute', true ) ? get_post_meta( $template_id, 'template_attribute', true ) : 'default';
postCategoryUXB()->add_classess_header_for_template( $page_attributes );
get_header();
require_once 'templates/' . $page_attributes . '.php';
get_footer();
