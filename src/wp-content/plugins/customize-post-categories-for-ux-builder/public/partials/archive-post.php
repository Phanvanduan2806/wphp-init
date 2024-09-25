<div class="col <?php echo esc_attr( implode( ' ', $col_class ) ); ?>" <?php echo esc_attr( $animate ); ?>>
	<div class="col-inner">

		<div class="box <?php echo esc_attr( $classes ); ?> box-blog-post has-hover">
			<?php if ( has_post_thumbnail() ) { ?>
				<div class="box-image" <?php echo get_shortcode_inline_css( $css_args_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="plain">
						<div class="<?php echo esc_attr( $classes_image ); ?>" <?php echo get_shortcode_inline_css( $css_image_height ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php the_post_thumbnail( $image_size, array( 'alt' => esc_html( get_the_title() ) ) ); ?>
							<?php
							if ( $image_overlay ) {
								?>
								<div class="overlay" style="background-color: <?php echo esc_attr( $image_overlay ); ?>"></div><?php } ?>
							<?php
							if ( 'shade' === $style ) {
								?>
								<div class="shade"></div><?php } ?>
						</div>
					</a>
					<?php if ( $post_icon && get_post_format() ) { ?>
						<div class="absolute no-click x50 y50 md-x50 md-y50 lg-x50 lg-y50">
							<div class="overlay-icon">
								<i class="icon-play"></i>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="box-text <?php echo esc_attr( $classes_text ); ?>" <?php echo get_shortcode_inline_css( $css_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<div class="box-text-inner blog-post-inner">
					<?php do_action( 'flatsome_blog_post_before' ); ?>
					<?php if ( 'false' !== $show_category ) { ?>
						<p class="cat-label <?php echo 'label' === $show_category ? 'tag-label' : ''; ?> is-xxsmall op-7 uppercase">
							<?php
							$list_cats = array();
							foreach ( get_the_category() as $archive_cat ) {
								$list_cats[] = $archive_cat->cat_name;
							}
							echo esc_html( implode( ', ', $list_cats ) );
							?>
						</p>
					<?php } ?>
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="plain post-archie-uxb-link-title">
						<h2 class="post-title is-<?php echo esc_attr( $title_size ); ?> <?php echo esc_attr( $title_style ); ?>"><?php echo esc_html( get_the_title() ); ?></h2>
					</a>
					<?php
					if ( ( ! has_post_thumbnail() && 'false' != $show_date ) || 'text' === $show_date ) {
						?>
						<div class="post-meta is-small op-8"><?php echo esc_html( get_the_date() ); ?></div><?php } ?>
					<div class="is-divider"></div>
					<?php if ( 'false' !== $show_excerpt ) { ?>
						<p class="from_the_blog_excerpt <?php echo 'visible' !== $show_excerpt ? 'show-on-hover hover-' . esc_attr( $show_excerpt ) : ''; ?>">
							<?php
								$the_excerpt  = get_the_excerpt();
								$excerpt_more = apply_filters( 'excerpt_more', ' [...]' );
								echo flatsome_string_limit_words( $the_excerpt, $excerpt_length ) . $excerpt_more; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</p>
					<?php } ?>
					<?php if ( 'true' === $comments && comments_open() && '0' != get_comments_number() ) { ?>
						<p class="from_the_blog_comments uppercase is-xsmall">
							<?php
							$comments_number = get_comments_number( get_the_ID() );

							printf(
								_n( '%s Comment', '%s Comments', $comments_number, 'flatsome' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.MissingTranslatorsComment
								number_format_i18n( $comments_number ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							)
							?>
						</p>
					<?php } ?>

					<?php if ( $readmore ) { ?>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="plain">
							<button class="button <?php echo esc_attr( $readmore_color ); ?> is-<?php echo esc_attr( $readmore_style ); ?> is-<?php echo esc_attr( $readmore_size ); ?> mb-0">
								<?php echo esc_html( $readmore ); ?>
							</button>
						</a>
					<?php } ?>
					<?php do_action( 'flatsome_blog_post_after' ); ?>
				</div>
			</div>
			<?php if ( has_post_thumbnail() && ( 'badge' === $show_date || 'true' === $show_date ) ) { ?>
				<?php
				if ( ! $badge_style ) {
					$badge_style = get_theme_mod( 'blog_badge_style', 'outline' );}
				?>
				<a href="<?php echo esc_url( get_day_link( false, get_the_time( 'm' ), get_the_time( 'd' ) ) ); ?>">
					<div class="badge absolute top post-date badge-<?php echo esc_attr( $badge_style ); ?>">
						<div class="badge-inner">
							<span class="post-date-day"><?php echo esc_html( get_the_time( 'd', get_the_ID() ) ); ?></span><br>
							<span class="post-date-month is-xsmall"><?php echo esc_html( get_the_time( 'M', get_the_ID() ) ); ?></span>
						</div>
					</div>
				</a>
			<?php } ?>
		</div>

	</div>
</div>
