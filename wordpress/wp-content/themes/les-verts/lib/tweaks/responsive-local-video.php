<?php

/**
 * Strip out width and height of video tag to work properly in a responsive design
 */
add_filter( 'wp_video_shortcode', function ( $output, $atts ) {
	$width  = 'width="' . $atts['width'] . '"';
	$height = 'height="' . $atts['height'] . '"';
	$style  = 'style="width: ' . $atts['width'] . 'px;"';

	$output = str_replace( $width, '', $output );
	$output = str_replace( $height, '', $output );
	$output = str_replace( $style, '', $output );

	return $output;
}, 10, 2 );
