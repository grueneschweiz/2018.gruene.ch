#!/bin/sh

###########################
# INSTALL DEV ENVIRONMENT #
###########################
# Install theme dependencies with composer
docker exec wp_docker_les_verts bash -c "cd /var/www/html/wp-content/themes/les-verts && composer install"
# Install wp core
docker exec wp_docker_les_verts wp core multisite-install --url=localhost --title=LesVerts --admin_user=admin --admin_password=admin --admin_email=tech+lesverts@superhuit.ch
# Run the bootstrap script
docker cp scripts/wp-install-plugins.sh wp_docker_les_verts:/var/www/html/wp-install-plugins.sh
docker exec wp_docker_les_verts bash -c "cd /var/www/html && chmod +x wp-install-plugins.sh && WPCLI='wp --color' BASE_URL='localhost' ./wp-install-plugins.sh"
# Sync all ACF Fields from JSON files
docker exec wp_docker_les_verts wp package install git@github.com:superhuit-ch/wp-cli-acf-json.git
docker exec wp_docker_les_verts wp acf-json sync --all_sites

######################
# ENABLE IDE SUPPORT #
######################
mkdir .wordpress

# download and unzip wordpress
curl -o .wordpress/latest.tar.gz https://wordpress.org/latest.tar.gz
tar xvzf .wordpress/latest.tar.gz -C .wordpress/
rm .wordpress/latest.tar.gz
mv .wordpress/wordpress/* .wordpress
rmdir .wordpress/wordpress

# copy the wp-content
cp -r wordpress/wp-content .wordpress/wp-content