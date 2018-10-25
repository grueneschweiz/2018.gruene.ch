<?php

// TIMMY CONFIGURATION
// Read more about it here: https://github.com/mindkomm/timmy#getting-startedpreparations

// reset thumbnail size
set_post_thumbnail_size( 0, 0 );

// never set the style_attr for an image
add_filter( 'timmy/oversize', function ( $oversize ) {
	$oversize['style_attr'] = false;
	
	return $oversize;
} );

// make the full size unavailable when an image is inserted into the WordPress Editor
add_filter( 'image_size_names_choose', function ( $sizes ) {
	unset( $sizes['full'] );
	
	return $sizes;
}, 11 );

// configure sizes
// NOTE: these are generated on image load as well as on page load if not existing
// REMARK: please read this before adding unnecessary sizes: https://github.com/mindkomm/timmy#using-an-image-size-array-instead-of-a-key
add_filter( 'timmy/sizes', function () {
	return [
		// full width
		'full-width' => [
			'resize'     => [ 2560 ],
			'srcset'     => [ [ 640 ], [ 768 ], [ 1024 ], [ 1680 ], [ 2048 ], [ 2560 ] ],
			'name'       => 'Full width',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],
		
		// regular
		'regular'    => [
			'resize'     => [ 790 ],
			'srcset'     => [ [ 100 ], [ 200 ], [ 400 ], [ 640 ], [ 768 ], [ 1024 ], [ 1580 ] ],
			'name'       => 'Regular width',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],
		
		// admin thumbnail
		'thumbnail'  => [
			'resize'     => [ 150 ],
			'name'       => 'Admin Thumbnail',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],
		
		// large - this is used by yoast as og image
		'large'      => [
			'resize'     => [ 1200, 1200 ],
			'name'       => 'Open Graph image',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		]
	];
} );

// reset image sizes options
$timmy_reset_options = [
	'thumbnail_size_w',
	'thumbnail_size_h',
	'medium_size_w',
	'medium_size_h',
	'medium_large_size_h',
	'medium_large_size_w',
	'large_size_w',
	'large_size_h'
];
foreach ( $timmy_reset_options as $opt ) {
	add_filter( 'pre_option_' . $opt, function ( $option ) {
		return 0;
	} );
}
