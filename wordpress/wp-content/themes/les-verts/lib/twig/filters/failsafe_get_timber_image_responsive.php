<?php
declare( strict_types=1 );

namespace SUPT;

use Twig\TwigFilter;
use Twig\Environment;
use SUPT\Twig\ImageFilters;

add_filter( 'get_twig', function ( $twig ) {
    $image_filters = new ImageFilters();
    
    $twig->addFilter(
        new TwigFilter( 'failsafe_get_timber_image_responsive', 
            function (Environment $env, $image, $size = 'full-width-2560x0') use ($image_filters) {
                return $image_filters->getTimberImageResponsive($env, $image, $size);
            },
            ['needs_environment' => true]
        )
    );
    
    return $twig;
});