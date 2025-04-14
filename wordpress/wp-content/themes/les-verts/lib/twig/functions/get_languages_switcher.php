<?php

namespace SUPT;

use Twig\TwigFunction;

/**
 * Returns an array with all available languages and their urls
 *
 * USAGE:
 * - in php `SUPT\get_languages_switcher()`
 * - in twig `{{ get_languages_switcher() }}`
 */
function get_languages_switcher() {
    if (!function_exists('pll_the_languages')) {
        return [];
    }

    return pll_the_languages([
        'raw' => 1,
        'hide_if_empty' => 0,
    ]);
}

// Add the function to Twig
add_filter('timber/twig', function($twig) {
    $twig->addFunction(new TwigFunction('get_languages_switcher', 'SUPT\get_languages_switcher'));
    return $twig;
});
