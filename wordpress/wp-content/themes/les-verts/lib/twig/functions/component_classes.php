<?php

namespace SUPT;

use Twig\TwigFunction;

/**
 * Returns a string with all classes for a component
 *
 * @param string $base_class
 * @param array $modifiers
 * @param array $extra_classes
 *
 * @return string
 */
function component_classes($base_class, $modifiers = [], $extra_classes = []) {
    $classes = [$base_class];

    // add modifiers
    if (is_array($modifiers)) {
        foreach ($modifiers as $modifier) {
            if ($modifier) {
                $classes[] = $base_class . '--' . $modifier;
            }
        }
    }

    // add extra classes
    if (is_array($extra_classes)) {
        $classes = array_merge($classes, $extra_classes);
    }

    return implode(' ', array_unique($classes));
}

// Add the function to Twig
add_filter('timber/twig', function($twig) {
    $twig->addFunction(new TwigFunction('component_classes', 'SUPT\component_classes'));
    return $twig;
});
