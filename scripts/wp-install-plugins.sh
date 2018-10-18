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
$WPCLI plugin install timber-library --version=1.7.1 --activate-network

## SVG support
$WPCLI plugin install svg-support --activate-network

# YAOST seo plugin
$WPCLI plugin install wordpress-seo --activate-network
$WPCLI option patch update wpseo_titles breadcrumbs-home <<< "Front Page"
$WPCLI option patch update wpseo enable_admin_bar_menu <<< "false"

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
$WPCLI option patch update disable_comments_options remove_everywhere <<< "true"

# Events
$WPCLI plugin install the-events-calendar --activate-network

# Duplicate posts
$WPCLI plugin install post-duplicator --activate-network

# Disable gutenberg
$WPCLI plugin install classic-editor --activate-network

# SearchWP
$WPCLI plugin activate searchwp --network

# Enhanced media library
plugin install enhanced-media-library --activate-network

#====================
# Enable theme
#====================
$WPCLI theme enable les-verts --network

#====================
# Setup default site
#====================
$WPCLI option update timezone_string "Europe/Zurich"
$WPCLI theme activate les-verts

#====================
# Delete useless themes & extensions
#====================
$WPCLI theme delete twentyfifteen
$WPCLI theme delete twentysixteen
$WPCLI theme delete twentyseventeen
$WPCLI plugin uninstall akismet --deactivate
$WPCLI plugin uninstall hello --deactivate

#====================
# Configrue
#====================
# set permalink structure
$WPCLI rewrite structure '%postname%'

# set upload limit to 64 MB
$WPCLI network meta update 1 fileupload_maxk 65536
