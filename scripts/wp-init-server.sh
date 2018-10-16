#!/usr/bin/env bash

# do the interactive things fist, so you can get a coffee while it installs
echo "Enter installation path"
while :
do
	read -ep "Path: " INSTALL_PATH
	if [ -d "$INSTALL_PATH" ]; then
		# directory exists
		echo "Directory exists."
		break
	fi

	echo "Directory '$INSTALL_PATH' not found."
	continue
done

echo "Configure database"
read -p "Enter DB host: " DB_HOST
read -p "Enter DB name: " DB_NAME
read -p "Enter DB user: " DB_USER
read -sp "Enter DB password: " DB_PASS
printf "\n"

echo "Configure website"
read -p "Enter website url: " SITE_URL
read -p "Enter website title: " SITE_TITLE
read -p "Enter website admin user: " SITE_ADMIN_USER
read -sp "Enter website admin password: " SITE_ADMIN_PASS
printf "\n"
read -p "Enter website admin email: " SITE_ADMIN_EMAIL
read -p "Enter language code (Ex. 'de_DE' or 'fr_FR'): " SITE_LOCALE

# load environment
source ~/.bash_profile

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

# install wordpress
###################

# download wordpress
cd ${INSTALL_PATH}
wp core download --locale="$SITE_LOCALE"

# configure database
wp core config \
	--dbname="$DB_NAME" \
	--dbuser="$DB_USER" \
	--dbpass="$DB_PASS" \
	--dbhost="$DB_HOST"

# install wordpress
wp core multisite-install \
	--url="$SITE_URL" \
	--title="$SITE_TITLE" \
	--admin_user="$SITE_ADMIN_USER"  \
	--admin_password="$SITE_ADMIN_PASS" \
	--admin_email="$SITE_ADMIN_EMAIL"

# set correct .htaccess
echo "RewriteEngine On

# force redirect to https
RewriteCond %{HTTPS} off
RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

# activate HSTS with 6 month duration time
Header set Strict-Transport-Security \"max-age=15768000\", includeSubDomains; preload

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
