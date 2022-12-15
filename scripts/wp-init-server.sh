#!/usr/bin/env bash

set -e

usage() {
	echo "Usage: $0 --db_name <name> --db_username <user> --site_url <url> --site_title <title> --site_admin_username <name> --site_admin_email <email> --site_locale <de_DE|fr_FR> [--db_host <host>] [--db_pass <password>] [--site_admin_pass <password>] [--install_path <path>] [--multisite] [-?|--help]"
	echo "Options:"
	echo "  --install_path PATH         absolute path. defaults to directory of this script"
	echo "  --db_host HOST              hostname of the database. defaults to 'localhost'"
	echo "  --db_name NAME              database name"
	echo "  --db_username USER          name of the database user"
	echo "  --db_pass PASS              database password. the script will prompt for the password if it is not provided as option."
	echo "  --site_url URL              the url, under which the site will be available. network url if multisite option is given."
	echo "  --site_title TITLE          the sites title. network title if multisite option is given."
	echo "  --site_admin_username USER  the username of the wordpress admin. network admin if multisite option is given."
	echo "  --site_admin_pass PASS      the password of the wordpress admin. the script will prompt for the password if it is not provided as option."
	echo "  --site_locale LANG          the language, in which the site will be installed. accepts 'de_DE' and 'fr_FR'"
	echo "  --multisite                 the presence triggers a multisite installation"
	echo "  -?, --help                  this help text"
}

arg_error() {
	echo "Missing argument: $1"
	ERROR=1
}

while [[ $# -gt 0 ]]
	do
	key="$1"

	case $key in
			--install_path)
			INSTALL_PATH="$2"
			shift # past argument
			shift # past value
			;;
			--db_host)
			DB_HOST="$2"
			shift # past argument
			shift # past value
			;;
			--db_name)
			DB_NAME="$2"
			shift # past argument
			shift # past value
			;;
			--db_username)
			DB_USER="$2"
			shift # past argument
			shift # past value
			;;
			--db_pass)
			DB_PASS="$2"
			shift # past argument
			shift # past value
			;;
			--site_url)
			SITE_URL="$2"
			shift # past argument
			shift # past value
			;;
			--site_title)
			SITE_TITLE="$2"
			shift # past argument
			shift # past value
			;;
			--site_admin_username)
			SITE_ADMIN_USER="$2"
			shift # past argument
			shift # past value
			;;
			--site_admin_pass)
			SITE_ADMIN_PASS="$2"
			shift # past argument
			shift # past value
			;;
			--site_admin_email)
			SITE_ADMIN_EMAIL="$2"
			shift # past argument
			shift # past value
			;;
			--site_locale)
			SITE_LOCALE="$2"
			shift # past argument
			shift # past value
			;;
			--multisite)
			MULTISITE=1
			shift # past argument
			;;
			-?|--help)
			usage
			exit 0
			;;
			*)
			echo "Unknown argument $1."
			usage
			exit 1
	esac
done

ERROR=

if [ -z "$DB_HOST" ]; then
	DB_HOST=localhost
fi

if [ -z "$DB_NAME" ]; then
	arg_error "--db_name"
fi

if [ -z "$DB_USER" ]; then
	arg_error "--db_username"
fi

if [ -z "$SITE_URL" ]; then
	arg_error "--site_url"
fi

if [ -z "$SITE_TITLE" ]; then
	arg_error "--site_title"
fi

if [ -z "$SITE_ADMIN_USER" ]; then
	arg_error "--site_admin_username"
fi

if [ -z "$SITE_ADMIN_EMAIL" ]; then
	arg_error "--site_admin_email"
fi

if [ -z "$SITE_LOCALE" ]; then
	arg_error "--site_locale"
fi

if [ 'de_DE' != "$SITE_LOCALE" ] && [ 'fr_FR' != "$SITE_LOCALE" ]; then
	echo "Invalid argument: --site_locale"
	echo "Accepted values: 'de_DE', 'fr_FR'"
	ERROR=1
fi

if [ -z "$INSTALL_PATH" ]; then
	INSTALL_PATH=$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )
fi

if [ ! -d "$INSTALL_PATH" ]; then
	echo "'$INSTALL_PATH' is no existing directory. Aborting."
	ERROR=1
fi

if [ -n "$ERROR" ]; then
	echo "Usage see: $0 --help"
	exit 1
fi

if [ -z "$DB_PASS" ]; then
	read -sp "Enter password for database user '$DB_USER': " DB_PASS
	echo ""
fi

if [ -z "$SITE_ADMIN_PASS" ]; then
	read -sp "Enter password for wordpress admin user '$SITE_ADMIN_USER': " SITE_ADMIN_PASS
	echo ""
fi

# load environment
if [[ -f ~/.bash_profile ]]; then
    source ~/.bash_profile
fi

# install wp cli
# see: https://www.cyon.ch/blog/Mit-WP-CLI-WordPress-auf-der-Kommandozeile-verwalten
################

# if wp command doesn't exist
if ! [ -x "$(command -v wp)" ]; then
  # download an install cli
  echo "Installing wp cli"
	cd ~
	curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
	mkdir -p bin
	mv wp-cli.phar bin/wp
	chmod u+x bin/wp

	# add wp cli autocompletion
	echo "Installing wp cli autocompletion"
	mkdir -p ~/.wp-cli
	cd ~/.wp-cli
	curl -O https://raw.githubusercontent.com/wp-cli/wp-cli/master/utils/wp-completion.bash
	echo "source ~/.wp-cli/wp-completion.bash" >> ~/.bash_profile
	source ~/.bash_profile
else
	echo "Updating wp cli"
	wp cli update --yes
fi

# install super-cache cli package if needed
if ! wp super-cache; then
	wp package install https://github.com/wp-cli/wp-super-cache-cli.git
fi

# install wordpress
###################
echo "Starting installation in: $INSTALL_PATH"

# download wordpress
cd "${INSTALL_PATH}"
wp core download --locale="$SITE_LOCALE"

# configure database
wp core config \
	--dbname="$DB_NAME" \
	--dbuser="$DB_USER" \
	--dbpass="$DB_PASS" \
	--dbhost="$DB_HOST"

# install wordpress
if [[ ${MULTISITE} ]]; then
	INSTALL="multisite-install"
else
	INSTALL="install"
fi

wp core ${INSTALL} \
	--url="$SITE_URL" \
	--title="$SITE_TITLE" \
	--admin_user="$SITE_ADMIN_USER"  \
	--admin_password="$SITE_ADMIN_PASS" \
	--admin_email="$SITE_ADMIN_EMAIL"

# set correct .htaccess
if [[ ${MULTISITE} ]]; then
echo "RewriteEngine On

# force redirect to https
RewriteCond %{HTTPS} off
RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

# activate HSTS with 6 month duration time
Header set Strict-Transport-Security \"max-age=15768000; preload\"

RewriteBase /
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ \$1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) \$2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ \$2 [L]
RewriteRule . index.php [L]" > .htaccess

echo ".htaccess file created and rewrite rules set"
fi

# if composer command doesn't exist
if ! [ -x "$(command -v composer)" ]; then
  # download an install cli
  echo "Installing composer"
	cd ~
	curl -sS https://getcomposer.org/installer -o composer-setup.php
	php composer-setup.php --install-dir=bin --filename=composer
	chmod u+x bin/composer
	rm composer-setup.php
else
	echo "Updating composer"
	composer self-update
fi
