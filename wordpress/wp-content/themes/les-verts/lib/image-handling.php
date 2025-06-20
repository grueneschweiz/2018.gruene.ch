<?php
/**
 * Image handling functionality for Les Verts theme
 * 
 * Replaces Timmy with WordPress core image handling
 */

/**
 * Setup image sizes and configurations
 */
add_action('after_setup_theme', 'les_verts_image_handling_setup');
function les_verts_image_handling_setup() {
    // Full width images
    add_image_size('full-width-2560', 2560, 0, false);
    add_image_size('full-width-2048', 2048, 0, false);
    add_image_size('full-width-1680', 1680, 0, false);
    add_image_size('full-width-1024', 1024, 0, false);
    add_image_size('full-width-768', 768, 0, false);
    add_image_size('full-width-640', 640, 0, false);

    // Regular content images
    add_image_size('regular-1580', 1580, 0, false);
    add_image_size('regular-1024', 1024, 0, false);
    add_image_size('regular-768', 768, 0, false);
    add_image_size('regular-640', 640, 0, false);
    add_image_size('regular-400', 400, 0, false);
    add_image_size('regular-200', 200, 0, false);
    add_image_size('regular-100', 100, 0, false);

    // Admin/thumbnail
    add_image_size('admin-thumbnail', 150, 150, true);
    
    // Social/SEO
    add_image_size('twitter-image', 1200, 0, false);
    add_image_size('open-graph', 1200, 630, true);
    
    // Set default image quality
    add_filter('jpeg_quality', function() { return 85; });
    add_filter('wp_editor_set_quality', function() { return 85; });
}

/**
 * Add custom image sizes to media selection
 */
add_filter('image_size_names_choose', 'les_verts_custom_image_sizes');
function les_verts_custom_image_sizes($sizes) {
    return array_merge($sizes, array(
        'full-width-2560' => __('Full Width (2560px)'),
        'regular-1580' => __('Regular Width (1580px)'),
        'admin-thumbnail' => __('Admin Thumbnail')
    ));
}

/**
 * Custom srcset handling
 */
add_filter('wp_calculate_image_srcset', 'les_verts_custom_srcset', 10, 5);
function les_verts_custom_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
    if (!is_array($sources)) {
        return $sources;
    }
    
    // Add any custom srcset modifications here if needed
    return $sources;
}

/**
 * Custom sizes attribute for responsive images
 */
add_filter('wp_calculate_image_sizes', 'les_verts_content_image_sizes_attr', 10, 2);
function les_verts_content_image_sizes_attr($sizes, $size) {
    if (is_array($size) && $size[0] >= 1580) {
        return '(max-width: 768px) 100vw, (max-width: 1200px) 80vw, 1200px';
    }
    return $sizes;
}

/**
 * Helper function to get responsive image
 * Similar to Timmy's get_timber_image_responsive
 */
function les_verts_get_responsive_image($attachment_id, $size = 'regular-1580', $attr = array()) {
    if (!$attachment_id) {
        return '';
    }

    $default_attr = array(
        'class' => 'wp-image-' . $attachment_id,
        'loading' => 'lazy',
        'sizes' => wp_get_attachment_image_sizes($attachment_id, $size)
    );

    $attr = wp_parse_args($attr, $default_attr);
    
    return wp_get_attachment_image($attachment_id, $size, false, $attr);
}

/**
 * Get responsive image HTML with srcset and sizes
 * 
 * @param int|string $image Image ID or URL
 * @param string $size Image size name
 * @param array $attr Image attributes
 * @return string HTML img element or empty string
 */
function les_verts_responsive_image($image, $size = 'regular-1580', $attr = []) {
    if (empty($image)) {
        return '';
    }

    // If it's a numeric ID
    if (is_numeric($image)) {
        return les_verts_get_responsive_image($image, $size, $attr);
    }
    
    // If it's a URL, create a simple img tag
    if (is_string($image)) {
        $default_attr = [
            'class' => 'wp-image',
            'loading' => 'lazy',
            'src' => $image,
            'alt' => $attr['alt'] ?? ''
        ];
        
        $attr = wp_parse_args($attr, $default_attr);
        
        $html = '<img';
        foreach ($attr as $name => $value) {
            $html .= ' ' . $name . '="' . esc_attr($value) . '"';
        }
        $html .= ' />';
        
        return $html;
    }
    
    return '';
}

/**
 * Get image URL by size
 */
function les_verts_get_image_url($image, $size = 'full') {
    if (is_numeric($image)) {
        $src = wp_get_attachment_image_src($image, $size);
        return $src[0] ?? '';
    }
    
    if (is_string($image)) {
        return $image;
    }
    
    return '';
}
