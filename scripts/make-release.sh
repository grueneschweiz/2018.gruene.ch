#!/usr/bin/env bash

# halt on all errors
set -e

# Make sure all is build into "production"
yarn build

# install php dependencies
composer install --no-interaction --working-dir=wordpress/wp-content/themes/les-verts

# generate release file
cd wordpress/wp-content/themes
zip -r release.zip les-verts -x */styleguide/**\*
cd ../../..

# move zip into project root
mv wordpress/wp-content/themes/release.zip release.zip
