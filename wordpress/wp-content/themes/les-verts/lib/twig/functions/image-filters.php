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

    private const MIN_EXPECTED_SIZE_OPTIONS = 6;

    public function getFilters() {
        return [
            new TwigFilter('get_timber_image_responsive', [$this, 'getTimberImageResponsive'], ['needs_environment' => true]),
            new TwigFilter('resize', [$this, 'resize']),
        ];
    }

    public function getTimberImageResponsive(Environment $env, $image, $size = 'medium', $attr = []) {
        $image = $this->initializeImage($image);
        if (empty($image)) {
            return '';
        }

        // Handle 'full-width' references
        if ($size === 'full-width') {
            $size = 'full';
        }

        if(!is_string($size) || $size === 'regular') {
            $size = 'medium';
        }

        if(!is_array($attr)) {
            $attr = [];
        }

        $src = $this->getImageSource($image, $size);
        if (empty($src)) {
            return '';
        }

        return $this->buildHtmlAttributes($image, $src, $size, $attr);
    }

    /**
     * Get the source URL via the wordpress core src() method
     */
    private function getImageSource(Image $image, $size) {
        try {
            if(isset(\SUPT\LesVertsImages::getSizesWithFull()[$size])) {
                $src = $image->src($size);
            }
            else {
                $src = $image->src('medium');
            }
            if ($src) return $src;

            return '';

        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Build HTML attributes from image data
     */
    private function buildHtmlAttributes(Image $image, $src, $size, $attr) {
        // First try WordPress core srcset generation
        $srcset = wp_get_attachment_image_srcset($image->ID, $size);

        $num_srcset_options = 0;
        if ($srcset) {
            $srcset_options = array_filter(array_map('trim', explode(',', $srcset)));
            $num_srcset_options = count($srcset_options);
        }

        if ($num_srcset_options < self::MIN_EXPECTED_SIZE_OPTIONS) {
            // If WordPress couldn't generate srcset, try to build one from legacy files
            $legacy_srcset = \SUPT\buildLegacySrcset($image->ID);
            $legacy_srcset_options = array_filter(array_map('trim', explode(',', $legacy_srcset)));
            $num_legacy_srcset_options = count($legacy_srcset_options);
            if ($num_legacy_srcset_options > $num_srcset_options) {
                $srcset = $legacy_srcset;
            }
        }

        $attributes = [
            'src' => esc_url($src),
            'srcset' => $srcset,        // Standard srcset for non-lazy images
            'data-srcset' => $srcset,   // Data-srcset for lazy loading JavaScript
            'sizes' => '100vw',
            'loading' => 'lazy',
            'alt' => !empty($attr['alt']) ? $attr['alt'] : ''
        ];

        $focal_point = $this->getFocalPoint($image);
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

    private function initializeImage($image) {
        $img = null;
        if (is_numeric($image) || is_string($image)) {
            $img = new Image($image);
        } elseif ($image instanceof Image) {
            $img = $image;
        } else {
            return null;
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

    // getName() is deprecated in Twig 3.x, but we keep it for backward compatibility
    public function getName() {
        return 'supt_image_filters';
    }
}
