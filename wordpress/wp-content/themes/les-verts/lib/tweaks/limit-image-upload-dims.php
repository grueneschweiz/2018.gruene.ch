<?php

use SUPT\LesVertsImages;

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

    if($type === IMAGETYPE_PNG) {
		// New hard size limit
		if (isset($file['size']) && $file['size'] > LesVertsImages::getPNGMaxSize()) {
			$file['error'] = sprintf(
				__( 'PNG images must be smaller than %dMB. Your file is %.1fMB. Please compress it or convert it to JPEG.', THEME_DOMAIN ),
				LesVertsImages::getPNGMaxSize() / (1024 * 1024),
				$file['size'] / (1024 * 1024)
			);
		}

		// Legacy PNG dimensions checks (could still be configured)
		if ( defined( 'SUPT_UPLOAD_MAX_PX_PNG' )
			&& SUPT_UPLOAD_MAX_PX_PNG < $width * $height
		) {
			$len           = (int) floor( sqrt( SUPT_UPLOAD_MAX_PX_PNG ) );
			$file['error'] = sprintf(
				__( 'PNG images must not exceed the area of %d * %d pixels. Reduce the image dimensions or convert it to JPEG.', THEME_DOMAIN ),
				$len,
				$len
			);
		}
		// New hard dimensions limit
		else if ($width > LesVertsImages::getFullSizeWidth() || $height > LesVertsImages::getFullSizeWidth()) {
			$file['error'] = sprintf(
				__( 'PNG images must not exceed the area of %d * %d pixels. Reduce the image dimensions or convert it to JPEG.', THEME_DOMAIN ),
				LesVertsImages::getFullSizeWidth(),
				LesVertsImages::getFullSizeWidth()
			);
		}
	}

	if ( defined( 'SUPT_UPLOAD_MAX_PX_JPEG' )
	     && $type === IMAGETYPE_JPEG
	     && SUPT_UPLOAD_MAX_PX_JPEG < $width * $height
	) {
		$len           = (int) floor( sqrt( SUPT_UPLOAD_MAX_PX_JPEG ) );
		$file['error'] = sprintf(
			__( 'Images must not exceed the area of %d * %d pixels. Please reduce the image dimensions and try again.', THEME_DOMAIN ),
			$len,
			$len
		);
	}

	return $file;
} );
