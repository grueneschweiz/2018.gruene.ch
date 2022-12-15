<?php
declare( strict_types=1 );

namespace SUPT;

use Throwable;
use Timber\Image;
use Twig\TwigFilter;

add_filter( 'get_twig', function ( $twig ) {
	$twig->addFilter(
	/**
	 * @param int|Image $image
	 * @param string|array $size
	 */
		new TwigFilter( 'failsafe_get_timer_image_responsive', function ( $image, $size ) {
			try {
				return get_timber_image_responsive( $image, $size );
			} catch ( Throwable $e ) {
				$attachment_id = $image instanceof Image ? $image->id : $image;
				$url           = wp_get_original_image_url( $attachment_id );
				trigger_error( "Failed to get responsive image via timmy. Using fallback method. Image: $url", E_USER_WARNING );
			}

			$image = wp_get_attachment_image_src( $attachment_id, $size, false );

			if ( $image ) {
				[ $src, $width, $height ] = $image;
				$image_meta = wp_get_attachment_metadata( $attachment_id );

				// ensure width and height are both set (respecting the image's side ratio)
				if ( ( $width === 0 || $height === 0 )
				     && $image_meta
				     && ! empty( $image_meta['width'] )
				     && ! empty( $image_meta['height'] )
				     && is_numeric( $image_meta['width'] )
				     && is_numeric( $image_meta['height'] )
				) {
					if ( $width === 0 && $height > 0 ) {
						$width = (int) ( ( $height / $image_meta['height'] ) * $image_meta['width'] );
					}
					if ( $height === 0 && $width > 0 ) {
						$height = (int) ( ( $width / $image_meta['width'] ) * $image_meta['height'] );
					}
				}

				$attachment = get_post( $attachment_id );

				$attr = array(
					'src' => $src,
					'alt' => trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ),
				);

				if ( is_array( $image_meta ) ) {
					$size_array = array( absint( $width ), absint( $height ) );
					$srcset     = wp_calculate_image_srcset( $size_array, $src, $image_meta, $attachment_id );
					$sizes      = wp_calculate_image_sizes( $size_array, $src, $image_meta, $attachment_id );

					if ( $srcset && $sizes ) {
						$attr['srcset'] = $srcset;
						$attr['sizes']  = $sizes;
					}
				}

				$attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment, $size );
				$attr = array_map( 'esc_attr', $attr );

				$html = '';
				foreach ( $attr as $name => $value ) {
					$html .= " $name=\"$value\"";
				}

				return trim( $html );
			}

			return 'src="" alt="Failed to load image."';
		} )
	);

	return $twig;
} );
