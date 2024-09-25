<?php // phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
/**
 * Background image.
 *
 * @package Flatsome
 */

if ( ! empty( $atts['bg'] ) ) :
	$output = '';

	if ( ! is_numeric( $atts['bg'] ) ) {
		$image_id = attachment_url_to_postid( $atts['bg'] );
		$output   = $image_id
			? wp_get_attachment_image( $image_id, $atts['bg_size'], false, [ 'class' => 'bg' ] )
			: sprintf( '<img src="%s" class="bg" alt="" />', esc_url( $atts['bg'] ) );
	} else {
		$output = wp_get_attachment_image( $atts['bg'], $atts['bg_size'], false, [ 'class' => 'bg' ] );
	}

	if ( ! empty( $output ) ) {
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput
	}
endif; // phpcs:enable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
