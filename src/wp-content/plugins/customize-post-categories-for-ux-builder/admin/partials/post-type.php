<?php
$page_attributes = get_post_meta( get_the_ID(), 'template_attribute', true ) ? get_post_meta( get_the_ID(), 'template_attribute', true ) : 'default';
postCategoryUXB()->add_classess_header_for_template( $page_attributes );
get_header();
?>
<?php
while ( have_posts() ) :
	the_post();
	switch ( $page_attributes ) {
		case 'fullwidth':
			?>
			<div id="content" role="main" class="content-area post-category-uxbuilder-fullwidth">
				<?php the_content(); ?>
			</div>
			<?php
			break;
		case 'fullwidth-transparent-header':
		case 'fullwidth-transparent-header-light-text':
		case 'fullwidth-header-on-scroll':
			echo '<div id="content" role="main">';
			the_content();
			echo '</div>';
			if ( 'fullwidth-transparent-header-light-text' === $page_attributes ) {
				echo '<script>jQuery("#masthead").addClass("nav-dark toggle-nav-dark");</script>';
			}
			break;
		case 'left-sidebar':
			?>
			<div id="content" class="blog-wrapper blog-archive page-wrapper">
				<div class="row row-large 
				<?php
				if ( flatsome_option( 'blog_layout_divider' ) ) {
					echo 'row-divided ';}
				?>
				">
					<div class="post-sidebar large-3 col">
						<?php flatsome_sticky_column_open( 'blog_sticky_sidebar' ); ?>
						<?php get_sidebar(); ?>
						<?php flatsome_sticky_column_close( 'blog_sticky_sidebar' ); ?>
					</div>
					<div class="large-9 col medium-col-first">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
			<?php
			break;
		case 'right-sidebar':
			?>
			<div id="content" class="blog-wrapper blog-archive page-wrapper">
				<div class="row row-large 
				<?php
				if ( flatsome_option( 'blog_layout_divider' ) ) {
					echo 'row-divided ';}
				?>
				">
					<div class="large-9 col">
						<?php the_content(); ?>
					</div>
					<div class="post-sidebar large-3 col">
						<?php flatsome_sticky_column_open( 'blog_sticky_sidebar' ); ?>
						<?php get_sidebar(); ?>
						<?php flatsome_sticky_column_close( 'blog_sticky_sidebar' ); ?>
					</div>
				</div>
			</div>
			<?php
			break;
		default:
			?>
			<div class="row page-wrapper">
				<div id="content" class="large-12 col" role="main">
					<div class="entry-content">
						<?php the_content(); ?>	
					</div>
				</div>
			</div>
			<?php
			break;
	}
endwhile;
?>

<?php get_footer(); ?>
