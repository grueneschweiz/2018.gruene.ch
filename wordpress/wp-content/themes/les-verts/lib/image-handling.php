<?php
/**
 * Image handling functionality for Les Verts theme
 * 
 * Replaces Timmy with WordPress core image handling
 */

/**
 * Global image size definitions
 */
class LesVertsImages {
    /**
     * Get image size widths
     * 
     * @return array Array of size name => width in pixels
     */
    public static function getSizes() {
        return [
            'thumbnail' => 150,
            'small' => 400,
            'medium' => 768,
            'regular' => 1200,
            'large' => 1580,
            'full' => 2560
        ];
    }
}

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

// Set threshold to 4K resolution instead of default 2560px
add_filter('big_image_size_threshold', function() {
    return 3840; // 4K width
});

/**
 * Hook into WordPress image URL generation to support legacy Timmy filenames
 * 
 * This filter intercepts wp_get_attachment_image_src() calls and provides
 * fallback support for old Timmy-style filenames when new ones don't exist.
 */
add_filter('image_downsize', 'les_verts_image_downsize_fallback', 5, 3);
function les_verts_image_downsize_fallback($out, $attachment_id, $size) {
    // Check if WordPress already has this image size by looking at metadata
    $metadata = wp_get_attachment_metadata($attachment_id);
    if (!$metadata || !isset($metadata['file'])) {
        return false;
    }

    if(!is_string($size) || $size === 'full') {
        $size = 'full-width-2560x0';
    }

    // Check if WordPress has the requested size
    if (isset($metadata['sizes'][$size])) {
        $upload_dir = wp_upload_dir();
        $pathinfo = pathinfo($metadata['file']);
        $size_data = $metadata['sizes'][$size];

        $wordpress_file = trailingslashit($upload_dir['basedir']) . 
                         $pathinfo['dirname'] . '/' . $size_data['file'];

        if (file_exists($wordpress_file)) {
            // WordPress has this size and file exists, let it handle it
            return false;
        }
    }

    // WordPress doesn't have this size or file doesn't exist, try legacy Timmy format
    $legacy_image = les_verts_find_legacy_image($attachment_id, $size);
    
    if ($legacy_image) {
        return $legacy_image;
    }
    
    return false;
}

/**
 * Find legacy Timmy-style image files
 * 
 * @param int $attachment_id The attachment ID
 * @param string|array $size The image size
 * @return array|false Image data array or false if not found
 */
function les_verts_find_legacy_image($attachment_id, $size) {
    $attachment = get_post($attachment_id);
    if (!$attachment) {
        return false;
    }
    
    $upload_dir = wp_upload_dir();
    $metadata = wp_get_attachment_metadata($attachment_id);
    
    if (!$metadata || !isset($metadata['file'])) {
        return false;
    }
    
    // Get the base filename without extension
    $pathinfo = pathinfo($metadata['file']);
    $extension = $pathinfo['extension'];

    if($extension === 'svg') {
        return false;
    }

    $base_path = trailingslashit($upload_dir['basedir']) . $pathinfo['dirname'];
    $base_name = str_replace('scaled', '', $pathinfo['filename']);
    
    // Extract width from WordPress filename using regex (e.g., europawahlen-1-1024x685 -> 1024)
    $width = false;
    if (preg_match('/-(\d+)x\d+$/', $base_name, $matches)) {
        $width = $matches[1];
    }
    
    // Remove any existing WordPress dimensions from filename (e.g., -1200x630)
    $clean_name = preg_replace('/-\d+x\d+$/', '', $base_name);
    
    if ($width) {
        // Search for legacy files with specific width: [name]-[width]*-c-default.[ext]
        $search_pattern = $base_path . '/' . $clean_name . '-' . $width . '*-c-default.' . $extension;
        $legacy_files = glob($search_pattern);
        
        // Also try -c-center as fallback
        if (empty($legacy_files)) {
            $search_pattern = $base_path . '/' . $clean_name . '-' . $width . '*-c-center.jpg';
            $legacy_files = glob($search_pattern);
        }
    } else {
        // Fallback: Search for any legacy files: [name]*-c-default.[ext]
        $search_pattern = $base_path . '/' . $clean_name . '*-c-default.' . $extension;
        $legacy_files = glob($search_pattern);
        
        // Also try -c-center as fallback
        if (empty($legacy_files)) {
            $search_pattern = $base_path . '/' . $clean_name . '*-c-center.jpg';
            $legacy_files = glob($search_pattern);
        }
    }
    
    if (!empty($legacy_files)) {
        // Find the best matching file based on extracted width
        $best_match = les_verts_select_legacy_file_by_width($legacy_files, $width, $size);
        
        if ($best_match) {
            $legacy_path = $best_match;
            $legacy_filename = basename($legacy_path);
            
            $legacy_url = trailingslashit($upload_dir['baseurl']) . 
                         $pathinfo['dirname'] . '/' . $legacy_filename;
            
            // Get dimensions from actual file
            $image_info = getimagesize($legacy_path);
            if ($image_info) {
                return [
                    $legacy_url,
                    $image_info[0], // width
                    $image_info[1], // height
                    true // is_intermediate
                ];
            }
        }
    }
    
    return false;
}

/**
 * Select the best matching legacy file based on width
 * 
 * @param array $legacy_files Array of legacy file paths
 * @param int|string $target_width Target width extracted from WordPress filename
 * @return string|false Best matching file path or false if none suitable
 * @param string $size The image size
 */
function les_verts_select_legacy_file_by_width($legacy_files, $target_width, $size) {
    if (empty($legacy_files)) {
        return false;
    }
    
    if (!$target_width) {
        //TODO this is not the best way to do it
        $sizes = ["full-width-2560x0" => 2560, "large" => 1200, "medium" => 768, "thumbnail" => 150];
        if (array_key_exists($size, $sizes)) {
            $target_width = $sizes[$size];
        }
        else {
            $target_width = 2560;
        }
    }
    
    $target_width = (int) $target_width;
    $best_match = null;
    $smallest_diff = PHP_INT_MAX;
    
    foreach ($legacy_files as $file_path) {
        // Extract width from legacy filename (e.g., image-790x0-c-default.jpg -> 790)
        $filename = basename($file_path);
        if (preg_match('/-(\d+)x\d+-c-/', $filename, $matches)) {
            $file_width = (int) $matches[1];
            
            // Calculate difference from target width
            $diff = abs($file_width - $target_width);
            
            if ($diff < $smallest_diff) {
                $smallest_diff = $diff;
                $best_match = $file_path;
            }
            
            // Perfect match found, no need to continue
            if ($diff === 0) {
                break;
            }
        }
    }
    
    // Return best match or first file as fallback
    return $best_match ?: $legacy_files[0];
}