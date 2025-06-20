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
    
        // Get the image dimensions for the requested size
        $image_sizes = wp_get_attachment_metadata($image->ID);
        $width = '';
        $height = '';
    
        // Handle case when size is an array
        if (is_array($size)) {
            $width = $size[0] ?? '';
            $height = $size[1] ?? '';
        } 
        // Handle case when size is a string
        elseif (is_string($size) && !empty($image_sizes['sizes'][$size])) {
            $width = $image_sizes['sizes'][$size]['width'] ?? $image_sizes['width'] ?? '';
            $height = $image_sizes['sizes'][$size]['height'] ?? $image_sizes['height'] ?? '';
        } 
        // Fallback to full size
        else {
            $width = $image_sizes['width'] ?? '';
            $height = $image_sizes['height'] ?? '';
        }
    
        // Get the image source
        $src = $image->src($size);
        if (empty($src)) {
            return '';
        }
    
        // Build attributes
        $attributes = [
            'src' => $src,
            'class' => 'wp-image-' . $image->ID,
            'loading' => 'lazy',
            'sizes' => wp_get_attachment_image_sizes($image->ID, $size),
            'style' => 'object-fit: cover; object-position: center; width: 100%; height: 100%;'
        ];
    
        // Only add width/height if we have valid values
        if ($width) $attributes['width'] = $width;
        if ($height) $attributes['height'] = $height;
    
        // Merge with provided attributes (allowing them to override defaults)
        $attributes = array_merge($attributes, $attr);
    
        // Build the image tag
        $html = '<img';
        foreach ($attributes as $name => $value) {
            if ($value !== null && $value !== '') {
                $html .= ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
            }
        }
    
        return $html;
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
