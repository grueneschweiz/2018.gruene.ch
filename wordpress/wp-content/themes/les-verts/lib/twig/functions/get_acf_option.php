<?php

namespace SUPT;

use Twig\TwigFunction;

// get acf option
add_filter('timber/twig', function($twig) {
    $twig->addFunction(new TwigFunction('get_acf_option', function($option_name) {
        if (!function_exists('get_field')) {
            return null;
        }
        return get_field($option_name, 'option');
    }));
    return $twig;
});
