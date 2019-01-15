#!/bin/bash

# Files:
# # Custom theme
# './wordpress/wp-content/themes/les-verts/**/*',
# '!./wordpress/wp-content/themes/les-verts/vendor/**/*',
# '!./wordpress/wp-content/themes/les-verts/styleguide/**/*',
# './wordpress/wp-content/themes/les-verts/styleguide/dist/static/**/*',
# '!./wordpress/wp-content/themes/les-verts/styleguide/dist/static/styleguide**/*',
# # Specific plugins (not installable by wp-cli)
# './wordpress/wp-content/plugins/advanced-custom-fields-pro/**/*'
# './wordpress/wp-content/plugins/polylang-pro/**/*'
# './wordpress/wp-content/plugins/searchwp/**/*'

# NOTES:
# We assume wp-cli & core wordpress files are already installed. If not, run the script `setup-server.sh` on the server before.

# ================================================================
# ============ EDIT THE VALUES OF THE VARIABLES BELOW ============

SERVER_USER_DIR='/home/'

SSH_USER='user'
SSH_HOST='mydomain.ch'
SSH_PATH="./www/mydomain.ch"

SITE_URL='mydomain.ch'
SITE_PATH=${SSH_PATH}
SITE_THEME_PATH="${SITE_PATH}/wp-content/themes/les-verts"

COMPOSER_COMMAND="${SERVER_USER_DIR}/bin/composer"
WP_COMMAND="${SERVER_USER_DIR}/bin/wp"

# ====== STOP TO EDIT HERE or continue at your own risks 💣 ======
# ================================================================

# Make sure all is build into "production"
yarn build

# Show what will be deployed and ask for confirmation
rsync -auvrzn --exclude='vendor' --exclude='styleguide' ./wordpress/wp-content/themes/les-verts "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/themes/"
rsync -auvrzn --exclude='styleguide' ./wordpress/wp-content/themes/les-verts/styleguide/dist/static "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/themes/les-verts/"
rsync -auvrzn ./wordpress/wp-content/plugins/advanced-custom-fields-pro "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/plugins/"
rsync -auvrzn ./wordpress/wp-content/plugins/polylang-pro "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/plugins/"
rsync -auvrzn ./wordpress/wp-content/plugins/searchwp "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/plugins/"

read -p "The above files will be deployed. Continue? [y/n] " -n 1 -r
echo

if [[ ! $REPLY =~ ^[Yy]$ ]]
then
	[[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1 # handle exits from shell or function but don't exit interactive shell
fi

# Copy everything needed
rsync -auvrz --exclude='vendor' --exclude='styleguide' ./wordpress/wp-content/themes/les-verts "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/themes/"
rsync -auvrz --exclude='styleguide' ./wordpress/wp-content/themes/les-verts/styleguide/dist/static "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/themes/les-verts/"
rsync -auvrz ./wordpress/wp-content/plugins/advanced-custom-fields-pro "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/plugins/"
rsync -auvrz ./wordpress/wp-content/plugins/polylang-pro "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/plugins/"
rsync -auvrz ./wordpress/wp-content/plugins/searchwp "${SSH_USER}@${SSH_HOST}:${SSH_PATH}/wp-content/plugins/"

# Install composer dependencies
ssh "$SSH_USER@$SSH_HOST" "$COMPOSER_COMMAND install --working-dir=$SITE_THEME_PATH"

# Run bootstrap script
ssh $SSH_USER@$SSH_HOST "cd $SITE_PATH && WPCLI='$WP_COMMAND' BASE_URL='$SITE_URL' bash -s " -- < ./scripts/wp-install-plugins.sh

# TODO: Fix file permissions
