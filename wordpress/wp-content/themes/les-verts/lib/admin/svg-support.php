<?php

add_action( 'admin_init', function () {
	update_option( 'bodhi_svgs_admin_notice_dismissed', 1 );
}, 10, 0 );
