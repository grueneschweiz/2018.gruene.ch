#!/usr/bin/env bash

# Navigation
# ==========
$WPCLI menu create "Main navigation"
$WPCLI menu location assign main-navigation main-nav

$WPCLI menu create "Footer navigation"
$WPCLI menu location assign footer-navigation footer-meta-nav

$WPCLI menu create "Language navigation"
$WPCLI menu location assign language-navigation language-nav

# Front Page
# =========================

# set static front page
$WPCLI option update show_on_front <<< "page"
$WPCLI option page_on_front show_on_front <<< "1" # todo: set correct id
