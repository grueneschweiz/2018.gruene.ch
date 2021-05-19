#!/usr/bin/env bash

#===========================================
# Configure installation
#===========================================
#
# README:
# * this script should not break things on
#   existing website in production
#===========================================

set -euo pipefail

# detect if it's a multisite installation
NETWORK=
if $WPCLI site list; then
	NETWORK=1
	echo "WP multisite detected."
fi

# set time zone
$WPCLI option update timezone_string "Europe/Zurich"

# set permalink structure
$WPCLI rewrite structure '/%category%/%postname%'

# set upload limit to 64 MB
if [ $NETWORK ]; then
    $WPCLI network meta update 1 fileupload_maxk 65536
fi

# allow site admins to add new users
if [ $NETWORK ]; then
    $WPCLI site option set add_new_users 1
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
    $WPCLI site option update --json disable_comments_options <<< '{"disabled_post_types":["post","page","attachment"],"remove_everywhere":true,"extra_post_types":false,"db_version":6}'
else
    $WPCLI option update disable_comments_options <<< '{"disabled_post_types":["post","page","attachment"],"remove_everywhere":true,"extra_post_types":false,"db_version":6}'
fi

# hide 'enable media replace' ads
$WPCLI option update emr_news 1

# configure tribe events
$WPCLI option patch insert --json tribe_events_calendar_options did_init true
$WPCLI option patch insert tribe_events_calendar_options tribeEventsTemplate default
$WPCLI option patch insert --json tribe_events_calendar_options showComments false
$WPCLI option patch insert  tribe_events_calendar_options defaultCurrencySymbol CHF
$WPCLI option patch insert --json tribe_events_calendar_options reverseCurrencyPosition true
$WPCLI option patch insert --json tribe_events_calendar_options embedGoogleMaps false
$WPCLI option patch insert tribe_events_calendar_options tribe_events_timezone_mode site
$WPCLI option patch insert --json tribe_events_calendar_options tribe_events_timezones_show_zone false
$WPCLI option patch insert tribe_events_calendar_options stylesheet_mode skeleton
$WPCLI option patch insert --json tribe_events_calendar_options tribeDisableTribeBar true
$WPCLI option patch insert --json tribe_events_calendar_options donate-link false
$WPCLI option patch insert tribe_events_calendar_options viewOption list
$WPCLI option patch insert tribe_events_calendar_options datepickerFormat "11"
$WPCLI option patch insert --json tribe_events_calendar_options views_v2_enabled false

# configure searchwp
$WPCLI option set --json searchwp_engines '{"default":{"label":"Default","settings":{"stemming":true,"adminengine":false},"sources":{"post.post":{"attributes":{"title":80,"content":5,"slug":60,"excerpt":40,"meta":{"*":1},"taxonomy":{"category":50,"post_tag":50}},"rules":[],"options":[]},"post.page":{"attributes":{"title":80,"content":5,"slug":60,"meta":{"*":1}},"rules":[],"options":[]},"post.tribe_events":{"attributes":{"title":80,"content":5,"slug":60,"excerpt":40,"comments":1,"meta":{"*":1}},"rules":[],"options":[]}}}}'

# configure wp super cache
$WPCLI super-cache enable
$WPCLI eval 'wp_cache_setting("cache_max_time",86400);'
$WPCLI eval 'wp_cache_setting("cache_time_interval",3600);'
$WPCLI eval 'wp_cache_setting("wp_cache_not_logged_in",2);'
$WPCLI eval 'wp_cache_setting("cache_rebuild_files",1);'
$WPCLI eval 'wp_cache_setting("wp_cache_clear_on_post_edit",1);'
