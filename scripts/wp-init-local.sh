#!/bin/sh

set -e

###########################
# INSTALL DEV ENVIRONMENT #
###########################
# Install theme dependencies with composer
docker exec wp_docker_les_verts bash -c "cd /var/www/html/wp-content/themes/les-verts && composer install"

# Install wp core
docker exec wp_docker_les_verts wp core install --url=localhost --title=LesVERTS --admin_user=admin --admin_password=admin --admin_email=admin@gruene.ch

# Run the bootstrap script
docker cp scripts/wp-install-plugins.sh wp_docker_les_verts:/var/www/html/wp-install-plugins.sh
docker cp scripts/wp-configure.sh wp_docker_les_verts:/var/www/html/wp-configure.sh
docker exec wp_docker_les_verts bash -c "chmod +x wp-install-plugins.sh && export WPCLI='wp --color'; ./wp-install-plugins.sh -l"
docker exec wp_docker_les_verts bash -c "chmod +x wp-configure.sh && export WPCLI='wp --color'; ./wp-configure.sh"

# Build dependencies so wo can make the symlink for the static files
yarn install && yarn build

# Create dist symlink
docker exec wp_docker_les_verts bash -c "cd wp-content/themes/les-verts && ln -sf styleguide/dist/static static"

# Import demo content
docker cp scripts/wp-add-demo-content.sh wp_docker_les_verts:/var/www/html/wp-add-demo-content.sh
docker exec wp_docker_les_verts bash -c "WPCLI='wp --color' wp plugin activate wordpress-importer"
docker exec wp_docker_les_verts bash -c "chmod +x wp-add-demo-content.sh && WPCLI='wp --color' ./wp-add-demo-content.sh"
