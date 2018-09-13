#!/bin/bash

# ================================================================
# ============ EDIT THE VALUES OF THE VARIABLES BELOW ============

SITE_TITLE="Les Verts"
SITE_PATH="./"
SITE_URL="https://testing.2018.gruene.ch/"

ADMIN_USERNAME="cyrill"
ADMIN_EMAIL="cyrill.bolliger@gruene.ch"

WP_VERSION="4.9"
WP_LOCALE="de_DE"

DB_HOST="localhost"
DB_NAME="gruenewi_testing"
DB_USER="gruenewi_testing"

WP_COMMAND='/home/gruenewi/bin/wp'

# ====== STOP TO EDIT HERE or continue at your own risks 💣 ======
# ================================================================

$WP_COMMAND --path=${SITE_PATH} core download --version=${WP_VERSION} --locale=${WP_LOCALE}
vim DB-USER-PASSWORD.txt
$WP_COMMAND --path=${SITE_PATH} config create --dbhost=${DB_HOST} --dbname=${DB_NAME} --dbuser=${DB_USER} --prompt=dbpass < DB-USER-PASSWORD.txt
rm DB-USER-PASSWORD.txt
vim WP-ADMIN_PASSWORD.txt
$WP_COMMAND --path=${SITE_PATH} core multisite-install --url=${SITE_URL} --title="${SITE_TITLE}" --admin_user=${ADMIN_USERNAME} --admin_email=${ADMIN_EMAIL} --prompt=admin_password < WP-ADMIN_PASSWORD.txt
rm WP-ADMIN_PASSWORD.txt
