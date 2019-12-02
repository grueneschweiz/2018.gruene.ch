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

INSTALL_ACTIVATE_ARGUMENT="--activate"
ACTIVATE_NETWORK_ARGUMENT=
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

set -e # exit on error
set -u # treat undefined variables as errors
set -o pipefail # only consider pipe successful if all commands involved were successful

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

# Enable media replace
$WPCLI plugin install enable-media-replace $INSTALL_ACTIVATE_ARGUMENT

# ACF Code Field
$WPCLI plugin install acf-code-field $INSTALL_ACTIVATE_ARGUMENT

#====================
# Theme
#====================
if [ $NETWORK ]; then
    $WPCLI theme enable les-verts $ACTIVATE_NETWORK_ARGUMENT
fi

$WPCLI theme activate les-verts

#====================
# Delete useless themes & extensions
#====================
$WPCLI theme delete twentyfifteen
$WPCLI theme delete twentysixteen
$WPCLI theme delete twentyseventeen
$WPCLI theme delete twentynineteen
$WPCLI theme delete twentytwenty

if $WPCLI plugin is-installed akismet; then
    $WPCLI plugin uninstall akismet --deactivate
fi

if $WPCLI plugin is-installed hello; then
    $WPCLI plugin uninstall hello --deactivate
fi
