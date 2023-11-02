<?php

if ( ! defined( 'UPLOAD_MAX_PX_PNG' ) ) {
	define( 'UPLOAD_MAX_PX_PNG', 1920 * 1080 ); // FullHD
}

if ( ! defined( 'UPLOAD_MAX_PX_JPEG' ) ) {
	define( 'UPLOAD_MAX_PX_JPEG', 4096 * 3072 ); // 4K
}

/**
 * Restrict max image upload dimensions for PNGs and JPEGs.
 *
 * We constantly had memory issues with large images, especially with cyon.
 * Therefore, we restricted the max image dimensions (the dimension is the
 * limiting factor, not the initial size).
 */
add_filter( 'wp_handle_upload_prefilter', function ( $file ) {
	$file_data = null;

	if (
		is_array( $file )
		&& array_key_exists( 'tmp_name', $file )
		&& file_exists( $file['tmp_name'] )
	) {
		$file_data = getimagesize( $file['tmp_name'] );
	}

	// if no image size could be read, return the file as is
	if ( ! $file_data ) {
		return $file;
	}

	list( $width, $height, $type ) = $file_data;

	if ( $type === IMAGETYPE_PNG && UPLOAD_MAX_PX_PNG < $width * $height ) {
		$file['error'] = 'PNG images must not exceed 1920px * 1080px. Reduce the image size or convert it to JPEG.';
	}

	if ( $type === IMAGETYPE_JPEG && UPLOAD_MAX_PX_JPEG < $width * $height ) {
		$file['error'] = 'Images must not exceed 4096px * 3072px. Please reduce the image dimensions before uploading.';
	}

	return $file;
} );
