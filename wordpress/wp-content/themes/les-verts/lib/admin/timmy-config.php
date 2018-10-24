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
add_filter( 'timmy/sizes', function ( $sizes ) {
	return [
		// maximum, used for example by a gallery image
		'static-max'      => [
			'resize'     => [ 2500 ],
			'name'       => 'Maximum Width - not responsive',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],
		
		// full width of the grid
		'full-grid'       => [
			'resize'     => [ 1440 ],
			'srcset'     => [ [ 320 ], [ 512 ], [ 768 ], [ 1024 ], [ 1280 ], [ 1440 ], [ 1792 ], [ 2048 ], [ 2500 ] ],
			'sizes'      => '(max-width: 1679px) 100vw, 1440px',
			'name'       => 'Full Grid Width',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],
		
		// half width of the grid
		'half-grid'       => [
			'resize'     => [ 1440 ],
			'srcset'     => [ [ 320 ], [ 512 ], [ 768 ], [ 1024 ], [ 1280 ] ],
			'sizes'      => '(max-width: 1470px) 100vw, 597px',
			'name'       => 'Half Grid Width',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],
		
		// header image
		'header'          => [
			'resize'     => [ 1440, 720 ],
			'srcset'     => [
				[ 320, 240 ],
				[ 512, 358 ],
				[ 768, 460 ],
				[ 1024, 512 ],
				[ 1280, 640 ],
				[ 1440, 720 ],
				[ 1792, 896 ],
				[ 2048, 1024 ],
				[ 2500, 1250 ]
			],
			'sizes'      => '(max-width: 1679px) 100vw, 1440px',
			'name'       => 'Header image',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],
		
		// Testimonial
		'testimonial'     => [
			'resize'     => [ 288, 260 ],
			'srcset'     => [ [ 288, 260 ], [ 576, 520 ] ],
			'name'       => 'Testimonial',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],
		
		// author image
		'author'          => [
			'resize'     => [ 100, 100 ],
			'srcset'     => [ [ 100, 100 ], [ 200, 200 ] ],
			'name'       => 'Author image',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],
		
		// quote image
		'quote'           => [
			'resize'     => [ 380, 380 ],
			'srcset'     => [ [ 380, 380 ], [ 546, 546 ], [ 720, 720 ] ],
			'name'       => 'Author image',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],
		
		// admin thumbnail
		'admin-thumbnail' => [
			'resize'     => [ 320 ],
			'name'       => 'Admin Thumbnail',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],
		
		// large - this is used by yoast as og image
		'large'           => [
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
