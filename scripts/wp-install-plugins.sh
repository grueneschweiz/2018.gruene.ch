#!/bin/bash

#===========================================
# Install & prepare everything
#===========================================
#
# README:
# * this script is executed on the server
#   during deploy AND locally (in docker)
# * this script should not break things on
#   existing website in production
#===========================================

#============================
# Install & activate plugins
#============================

# Polylang
$WPCLI plugin activate polylang-pro --network
# TODO: activate license key



## Advanced Custom Fields PRO
$WPCLI plugin activate advanced-custom-fields-pro --network
# $WPCLI eval 'acf_pro_update_license("INSERT LICENSE NUMBER HERE");'

## Timber
$WPCLI plugin install timber-library --activate-network

## SVG support
$WPCLI plugin install svg-support --activate-network

# YAOST seo plugin
$WPCLI plugin install wordpress-seo --activate-network

# Smush Image Compression
$WPCLI plugin install wp-smushit --activate-network

# Disable emojis code bloat
$WPCLI plugin install disable-emojis --activate-network

# Disable embed code bloat
$WPCLI plugin install disable-embeds --activate-network

# Wordpress importer
$WPCLI plugin install wordpress-importer --activate-network

# Theme and plugin translation for polylang
$WPCLI plugin install theme-translation-for-polylang --activate-network

# Disable comments system
$WPCLI plugin install disable-comments --activate-network
$WPCLI option update disable_comments_options '{"disabled_post_types":["post","page","attachment"],"remove_everywhere":true,"permanent":false,"extra_post_types":false,"db_version":6}' --format=json

# Events
$WPCLI plugin install the-events-calendar --activate-network

# Duplicate posts
$WPCLI plugin install post-duplicator --activate-network

# Delete useless themes & extensions
$WPCLI theme delete twentyfifteen
$WPCLI theme delete twentysixteen
$WPCLI theme delete twentyseventeen
$WPCLI plugin uninstall akismet --deactivate
$WPCLI plugin uninstall hello --deactivate


#====================
# Setup default site
#====================
# $WPCLI option update timezone_string "Europe/Zurich"
$WPCLI theme enable les-verts --network


#=================
# Setup all sites
# NOTE: runs only if sites aren't setup already
# NOTE 2: Needs to be defined accordingly to available sites
#=================

# # Stop the script if more than 1 site exist
# if [[ $($WPCLI site list --format=count) > 1 ]]; then
#     echo "Warning: More than 1 sites exist already, skipping the multisites creation.";
#     exit 0;
# fi

# # Define variables
# sites_slugs=('_ch' '_us' '_ca' '_uk' '_nl' '_de' '_fr')
# sites_titles=('Switzerland' 'United-States' 'Canada' 'United-Kingdom' 'Netherlands' 'Germany' 'France')
# sites_langs=('fr_FR' 'en_US' 'en_CA' 'en_GB' 'nl_NL' 'de_DE' 'fr_FR')
# # sites_pll=('fr_FR' 'en_US' 'en_CA' 'en_GB' 'nl_NL' 'de_DE' 'fr_FR') # polylang languages
# sites_timezones=('Europe/Zurich' 'America/New_York' 'America/Montreal' 'Europe/London' 'Europe/Amsterdam' 'Europe/Berlin' 'Europe/Paris')

# # Create & configure the sites
# arraylength=${#sites_slugs[@]}
# for (( i=0; i<${arraylength}; i++ )); do
#     url="${BASE_URL}/${sites_slugs[$i]}"
#     echo "---"
#     echo "Setting up ${url} with…"
#     echo "- slug: ${sites_slugs[$i]}"
#     echo "- title: ${sites_titles[$i]}"
#     echo "- lang: ${sites_langs[$i]}"
#     echo "- timezone: ${sites_timezones[$i]}"
#     $WPCLI site create --slug="${sites_slugs[$i]}" --title="${sites_titles[$i]}"
#     $WPCLI language core install "${sites_langs[$i]}" --url="${url}" --activate
#     $WPCLI option update timezone_string "${sites_timezones[$i]}" --url="${url}"
#     $WPCLI theme activate medair --url="${url}"
# done
