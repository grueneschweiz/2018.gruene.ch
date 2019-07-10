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

# publish a copy in the styleguide so we have it available on the github pages
# yes, this is hacky, do you have a better idea to have a permanent url that
# holds always the latest release?
mkdir wordpress/wp-content/themes/les-verts/styleguide/dist/build/theme
cp les-verts.zip wordpress/wp-content/themes/les-verts/styleguide/dist/build/theme/les-verts.zip

# get the version number
THEME_VERSION=`sed '/^\s*\*\s*Version:/! d; s/^\s*\*\s*Version:\s*//; s/\s*$//' wordpress/wp-content/themes/les-verts/style.css`
# and write it into the update.json file we need to check for new versions
sed "s/%VERSION%/$THEME_VERSION/" update.json > wordpress/wp-content/themes/les-verts/styleguide/dist/build/theme/update.json
