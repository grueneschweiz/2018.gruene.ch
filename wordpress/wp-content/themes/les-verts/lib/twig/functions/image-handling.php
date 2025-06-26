<?php
/**
 * Image handling functionality for Les Verts theme
 *
 * Replaces Timmy with WordPress core image handling
 *
 */

namespace SUPT;

use SUPT\Twig\ImageFilters;
use Twig\TwigFilter;
use Twig\Environment;

add_filter('get_twig', function($twig) {
    $image_filters = new ImageFilters();

    $twig->addFilter(
        new TwigFilter( 'get_timber_image_responsive',
            function (Environment $env, $image, $size = 'full-width') use ($image_filters) {
                return $image_filters->getTimberImageResponsive($env, $image, $size);
            },
            ['needs_environment' => true]
        )
    );

    return $twig;
}, 10);

/**
 * Global image size definitions
 */
class LesVertsImages {
    public static function getSizes() {
        return [
            'thumbnail' => 150,
            'small' => 400,
            'medium' => 640,
            'regular' => 790,
            'large' => 1200,
            'huge' => 1580,
            'full-width' => 2560
        ];
    }
}

// Set threshold to 4K resolution instead of default 2560px
add_filter('big_image_size_threshold', function() {
    return 3840; // 4K width
});

/**
 * Setup image sizes and configurations
 */
add_action('after_setup_theme', 'SUPT\les_verts_image_handling_setup');
function les_verts_image_handling_setup() {
    $sizes = LesVertsImages::getSizes();

    // Register image sizes using simple names
    foreach ($sizes as $size_name => $width) {
        add_image_size($size_name, $width, 0, false);
    }

    // Social/SEO specific sizes
    add_image_size('wpseo-opengraph', 1200, 630, true);

    // Set default image quality
    add_filter('jpeg_quality', function() { return 85; });
    add_filter('wp_editor_set_quality', function() { return 85; });
}

/**
 * --- This part is for compatibility with legacy Timmy image handling ---
 * --- If most of the images have been uploaded with the new image handling, remove this part ---
 * --- This fix is implemented in july 2025 ---
 *
 * Example of legacy Timmy file names:
 * custom_palette-1-150x0-c-default.png  rgb_palette-1-1200x0-c-default.png  rgb_palette-1-20x0-c-default.png    rgb_palette-1-790x0-c-default.png
 * rgb_palette-1-10x0-c-default.png      rgb_palette-1-150x0-c-default.png   rgb_palette-1-2560x0-c-default.png
 *
 * Example of new (wordpress default) file names:
 * europawahlen-1-1200x630.jpg  europawahlen-1-150x100.jpg    europawahlen-1-2560x1713.jpg  europawahlen-1-768x514.jpg
 * europawahlen-1-1200x803.jpg  europawahlen-1-1580x1057.jpg  europawahlen-1-400x268.jpg	 europawahlen-1-scaled.jpg
 */

/**
 * Hook into WordPress image URL generation to support legacy Timmy filenames
 *
 * This filter intercepts wp_get_attachment_image_src() calls and provides
 * fallback support for old Timmy-style filenames when new ones don't exist.
 */
add_filter('image_downsize', 'SUPT\image_downsize_fallback', 5, 3);
function image_downsize_fallback($out, $attachment_id, $size) {
    // Check if WordPress already has this image size by looking at metadata
    $metadata = wp_get_attachment_metadata($attachment_id);
    if (!$metadata || !isset($metadata['file'])) {
        return false;
    }

    if(!is_string($size)) {
        $size = 'regular';
    }

    $upload_dir = wp_upload_dir();
    $pathinfo = pathinfo($metadata['file']);

    // Check if WordPress has the requested size
    if(hasRequestedImageSize($metadata, $size, $upload_dir, $pathinfo)) {
        return false;
    }

    // Try legacy Timmy format
    $legacy_image = find_legacy_image($attachment_id, $size);
    if ($legacy_image) {
        return $legacy_image;
    }

    // Check for "-scaled" in filename and match with original file
    return getScaledImage($metadata, $pathinfo, $upload_dir);
}

