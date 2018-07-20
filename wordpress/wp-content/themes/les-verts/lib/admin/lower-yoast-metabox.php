<?php

// Lower the Yoast Metabox to be shown after the normal content
add_filter( 'wpseo_metabox_prio', function() {
	return 'low';
});
