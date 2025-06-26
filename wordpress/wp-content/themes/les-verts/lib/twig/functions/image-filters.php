<?php
/**
 * Twig filters for image handling
 */

namespace SUPT\Twig;

use Timber\Image;
use Timber\ImageHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\Environment;

class ImageFilters extends AbstractExtension {
    public function getFilters() {
        return [
            new TwigFilter('get_timber_image_responsive', [$this, 'getTimberImageResponsive'], ['needs_environment' => true]),
            new TwigFilter('resize', [$this, 'resize']),
            new TwigFilter('timber_image', [$this, 'timberImage'], ['needs_environment' => true]),
        ];
    }

    public function getTimberImageResponsive(Environment $env, $image, $size = 'regular', $attr = []) {
        $image = $this->initializeImage($image);
        if (empty($image)) {
            return '';
        }

        if(!is_string($size)) {
            $size = 'regular';
        }

        if(!is_array($attr)) {
            $attr = [];
        }

        $src = $this->getImageSource($image, $size);
        if (empty($src)) {
            return '';
        }

        // Build and return HTML attributes
        return $this->buildHtmlAttributes($image, $src, $size, $attr);
    }

    private function getImageSource(Image $image, $size) {
        try {
            if(\SUPT\LesVertsImages::getSizes()[$size]) {
                $src = $image->src($size);
            }
            else {
                $src = $image->src('regular');
            }
            if ($src) return $src;

            return '';

        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Build HTML attributes st§ring from image data
     */
    private function buildHtmlAttributes(Image $image, $src, $size, $attr) {
        $srcset = wp_get_attachment_image_srcset($image->ID, $size);

        $focal_point = $this->getFocalPoint($image);
        $position = $this->mapFocalPointToPosition($focal_point);

        $attributes = [
            'src' => esc_url($src),
            'srcset' => $srcset,        // Standard srcset for non-lazy images
            'data-srcset' => $srcset,   // Data-srcset for lazy loading JavaScript
            'sizes' => '100vw', //TODO
            'loading' => 'lazy',
            'style' => sprintf(
                'object-fit: cover; object-position: %s; width: 100%%; height: 100%%;',
                esc_attr($position)
            ),
            'alt' => !empty($attr['alt']) ? $attr['alt'] : ''
        ];

        $attributes['data-focal-point'] = $focal_point;

        $attributes = array_merge($attributes, $attr);

        $html_attributes = [];
        foreach ($attributes as $name => $value) {
            if ($value !== null && $value !== '') {
                $html_attributes[] = esc_attr($name) . '="' . esc_attr($value) . '"';
            }
        }

        return implode(' ', $html_attributes);
    }

    /**
     * Get the focal point for an image
     *
     * @param Image $image Timber Image object
     * @return string Focal point in format 'position_x_position_y' (e.g. 'center_center')
     */
    private function getFocalPoint(Image $image) {
        // Try to get focal point from image meta
        $focal_point = $image->meta('focal_point') ?? '';

        // If no focal point is set, try to get it from the image object
        if (empty($focal_point) && property_exists($image, 'focal_point')) {
            $focal_point = $image->focal_point;
        }

        // Default to center if no focal point is set
        return !empty($focal_point) ? $focal_point : 'center';
    }

    /**
     * Map focal point to CSS object-position value
     *
     * @param string $focal_point Focal point value from ACF (e.g., 'top-left', 'center-center')
     * @return string CSS object-position value (e.g., 'left top', 'center center')
     */
    private function mapFocalPointToPosition($focal_point) {
        if (!function_exists('acf_get_field')) {
            return 'center';
        }

        $field = acf_get_field('focal_point');

        if (empty($field) || !isset($field['choices'][$focal_point])) {
            return 'center';
        }

        return $field['choices'][$focal_point];
    }

    private function initializeImage($image) {
        $img = null;
        if (is_numeric($image) || is_string($image)) {
            $img = new Image($image);
        } elseif ($image instanceof Image) {
            $img = $image;
        } else {
            return null;
        }

        // Check if we're dealing with a scaled image and if we should use the original
        $file_loc = $img->file_loc();
        $file = $img->file();
        if ($file_loc && strpos($file_loc, '-scaled.') !== false) {
            $original_file_loc = str_replace('-scaled.', '.', $file_loc);
            $original_file = str_replace('-scaled.', '.', $file);
            $original_url = str_replace(
                basename($file_loc),
                basename($original_file_loc),
                $img->src()
            );

            // Only proceed if original exists and we're not already using it
            if (file_exists($original_file_loc)) {
                // Create a new Image instance with the original file
                $original_img = new \Timber\Image($img->ID);
                $original_img->file = $original_file;
                $original_img->file_loc = $original_file_loc;
                $original_img->abs_url = $original_url;

                // Update the source to use the original
                $original_img->src = function() use ($original_url) {
                    return $original_url;
                };

                return $original_img;
            }
        }

        return $img;
    }

    public function resize($image, $width, $height = 0, $crop = 'default') {
        if (empty($image)) {
            return '';
        }

        if (is_numeric($image)) {
            $image = new Image($image);
        } elseif (is_string($image)) {
            $image = new Image($image);
        }

        if (!($image instanceof Image)) {
            return '';
        }

        $resized = ImageHelper::resize(
            $image->src(),
            $width,
            $height,
            $crop,
            false
        );

        return $resized ?: $image->src();
    }

    public function timberImage(Environment $env, $image, $size = 'full-width', $crop = 'default') {
        if (empty($image)) {
            return '';
        }

        if (is_numeric($image)) {
            $image = new Image($image);
        } elseif (is_string($image)) {
            $image = new Image($image);
        }

        if (!($image instanceof Image)) {
            return '';
        }

        return $image->src($size);
    }

    // getName() is deprecated in Twig 3.x, but we keep it for backward compatibility
    public function getName() {
        return 'supt_image_filters';
    }
}

// Add to Twig
add_filter('timber/twig', function(Environment $twig) {
    $twig->addExtension(new ImageFilters());
    return $twig;
});
