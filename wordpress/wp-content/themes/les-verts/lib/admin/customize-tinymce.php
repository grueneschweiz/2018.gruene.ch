<?php

namespace SUPT;

// customize toolbars
add_filter( 'acf/fields/wysiwyg/toolbars', function ( $toolbars ) {
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
			'subscript',
			'superscript',
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


// remove unused block formats
add_filter( 'tiny_mce_before_init', function ( $init ) {
	$init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;';

	return $init;
} );


// allow &nbsp; and &shy;
add_filter( 'tiny_mce_before_init', function ( $init ) {
	$init['entities']        = '160,nbsp,173,shy';
	$init['entity_encoding'] = 'named';

	return $init;
} );

// custom js
add_action( 'acf/input/admin_footer', function () {
	?>
	<script type="text/javascript">
		acf.add_filter( 'wysiwyg_tinymce_settings', function( mceInit, id, $field ) {
			mceInit.style_formats = [
				{
					title: 'Button Green',
					selector: 'a',
					classes: 'a-button a-button--primary',
				},
				{
					title: 'Button Magenta',
					selector: 'a',
					classes: 'a-button a-button--secondary',
				},
				{
					title: 'Button Outline Green',
					selector: 'a',
					classes: 'a-button a-button--primary a-button--outline',
				},
				{
					title: 'Button Outline Magenta',
					selector: 'a',
					classes: 'a-button a-button--secondary a-button--outline',
				},
			];
			return mceInit;
		} );
	</script>
	<?php
} );

// custom css
add_filter( 'tiny_mce_before_init', function ( $mce_init ) {
	$styleguide_css = get_stylesheet_directory_uri() . '/static/style' . ( WP_DEBUG ? '' : '.min' ) . '.css';
	$content_css    = get_stylesheet_directory_uri() . '/style-admin-editor.css';

	if ( isset( $mce_init['content_css'] ) ) {
		$styles = $mce_init['content_css'] . ',' . $styleguide_css . ',' . $content_css;
	} else {
		$styles = $styleguide_css . ',' . $content_css;
	}

	$mce_init['content_css'] = $styles;

	return $mce_init;
} );
