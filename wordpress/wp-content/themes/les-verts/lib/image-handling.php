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
 * Custom sizes attribute for responsive images
 */
add_filter('wp_calculate_image_sizes', 'les_verts_content_image_sizes_attr', 10, 2);
function les_verts_content_image_sizes_attr($sizes, $size) {
    if (is_array($size) && $size[0] >= 1580) {
        return '(max-width: 768px) 100vw, (max-width: 1200px) 80vw, 1200px';
    }
    return $sizes;
}


