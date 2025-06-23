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

    public function getTimberImageResponsive(Environment $env, $image, $size = 'regular-1580', $attr = []) {
        $image = $this->initializeImage($image);
        if (empty($image)) {
            return '';
        }

        // get image sizes
        list($width, $height, $size) = $this->handleImageSizing($image, $size);

        // get image source
        $src = $this->getImageSource($image, $size);
        if (empty($src)) {
            return '';
        }

        // Build and return HTML attributes
        return $this->buildHtmlAttributes($image, $src, $width, $height, $size, $attr);
    }

    /**
     * Handle image sizing based on the provided size parameter
     */
    private function handleImageSizing(Image $image, $size) {
        $width = $height = '';
        $image_sizes = wp_get_attachment_metadata($image->ID);
        $image_sizes = is_array($image_sizes) ? $image_sizes : [];

        if (is_array($size)) {
            $width = !empty($size[0]) ? (int)$size[0] : 0;
            $height = !empty($size[1]) ? (int)$size[1] : 0;
            $size = [$width, $height];
        }
        elseif (is_string($size)) {
            if (!empty($image_sizes['sizes'][$size])) {
                $width = $image_sizes['sizes'][$size]['width'] ?? '';
                $height = $image_sizes['sizes'][$size]['height'] ?? '';
            } else {
                $width = $image_sizes['width'] ?? '';
                $height = $image_sizes['height'] ?? '';
            }
        }

        return [$width, $height, $size];
    }

    /**
     * Get the image source URL with error handling
     */
    private function getImageSource(Image $image, $size) {
        try {
            $src = $image->src($size);
            if (empty($src)) {
                error_log('Empty image source for attachment ID: ' . $image->ID);
                return '';
            }
            return $src;
        } catch (\Exception $e) {
            error_log('Error getting image source: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Build HTML attributes string from image data
     */
    private function buildHtmlAttributes(Image $image, $src, $width, $height, $size, $attr) {
        $attributes = [
            'src' => esc_url($src),
            'class' => 'wp-image-' . (int)$image->ID,
            'loading' => 'lazy',
            'sizes' => wp_get_attachment_image_sizes($image->ID, $size) ?: '',
            'style' => 'object-fit: cover; object-position: center; width: 100%; height: 100%;',
            'alt' => !empty($attr['alt']) ? $attr['alt'] : ''
        ];

        if (!empty($width)) $attributes['width'] = (int)$width;
        if (!empty($height)) $attributes['height'] = (int)$height;

        $attributes = array_merge($attributes, $attr);

        $html_attributes = [];
        foreach ($attributes as $name => $value) {
            if ($value !== null && $value !== '') {
                $html_attributes[] = esc_attr($name) . '="' . esc_attr($value) . '"';
            }
        }

        return implode(' ', $html_attributes);
    }

    private function initializeImage($image) {
        if (is_numeric($image) || is_string($image)) {
            return new Image($image);
        }

        if (!($image instanceof Image)) {
            return null;
        }

        return $image;
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

    public function timberImage(Environment $env, $image, $size = 'full', $crop = 'default') {
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
