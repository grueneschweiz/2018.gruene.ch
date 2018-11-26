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
#
# PARAMETERS:
# -n treat this installation as multi site
#    (network)
# -l this is a multilingual setup
#===========================================

set -e # exit on error
set -u # treat undefined variables as errors
set -o pipefail # only consider pipe successful if all commands involved were successful

INSTALL_ACTIVATE_ARGUMENT="--activate"
ACTIVATE_NETWORK_ARGUMENT=""
NETWORK=
MULTILANG=

while getopts "nl" opt; do
  case $opt in
    n)
    	NETWORK=1
    	ACTIVATE_ARGUMENT="--activate-network"
    	ACTIVATE_NETWORK_ARGUMENT="--network"
      echo "WP multi site support enabled."
      ;;
    l)
    	MULTILANG=1
      echo "Multi language support enabled."
      ;;
    ?)
      echo "Invalid option: -$OPTARG" >&2
      exit 1
      ;;
  esac
done

#============================
# Ensure proprietary plugins are uploaded
#============================

POLYLANG=`$WPCLI plugin list | grep polylang-pro`
ACF=`$WPCLI plugin list | grep advanced-custom-fields-pro`
SEARCHWP=`$WPCLI plugin list | grep searchwp`
MISSING=0

RED='\033[0;31m'
YELLOW='\033[1;33m'

if [ ! "$POLYLANG" ] && [ $MULTILANG ];
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
if [ $MULTILANG ];
then
$WPCLI plugin activate polylang-pro $ACTIVATE_NETWORK_ARGUMENT
# TODO: activate license key
fi

## Advanced Custom Fields PRO
$WPCLI plugin activate advanced-custom-fields-pro $ACTIVATE_NETWORK_ARGUMENT
# $WPCLI eval 'acf_pro_update_license("INSERT LICENSE NUMBER HERE");'

## WP CLI command to sync advanced custom fields
$WPCLI package install git@github.com:superhuit-ch/wp-cli-acf-json.git

## Timber
$WPCLI plugin install timber-library --version=1.7.1 $INSTALL_ACTIVATE_ARGUMENT

## SVG support
$WPCLI plugin install svg-support $INSTALL_ACTIVATE_ARGUMENT

# YAOST seo plugin
$WPCLI plugin install wordpress-seo $INSTALL_ACTIVATE_ARGUMENT

# Make yoast SEO work with acf
$WPCLI plugin install acf-content-analysis-for-yoast-seo $INSTALL_ACTIVATE_ARGUMENT

# Smush Image Compression
$WPCLI plugin install wp-smushit $INSTALL_ACTIVATE_ARGUMENT

# Disable emojis code bloat
$WPCLI plugin install disable-emojis $INSTALL_ACTIVATE_ARGUMENT

# Disable embed code bloat
$WPCLI plugin install disable-embeds $INSTALL_ACTIVATE_ARGUMENT

# Wordpress importer
$WPCLI plugin install wordpress-importer $INSTALL_ACTIVATE_ARGUMENT

# Theme and plugin translation for polylang
$WPCLI plugin install theme-translation-for-polylang $INSTALL_ACTIVATE_ARGUMENT

# Disable comments system
$WPCLI plugin install disable-comments $INSTALL_ACTIVATE_ARGUMENT

# Events
$WPCLI plugin install the-events-calendar $INSTALL_ACTIVATE_ARGUMENT

# Duplicate posts
$WPCLI plugin install post-duplicator $INSTALL_ACTIVATE_ARGUMENT

# Disable gutenberg
$WPCLI plugin install classic-editor $INSTALL_ACTIVATE_ARGUMENT

# SearchWP
$WPCLI plugin activate searchwp $ACTIVATE_NETWORK_ARGUMENT

# Enhanced media library
$WPCLI plugin install enhanced-media-library $INSTALL_ACTIVATE_ARGUMENT

#====================
# Enable theme
#====================
$WPCLI theme enable les-verts $ACTIVATE_NETWORK_ARGUMENT

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
