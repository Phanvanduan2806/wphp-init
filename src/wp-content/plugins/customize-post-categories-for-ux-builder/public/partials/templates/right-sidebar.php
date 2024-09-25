<div id="content" class="blog-wrapper blog-archive page-wrapper">
	<div class="row row-large <?php if ( flatsome_option( 'blog_layout_divider' ) ) {
		echo 'row-divided ';} ?>">
		<div class="large-9 col">
		<?php postCategoryUXB()->show_template_content( $content ); ?>
		</div>
		<div class="post-sidebar large-3 col">
			<?php flatsome_sticky_column_open( 'blog_sticky_sidebar' ); ?>
			<?php get_sidebar(); ?>
			<?php flatsome_sticky_column_close( 'blog_sticky_sidebar' ); ?>
		</div>
	</div>
</div>
