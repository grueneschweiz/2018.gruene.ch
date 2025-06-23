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
    // Full width images (for full viewport width sections)
    add_image_size('full-width-2560x0', 2560, 0, false);  // Max width for large displays
    add_image_size('full-width-1680x0', 1680, 0, false);  // Common desktop
    add_image_size('full-width-1024x0', 1024, 0, false);  // Tablets
    add_image_size('full-width-640x0', 640, 0, false);    // Large phones

    // Regular content images (for content areas)
    add_image_size('regular-1580x0', 1580, 0, false);     // Max content width
    add_image_size('regular-1024x0', 1024, 0, false);     // Smaller content
    add_image_size('regular-790x0', 790, 0, false);       // Default content
    add_image_size('regular-400x0', 400, 0, false);       // Small content

    // Social/SEO
    add_image_size('large', 1200, 0, false);
    add_image_size('wpseo-opengraph', 1200, 630, true);

    // Set default image quality
    add_filter('jpeg_quality', function() { return 85; });
    add_filter('wp_editor_set_quality', function() { return 85; });
}

/*
Debugging: Log and remove image sizes

// At the end of the les_verts_image_handling_setup() function, add:
add_action('after_setup_theme', function() {
    // ... existing code ...

    // Debug: Log all registered image sizes
    add_action('init', function() {
        global $_wp_additional_image_sizes;
        error_log('=== REGISTERED IMAGE SIZES ===');
        error_log(print_r($_wp_additional_image_sizes, true));
        error_log(print_r(wp_get_registered_image_subsizes(), true));
        error_log('==============================');
    }, 999);
}, 0);

// Remove unnecessary image sizes
add_action('init', function() {
    // Remove default WordPress sizes we don't need
    remove_image_size('1536x1536');
    remove_image_size('2048x2048');
    remove_image_size('medium_large');

    // Remove duplicate sizes
    remove_image_size('regular-2560x0');  // Duplicate of full-width
    remove_image_size('regular-2048x0');  // Duplicate of full-width
    remove_image_size('regular-768x0');   // Too close to 790x0
    remove_image_size('regular-200x0');   // Too small
    remove_image_size('regular-150x0');   // Too small
    remove_image_size('regular-100x0');   // Too small
    remove_image_size('full-width-200x0'); // Too small
    remove_image_size('full-width-100x0'); // Too small
}, 99);

*/