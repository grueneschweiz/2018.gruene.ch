<?php

add_action( 'admin_init', function () {
	update_option( 'wp-smush-hide_upgrade_notice', 1 );
	update_option( 'wp-smush-hide_update_info', 1 );
}, 10, 0 );
