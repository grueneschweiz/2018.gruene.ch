#!/bin/bash

# ================================================================
# ============ EDIT THE VALUES OF THE VARIABLES BELOW ============

SITE_TITLE="LesVerts" # Avoid space in the name cause it make wp-cli to crash the install # TODO understand why 😢
SITE_PATH="./web"
SITE_URL="http://paiiscbk.preview.infomaniak.website/"

ADMIN_USERNAME="superhuit"
ADMIN_EMAIL="tech+lesverts@superhuit.ch"

WP_VERSION="4.9.7"
WP_LOCALE="fr_CH"

DB_HOST="localhost"
DB_NAME="wordpress"
DB_USER="root"

# ====== STOP TO EDIT HERE or continue at your own risks 💣 ======
# ================================================================

curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
php wp-cli.phar --path=${SITE_PATH} core download --version=${WP_VERSION} --locale=${WP_LOCALE}
vim DB-USER-PASSWORD.txt
php wp-cli.phar --path=${SITE_PATH} config create --dbhost=${DB_HOST} --dbname=${DB_NAME} --dbuser=${DB_USER} --prompt=dbpass < DB-USER-PASSWORD.txt
rm DB-USER-PASSWORD.txt
vim WP-ADMIN_PASSWORD.txt
php wp-cli.phar --path=${SITE_PATH} core multisite-install --url=${SITE_URL} --title=${SITE_TITLE} --admin_user=${ADMIN_USERNAME} --admin_email=${ADMIN_EMAIL} --prompt=admin_password < WP-ADMIN_PASSWORD.txt
rm WP-ADMIN_PASSWORD.txt
