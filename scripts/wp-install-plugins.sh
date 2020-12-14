#!/bin/bash

#===========================================
# Install plugins and remove unused stuff
#===========================================
#
# README:
# * this script is executed on the server
#   during deploy AND locally (in docker)
# * this script should not break things on
#   existing website in production
#
# PARAMETERS:
# -l this is a multilingual setup
#===========================================

set -e

ACTIVATE_NETWORK=
INSTALL_ACTIVATE="--activate"
NETWORK=
MULTILANG=

# detect if it's a multisite installation
if wp site list; then
	NETWORK=1
	ACTIVATE_NETWORK="--network"
	INSTALL_ACTIVATE="--activate-network"
	echo "WP multisite detected."
fi

while getopts "nl" opt; do
	case $opt in
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

POLYLANG=$($WPCLI plugin list | grep polylang-pro) || POLYLANG=
ACF=$($WPCLI plugin list | grep advanced-custom-fields-pro) || ACF=
SEARCHWP=$($WPCLI plugin list | grep searchwp) || SEARCHWP=
SEARCHWP_POLYLANG=$($WPCLI plugin list | grep searchwp-polylang) || SEARCHWP_POLYLANG=
MISSING=0

set -e # exit on error
set -u # treat undefined variables as errors
set -o pipefail # only consider pipe successful if all commands involved were successful

RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0;00m'

if [ ! "$POLYLANG" ] && [ $MULTILANG ];
then
      echo -e "${RED}ERROR: ${YELLOW}Missing plugin 'polylang-pro'. Upload it, then rerun this script.${NC}"
      MISSING=1
fi

if [ ! "$ACF" ];
then
      echo -e "${RED}ERROR: ${YELLOW}Missing plugin 'advanced-custom-fields-pro'. Upload it, then rerun this script.${NC}"
      MISSING=1
fi

if [ ! "$SEARCHWP" ];
then
      echo -e "${RED}ERROR: ${YELLOW}Missing plugin 'searchwp'. Upload it, then rerun this script.${NC}"
      MISSING=1
fi

if [ ! "$SEARCHWP_POLYLANG" ];
then
      echo -e "${RED}ERROR: ${YELLOW}Missing plugin 'searchwp-polylang'. Upload it, then rerun this script.${NC}"
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
if [ $MULTILANG ]; then
	$WPCLI plugin activate polylang-pro $ACTIVATE_NETWORK
fi

## Advanced Custom Fields PRO
$WPCLI plugin activate advanced-custom-fields-pro $ACTIVATE_NETWORK
# $WPCLI eval 'acf_pro_update_license("INSERT LICENSE NUMBER HERE");'

## SVG support
$WPCLI plugin install svg-support $INSTALL_ACTIVATE

# YAOST seo plugin
$WPCLI plugin install wordpress-seo $INSTALL_ACTIVATE

# Make yoast SEO work with acf
$WPCLI plugin install acf-content-analysis-for-yoast-seo $INSTALL_ACTIVATE

# Smush Image Compression
$WPCLI plugin install wp-smushit $INSTALL_ACTIVATE

# Disable emojis code bloat
$WPCLI plugin install disable-emojis $INSTALL_ACTIVATE

# Disable embed code bloat
$WPCLI plugin install disable-embeds $INSTALL_ACTIVATE

# Wordpress importer
$WPCLI plugin install wordpress-importer

# Theme and plugin translation for polylang
if [ $MULTILANG ]; then
	$WPCLI plugin install theme-translation-for-polylang $INSTALL_ACTIVATE
fi

# Disable comments system
$WPCLI plugin install disable-comments $INSTALL_ACTIVATE

# Events
$WPCLI plugin install the-events-calendar $INSTALL_ACTIVATE

# Duplicate posts
$WPCLI plugin install post-duplicator $INSTALL_ACTIVATE

# Disable gutenberg
$WPCLI plugin install classic-editor $INSTALL_ACTIVATE

# SearchWP
$WPCLI plugin activate searchwp $ACTIVATE_NETWORK
if [ $MULTILANG ]; then
	$WPCLI plugin activate searchwp-polylang $ACTIVATE_NETWORK
fi

# Enable media replace
$WPCLI plugin install enable-media-replace $INSTALL_ACTIVATE

# ACF Code Field
$WPCLI plugin install acf-code-field $INSTALL_ACTIVATE

# Disable Administration Email Verification Prompt
$WPCLI plugin install disable-administration-email-verification-prompt $INSTALL_ACTIVATE

# Limit Login Attempts
$WPCLI plugin install limit-login-attempts-reloaded $INSTALL_ACTIVATE

# Maintenance Mode
$WPCLI plugin install wp-maintenance-mode

#====================
# Theme
#====================
if [ $NETWORK ]; then
    $WPCLI theme enable les-verts $ACTIVATE_NETWORK
fi

$WPCLI theme activate les-verts

#====================
# Delete unused themes & extensions
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
