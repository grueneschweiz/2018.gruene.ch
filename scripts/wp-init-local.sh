#!/bin/sh

set -e

###########################
# INSTALL DEV ENVIRONMENT #
###########################
# Install theme dependencies with composer
docker exec wp_docker_les_verts bash -c "cd /var/www/html/wp-content/themes/les-verts && composer install"

# Install wp core
docker exec wp_docker_les_verts wp core multisite-install --url=localhost --title=LesVERTS --admin_user=admin --admin_password=admin --admin_email=admin@gruene.ch

# Run the bootstrap script
docker cp scripts/wp-install-plugins.sh wp_docker_les_verts:/var/www/html/wp-install-plugins.sh
docker cp scripts/wp-configure.sh wp_docker_les_verts:/var/www/html/wp-configure.sh
#docker exec wp_docker_les_verts bash -c "chmod +x wp-install-plugins.sh && WPCLI='wp --color' ./wp-install-plugins.sh -nl"
docker exec wp_docker_les_verts bash -c "chmod +x wp-configure.sh && WPCLI='wp --color' ./wp-configure.sh -n"

# Create dist symlink
docker exec wp_docker_les_verts bash -c "cd wp-content/themes/les-verts && ln -sf styleguide/dist/static static"

######################
# ENABLE IDE SUPPORT #
######################
rm -rf .wordpress
mkdir .wordpress

# download and unzip wordpress
curl -o .wordpress/latest.tar.gz https://wordpress.org/latest.tar.gz
tar xvzf .wordpress/latest.tar.gz -C .wordpress/
rm .wordpress/latest.tar.gz
mv .wordpress/wordpress/* .wordpress
rmdir .wordpress/wordpress
