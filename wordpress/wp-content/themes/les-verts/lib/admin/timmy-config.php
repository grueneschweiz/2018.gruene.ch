<?php

// TIMMY CONFIGURATION
// Read more about it here: https://github.com/mindkomm/timmy#getting-startedpreparations

// reset thumbnail size
set_post_thumbnail_size( 0, 0 );

// never set the style_attr for an image
add_filter( 'timmy/oversize', function( $oversize ) {
	$oversize['style_attr'] = false;
	return $oversize;
} );

// make the full size unavailable when an image is inserted into the WordPress Editor
add_filter( 'image_size_names_choose', function( $sizes ) {
	unset( $sizes['full'] );
	return $sizes;
}, 11 );

// configure sizes
// NOTE: these are generated on image load as well as on page load if not existing
// REMARK: please read this before adding unnecessary sizes: https://github.com/mindkomm/timmy#using-an-image-size-array-instead-of-a-key
add_filter( 'timmy/sizes', function( $sizes ) {
	return [

		// maximum, used for example by a gallery image
		'static-max' => [
			'resize' => [ 2500 ],
			'name' => 'Maximum Width - not responsive',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// full width of the grid
		'full-grid' => [
			'resize' => [ 1440 ],
			'srcset' => [ [320], [512], [768], [1024], [1280], [1440], [1792], [2048], [2500] ],
			'sizes' => '(max-width: 1679px) 100vw, 1440px',
			'name' => 'Full Grid Width',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],

		// full width of the grid - large
		'full-grid-large' => [
			'resize' => [ 1680 ],
			'srcset' => [ [320], [512], [768], [1024], [1280], [1440], [1792], [2048], [2500] ],
			'sizes' => '(max-width: 1679px) 100vw, 1680px',
			'name' => 'Full Grid Large Width',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// full width of the screen
		'full-screen' => [
			'resize' => [ 1024 ],
			'srcset' => [ [320], [512], [768], [1024], [2048], [2500] ],
			'sizes' => '100vw',
			'name' => 'Full Screen Width',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// full width of the screen - mobile only, used for header
		'full-screen-mobile' => [
			'resize' => [ 1024 ],
			'srcset' => [ [320], [512], [768], [1024], [1280], [1440], [1792] ],
			'sizes' => '100vw',
			'name' => 'Full Screen Width Mobile Only',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// full screen on mobile, then half screen
		'full-half-screen' => [
			'resize' => [ 1024 ],
			'srcset' => [ [320], [512], [768], [1024], [1280], [1440], [1792] ],
			'sizes' => '(max-width: 767px) 100vw, (max-width: 1679px) 50vw, 940px',
			'name' => 'Mobile Full Screen - Desktop Half Screen',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// widgets image
		'widget-full' => [
			'resize' => [ 320 ],
			'srcset' => [ [640] ],
			'sizes' => '320px',
			'name' => 'Full Widget Width',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// small thumbnail
		// note: used in widget listing, team members,…
		'small-thumbnail' => [
			'resize' => [ 150 ],
			'srcset' => [ [320] ],
			'sizes' => '150px',
			'name' => 'Small Thumbnail',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// gifthandle
		'gifthandle' => [
			'resize' => [ 460 ],
			'srcset' => [ [320], [512], [768] ],
			'sizes' => '(max-width: 499px) 100vw, (max-width: 1023px) 50vw, (max-width: 1679px) 30vw, 460px',
			'name' => 'Gift Handle',
			'post_types' => [ 'all' ],
			'show_in_ui' => false,
		],

		// admin thumbnail
		'admin-thumbnail' => [
			'resize' => [ 320 ],
			'name' => 'Admin Thumbnail',
			'post_types' => [ 'all' ],
			'show_in_ui' => true,
		],

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
