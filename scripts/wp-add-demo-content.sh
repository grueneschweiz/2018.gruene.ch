#!/usr/bin/env bash

# Perpare for import
# ==================
$WPCLI plugin activate wordpress-importer

# Navigation
# ==========
$WPCLI menu create main-navigation <<< "Main navigation"
$WPCLI menu location assign main-navigation main-nav

$WPCLI menu create footer-navigation <<< "Footer navigation"
$WPCLI menu location assign footer-navigation footer-meta-nav

$WPCLI menu create language-navigation <<< "Language navigation"
$WPCLI menu location assign language-navigation language-nav

# Content
# =======
$WPCLI import --authors=skip wp-content/themes/les-verts/demo-content/content.xml

# Front Page
# ==========
$WPCLI option update show_on_front "page"
$WPCLI option update page_on_front "$($WPCLI post list --post_type=page --field=ID --name=Startseite)"
