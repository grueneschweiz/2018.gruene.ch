<?php

namespace SUPT;

// customize toolbars
add_filter( 'acf/fields/wysiwyg/toolbars', function( $toolbars ) {
	$toolbars['Full'] = array(
		1 => array(
			'formatselect',
			'bold',
			'italic',
			'bullist',
			'numlist',
			'blockquote',
			'alignleft',
			'aligncenter',
			'alignright',
			'link',
			'unlink',
			'spellchecker',
			'fullscreen',
			'wp_adv'
		),
		2 => array(
			'styleselect',
			'strikethrough',
			'underline',
			'pastetext',
			'removeformat',
			'charmap',
			'outdent',
			'indent',
			'undo',
			'redo',
			'wp_help',
		),
	);
	
	return $toolbars;
} );


// custom js
add_action( 'acf/input/admin_footer', function() {
	?>
	<script type="text/javascript">
		acf.add_filter('wysiwyg_tinymce_settings', function( mceInit, id, $field ) {
			mceInit.style_formats = [
				{
					title: "Button Green",
					selector: "a",
					classes: "a-button a-button--primary"
				},
				{
					title: "Button Magenta",
					selector: "a",
					classes: "a-button a-button--secondary"
				},
				{
					title: "Button Outline Green",
					selector: "a",
					classes: "a-button a-button--primary a-button-outline"
				},
				{
					title: "Button Outline Magenta",
					selector: "a",
					classes: "a-button a-button--secondary a-button-outline"
				}
			];
			return mceInit;
		});
	</script>
	<?php
} );

// custom css
// todo: include backend styles
//add_filter( 'tiny_mce_before_init', function( $mce_init ) {
//	$styleguide_css = get_stylesheet_directory_uri() . '/static/main.min.css';
//	$content_css = get_stylesheet_directory_uri() . '/style-admin-editor.css';
//	if (isset($mce_init['content_css'])) {
//		$content_css_new =  $mce_init[ 'content_css' ].','.$styleguide_css.','.$content_css;
//	}
//	$mce_init[ 'content_css' ] = $content_css_new;
//	$mce_init['body_class'] .= ' u-typography';
//
//	return $mce_init;
//} );
