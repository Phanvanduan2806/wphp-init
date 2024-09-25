<div class="wrap">
<?php
	$template_info = postCategoryUXB()->get_template_info( $type );
	echo '<h1>' . esc_html__( 'Archive Templates', 'customize-post-categories-for-ux-builder' ) . '</h1>';
	echo postCategoryUXB()->get_nav_tab_html( $type );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</div>

<div class="wrap">
	<h1>
		<?php echo esc_html( $template_info[ $type ]['name'] ); ?><span class="title-count theme-count"><?php echo esc_html( count( $templates ) ); ?></span>
		<a href="<?php echo esc_url( $addnew ); ?>" class="hide-if-no-js page-title-action"><?php echo esc_html__( 'Add New', 'customize-post-categories-for-ux-builder' ); ?></a>
		<?php if ( $template_active ) { ?>
			<span class="post-category-uxb-default-template"><button data-language="<?php echo esc_attr( $language ); ?>" data-template-id="0" class="button post-category-uxb-active-template post-category-uxb-default-template"><?php echo esc_html__( 'Default Template', 'customize-post-categories-for-ux-builder' ); ?></button></span>
		<?php } ?>
	</h1> 
	<br/>
	<div class="theme-browser rendered">
		<div class="themes wp-clearfix">
			<?php echo postCategoryUXB()->get_template_active_html( $template_active ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo postCategoryUXB()->get_all_templates_html( $templates, $language ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="theme add-new-theme">
				<a href="<?php echo esc_url( $addnew ); ?>">
					<div class="theme-screenshot"><span></span></div>
					<h2 class="theme-name"><?php echo esc_html__( 'Add New Template', 'customize-post-categories-for-ux-builder' ); ?></h2>
				</a>
			</div>
		</div>
	</div>
</div>
