<?php

/**
 * Show the name of the person in the blocks title (in the backend)
 */
add_filter('acf/fields/flexible_content/layout_title', function ($title, $field, $layout) {
	if ($layout['key'] !== 'layout_5bbf722de1080') {
		return $title;
	}

	$post = get_sub_field('person');
	if ($post) {
		$post_title = get_the_title($post);
	}


	return $post_title ? "$title - <small>$post_title</small>" : $title;
}, 10, 3);