function hasRequestedImageSize($metadata, $size, $upload_dir, $pathinfo) {
    if (isset($metadata['sizes'][$size])) {

        $size_data = $metadata['sizes'][$size];
        $wordpress_file = trailingslashit($upload_dir['basedir']) .
                         $pathinfo['dirname'] . '/' . $size_data['file'];

        if (file_exists($wordpress_file)) {
            // WordPress has this size and file exists, let it handle it
            return true;
        }
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
function find_legacy_image($attachment_id, $size) {
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

    if($extension !== 'png' && $extension !== 'jpg' && $extension !== 'jpeg' && $extension !== 'webp') {
        return false;
    }

    $base_path = trailingslashit($upload_dir['basedir']) . $pathinfo['dirname'];
    $base_name = $pathinfo['filename'];

    // Extract width from WordPress filename using regex (e.g., europawahlen-1-1024x685 -> 1024)
    $width = false;
    if (preg_match('/-(\d+)x\d+$/', $base_name, $matches)) {
        $width = $matches[1];
    }

    if ($width === false) {
        if($size === 'full') {
            return false;
        }
        else {
            $width = isset(LesVertsImages::getSizes()[$size]) ?
                LesVertsImages::getSizes()[$size] :
                LesVertsImages::getSizes()['regular'];
        }
    }

    // Remove any existing WordPress dimensions from filename (e.g., -1200x630)
    $clean_name = str_replace('-scaled', '', preg_replace('/-\d+x\d+$/', '', $base_name));
    $search_pattern = $base_path . '/' . $clean_name . '-' . $width . '*-c-default.' . $extension;
    $legacy_files = glob($search_pattern);

    if (empty($legacy_files)) {
        $search_pattern = $base_path . '/' . $clean_name . '-' . $width . '*-c-center.jpg';
        $legacy_files = glob($search_pattern);
    }

    if (!empty($legacy_files)) {
        // Find the best matching file based on extracted width
        $best_match = les_verts_select_legacy_file_by_width($legacy_files, $width, $size);

        if ($best_match) {
            $legacy_path = $best_match;
            $legacy_filename = basename($legacy_path);

            $legacy_url = trailingslashit($upload_dir['baseurl']) .
                         $pathinfo['dirname'] . '/' . $legacy_filename;

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
        $sizes = LesVertsImages::getSizes();
        if (array_key_exists($size, $sizes)) {
            $target_width = $sizes[$size];
        }
        else {
            $target_width = LesVertsImages::getSizes()['regular'];
        }
    }

    $target_width = (int) $target_width;
    $best_match = null;
    $best_match_width = PHP_INT_MAX;  // Track width of best match
    $biggest_image_index = 0;
    $biggest_image_width = 0;

    for($i = 0; $i < count($legacy_files); $i++) {
        // Extract width from legacy filename (e.g., image-790x0-c-default.jpg -> 790)
        $filename = basename($legacy_files[$i]);
        if (preg_match('/-(\d+)x\d+-c-/', $filename, $matches)) {
            $file_width = (int) $matches[1];

            // Only update best match if image is >= target AND smaller than current best
            if ($file_width >= $target_width && $file_width < $best_match_width) {
                $best_match_width = $file_width;
                $best_match = $legacy_files[$i];
            }

            // Track biggest image as fallback
            if ($file_width > $biggest_image_width) {
                $biggest_image_index = $i;
                $biggest_image_width = $file_width;
            }

            // Perfect match found, no need to continue
            if ($file_width === $target_width) {
                break;
            }
        }
    }

    // Return best match or biggest image as fallback
    return $best_match ?: $legacy_files[$biggest_image_index];
}

function getScaledImage($metadata, $pathinfo, $upload_dir) {
    // Check for "-scaled" in filename and match with original file
    if(str_contains($pathinfo['filename'], '-scaled')) {
        $scaled_file = trailingslashit($upload_dir['basedir']) . $pathinfo['dirname'] . '/' . $pathinfo['basename'];
        if (file_exists($scaled_file)) {
            return false;
        }

        $original_url = trailingslashit($upload_dir['baseurl']) . $pathinfo['dirname'] . '/' . $metadata['original_image'];
        $original_file = trailingslashit($upload_dir['basedir']) . $pathinfo['dirname'] . '/' . $metadata['original_image'];

        if (file_exists($original_file)) {
            return [
                $original_url,
                $metadata['width'], // width
                $metadata['height'], // height
                false // original image
            ];
        }
    }

    return false;
}

/**
 * One-time migration to clean up legacy image sizes
 * This will run once and then never again
 */
add_action('init', function() {
    $migration_key = 'les_verts_image_cleanup_20250701';

    // Check if migration has already run
    if (get_option($migration_key, false)) {
        return; // Already completed, skip
    }

    // Perform the cleanup
    les_verts_cleanup_legacy_image_sizes();

    // Mark migration as completed
    update_option($migration_key, true);
});

/**
 * Clean up legacy image sizes and WordPress defaults we don't need
 */
function les_verts_cleanup_legacy_image_sizes() {
    // Remove WordPress default sizes we don't need based on production audit
    $legacy_sizes = [
        '1536x1536',     // WordPress default large size
        '2048x2048',     // WordPress default extra large size
        'post-thumbnail' // Unused theme size (0x0)
    ];

    foreach ($legacy_sizes as $size) {
        remove_image_size($size);
    }

    // Optional: Clean up database entries for these sizes
    // This removes them from the global $_wp_additional_image_sizes array
    global $_wp_additional_image_sizes;
    foreach ($legacy_sizes as $size) {
        if (isset($_wp_additional_image_sizes[$size])) {
            unset($_wp_additional_image_sizes[$size]);
        }
    }
}
