<?php

namespace SUPT;

use Timber\Timber;
use Twig\TwigFunction;

add_filter('timber/twig', function($twig) {
    // Register ACFPost type
    $twig->addFunction(new TwigFunction('ACFPost', function($post) {
        if (!$post) {
            return null;
        }
        return Timber::get_post($post->ID);
    }));

    // Register PostQuery type
    $twig->addFunction(new TwigFunction('PostQuery', function($query) {
        if (!$query) {
            return null;
        }
        return new SUPTPostQuery($query);
    }));

    // Register TribeEvent type
    $twig->addFunction(new TwigFunction('TribeEvent', function($post) {
        if (!$post) {
            return null;
        }
        return Timber::get_post($post->ID);
    }));

    return $twig;
});
