#!/usr/bin/env bash

# set time zone
$WPCLI option update timezone_string "Europe/Zurich"

# set permalink structure
$WPCLI rewrite structure '/%category%/%postname%'

# set upload limit to 64 MB
if [ $NETWORK ]; then
    $WPCLI network meta update 1 fileupload_maxk 65536
fi

# configure YOAST meta description fields
$WPCLI option patch update wpseo_titles metadesc-tribe_events <<< "%%cf_description%%"
$WPCLI option patch update wpseo_titles metadesc-post <<< "%%cf_teaser%%"
$WPCLI option patch update wpseo_titles metadesc-page <<< "%%cf_teaser%%"
$WPCLI option patch update wpseo_titles breadcrumbs-home <<< "Front Page"
$WPCLI option patch update wpseo enable_admin_bar_menu <<< "false"
$WPCLI option patch update wpseo_titles disable-author <<< "true"
$WPCLI option patch update wpseo_titles disable-date <<< "true"
$WPCLI option patch update wpseo_titles disable-attachment <<< "true"
$WPCLI option patch update wpseo_titles post_types-post-maintax <<< "category"

# disable comment everywhere
if [ $NETWORK ]; then
    $WPCLI site option patch update disable_comments_options remove_everywhere <<< "true"
else
    $WPCLI option patch update disable_comments_options remove_everywhere <<< "true"
fi

# hide 'enable media replace' ads
$WPCLI option update emr_news 1

# configure searchwp
$WPCLI eval-file 'wp-content/themes/les-verts/lib/searchwp/configure.php'
