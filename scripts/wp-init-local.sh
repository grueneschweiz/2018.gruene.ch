#!/bin/sh

set -e

###########################
# INSTALL DEV ENVIRONMENT #
###########################
# Install theme dependencies with composer
docker exec --user $(id -u):$(id -g) wp_docker_les_verts bash -c "cd /var/www/html/wp-content/themes/les-verts && composer install"

# Install wp core
docker exec wp_docker_les_verts wp core install --url=localhost --title=LesVERTS --admin_user=admin --admin_password=admin --admin_email=admin@gruene.ch

# Run the bootstrap script
docker cp scripts/wp-install-plugins.sh wp_docker_les_verts:/var/www/html/wp-install-plugins.sh
docker cp scripts/wp-configure.sh wp_docker_les_verts:/var/www/html/wp-configure.sh
docker exec wp_docker_les_verts bash -c "chmod +x wp-install-plugins.sh && export WPCLI='wp --color'; export WP_CLI_CACHE_DIR=/home/www-data; ./wp-install-plugins.sh -l"
docker exec wp_docker_les_verts bash -c "chmod +x wp-configure.sh && export WPCLI='wp --color'; export WP_CLI_CACHE_DIR=/home/www-data; ./wp-configure.sh"

# Build dependencies so wo can make the symlink for the static files
yarn install && yarn build

# Create dist symlink
docker exec --user $(id -u):$(id -g) wp_docker_les_verts bash -c "cd wp-content/themes/les-verts && ln -sf styleguide/dist/static static"

# Import demo content
docker cp scripts/wp-add-demo-content.sh wp_docker_les_verts:/var/www/html/wp-add-demo-content.sh
docker exec wp_docker_les_verts bash -c "WPCLI='wp --color' wp plugin activate wordpress-importer"
docker exec wp_docker_les_verts bash -c "chmod +x wp-add-demo-content.sh && WP_CLI_CACHE_DIR=/home/www-data WPCLI='wp --color' ./wp-add-demo-content.sh"

######################
# ENABLE IDE SUPPORT #
######################
#rm -rf .wordpress
#mkdir .wordpress
#
## download and unzip wordpress
#curl -o .wordpress/latest.tar.gz https://wordpress.org/latest.tar.gz
#tar xvzf .wordpress/latest.tar.gz -C .wordpress/
#rm .wordpress/latest.tar.gz
#mv .wordpress/wordpress/* .wordpress
#rmdir .wordpress/wordpress
