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
# Ensure proprietary plugins are uploaded
#============================

POLYLANG=`$WPCLI plugin list | grep polylang-pro`
ACF=`$WPCLI plugin list | grep advanced-custom-fields-pro`
SEARCHWP=`$WPCLI plugin list | grep searchwp`
MISSING=0

RED='\033[0;31m'
YELLOW='\033[1;33m'

if [ ! "$POLYLANG" ];
then
      echo -e "${RED}ERROR: ${YELLOW}Missing plugin 'polylang-pro'. Upload it, then rerun this script."
      MISSING=1
fi

if [ ! "$ACF" ];
then
      echo -e "${RED}ERROR: ${YELLOW}Missing plugin 'advanced-custom-fields-pro'. Upload it, then rerun this script."
      MISSING=1
fi

if [ ! "$SEARCHWP" ];
then
      echo -e "${RED}ERROR: ${YELLOW}Missing plugin 'searchwp'. Upload it, then rerun this script."
      MISSING=1
fi

if [ "$MISSING" -eq "1" ];
then
	exit 1 || return 1
fi

#============================
# Install & activate plugins
#============================

# Polylang
$WPCLI plugin activate polylang-pro --network
# TODO: activate license key

## Advanced Custom Fields PRO
$WPCLI plugin activate advanced-custom-fields-pro --network
# $WPCLI eval 'acf_pro_update_license("INSERT LICENSE NUMBER HERE");'

## WP CLI command to sync advanced custom fields
$WPCLI package install git@github.com:superhuit-ch/wp-cli-acf-json.git

## Timber
$WPCLI plugin install timber-library --version=1.7.1 --activate-network

## SVG support
$WPCLI plugin install svg-support --activate-network

# YAOST seo plugin
$WPCLI plugin install wordpress-seo --activate-network

# Make yoast SEO work with acf
$WPCLI plugin install acf-content-analysis-for-yoast-seo --activate-network

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

# Events
$WPCLI plugin install the-events-calendar --activate-network

# Duplicate posts
$WPCLI plugin install post-duplicator --activate-network

# Disable gutenberg
$WPCLI plugin install classic-editor --activate-network

# SearchWP
$WPCLI plugin activate searchwp --network

# Enhanced media library
$WPCLI plugin install enhanced-media-library --activate-network

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

# sync acf fields
$WPCLI acf-json sync --all_sites

# configure YOAST meta description fields
$WPCLI option patch update wpseo_titles metadesc-tribe_events <<< "%%cf_description%%"
$WPCLI option patch update wpseo_titles metadesc-post <<< "%%cf_teaser%%"
$WPCLI option patch update wpseo_titles metadesc-page <<< "%%cf_teaser%%"
$WPCLI option patch update wpseo_titles breadcrumbs-home <<< "Front Page"
$WPCLI option patch update wpseo enable_admin_bar_menu <<< "false"
$WPCLI option patch update wpseo_titles disable-author <<< "true"
$WPCLI option patch update wpseo_titles disable-date <<< "true"
$WPCLI option patch update wpseo_titles disable-attachment <<< "true"
$WPCLI option patch update wpseo_titles hideeditbox-tax-media_category <<< "true"
$WPCLI option patch update wpseo_titles post_types-post-maintax <<< "category"

# disable comment everywhere
$WPCLI option patch update disable_comments_options remove_everywhere <<< "true"
