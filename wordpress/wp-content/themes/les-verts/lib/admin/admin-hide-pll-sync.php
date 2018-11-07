<?php

/* Polylang v2.2.1+ adds a feature we don't want: synchronization. Let's hide it. */
add_action('admin_head', function() {
	echo '<style>.pll-sync-column {display: none !important;}</style>';
});
