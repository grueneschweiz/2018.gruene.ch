#!/usr/bin/env bash

# halt on all errors
set -e

# Make sure all is build into "production"
yarn build

# install php dependencies
composer install --no-interaction --working-dir=wordpress/wp-content/themes/les-verts

# generate release file
cd wordpress/wp-content/themes
mkdir les-verts/static
cp -r les-verts/styleguide/dist/static/js les-verts/static/js
cp -r les-verts/styleguide/dist/static/fonts les-verts/static/fonts
cp les-verts/styleguide/dist/static/icons.svg les-verts/static/icons.svg
cp les-verts/styleguide/dist/static/style.css les-verts/static/style.css
cp les-verts/styleguide/dist/static/style.min.css les-verts/static/style.min.css
zip -r release.zip les-verts -x */styleguide/**\*
cd ../../..

# move zip into project root
mv wordpress/wp-content/themes/release.zip les-verts.zip
