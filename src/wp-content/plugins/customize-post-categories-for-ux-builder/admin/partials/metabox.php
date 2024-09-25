<tr class="form-field">
		<th valign="top" scope="row">
			<label for="">
				<?php
					echo ! empty( $language ) ? esc_html__( 'Template', 'customize-post-categories-for-ux-builder' ) . ' (' . esc_html( $language ) . ')' : esc_html__( 'Template', 'customize-post-categories-for-ux-builder' );
				?>
			</label>
		</th>
		<td>
			<select name="<?php echo esc_attr( $name ); ?>" data-placeholder="<?php echo esc_attr__( 'Select a template', 'customize-post-categories-for-ux-builder' ); ?>" class="enhanced chosen_select_nostd post-category-uxb-select-option regular-text" id="post_cat_uxb_template">
				<?php
					echo '<option value="">' . esc_html__( 'Default Template', 'customize-post-categories-for-ux-builder' ) . '</option>';
				if ( $template_active ) {
					echo '<option value="active" selected>' . esc_html__( 'Template has been activated', 'customize-post-categories-for-ux-builder' ) . '</option>';
				}
				foreach ( $templates as $key => $template ) {
					echo '<option value="' . esc_attr( $template->ID ) . '"' . selected( $selected, $template->ID ) . '>' . esc_html__( $template->post_title, 'customize-post-categories-for-ux-builder' ) . '</option>';
				}
				?>
			</select>
		</td>
</tr>
<?php
if ( 'category' === $type ) {
	$thumbnail_id      = get_term_meta( $term_id, 'post_cat_uxb_thumbnail_id', true );
	$thumbnail_default = post_category_uxb_get_image_default();
	if ( $thumbnail_id ) {
		$image = wp_get_attachment_image_src( $thumbnail_id, 'thumbnail' );
		$image = $image ? $image[0] : $thumbnail_default;
	} else {
		$image = $thumbnail_default;
	}
	?>
	<tr class="form-field term-thumbnail-wrap post-cat-uxb-thumbnail-wrap">
		<th scope="row" valign="top">
			<label>
				<?php echo esc_html__( 'Thumbnail', 'customize-post-categories-for-ux-builder' ); ?>
			</label>
		</th>
		<td>
			<div id="post_cat_uxb_thumbnail" style="float: left; margin-right: 10px;">
				<img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px">
			</div>
			<div style="line-height: 60px;">
				<input type="hidden" id="post_cat_uxb_thumbnail_id" name="post_cat_uxb_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>">
				<button type="button" class="cat_uxb_upload_image_button button"><?php echo esc_html__( 'Upload/Add image', 'customize-post-categories-for-ux-builder' ); ?></button>
				<button type="button" class="cat_uxb_remove_image_button button" style="<?php echo ! empty( $thumbnail_id ) ? '' : 'display: none;'; ?>"><?php echo esc_html__( 'Remove image', 'customize-post-categories-for-ux-builder' ); ?></button>
			</div>
			<div class="clear"></div>
		</td>
	</tr>
	<script type="text/javascript">
		// Only show the "remove image" button when needed
		if ( '0' === jQuery( '#post_cat_uxb_thumbnail_id' ).val() ) {
			jQuery( '.cat_uxb_remove_image_button' ).hide();
		}
		// Uploading files
		var file_frame;
		jQuery( document ).on( 'click', '.cat_uxb_upload_image_button', function( event ) {
			event.preventDefault();
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}
			// Create the media frame.
			file_frame = wp.media.frames.downloadable_file = wp.media({
				title: 'Choose an image',
				button: {
					text: 'Use image'
				},
				multiple: false
			});
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
				var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;
				jQuery( '#post_cat_uxb_thumbnail_id' ).val( attachment.id );
				jQuery( '#post_cat_uxb_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
					jQuery( '.cat_uxb_remove_image_button' ).show();
				});
				// Finally, open the modal.
				file_frame.open();
		});
		jQuery( document ).on( 'click', '.cat_uxb_remove_image_button', function() {
			jQuery( '#post_cat_uxb_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_url( $thumbnail_default ); ?>' );
			jQuery( '#post_cat_uxb_thumbnail_id' ).val( '' );
			jQuery( '.cat_uxb_remove_image_button' ).hide();
			return false;
		});
	</script>
	<?php
} ?>
