#!/bin/bash

# Files:
# # Custom theme
# './wordpress/wp-content/themes/les-verts/**/*',
# '!./wordpress/wp-content/themes/les-verts/vendor/**/*',
# # Specific plugins (not installable by wp-cli)
# './wordpress/wp-content/plugins/advanced-custom-fields-pro/**/*'

# NOTES:
# We assume wp-cli & core wordpress files are already installed. If not, run the script `setup-server.sh` on the server before.

# ================================================================
# ============ EDIT THE VALUES OF THE VARIABLES BELOW ============

SSH_USER='SSH_USERNAME'
SSH_HOST='SSH_HOST'
SSH_PATH='~/SITE_PATH'

SITE_URL='locahost'
SITE_PATH="./web"
SITE_THEME_PATH='./web/wp-content/themes/les-verts'

# ====== STOP TO EDIT HERE or continue at your own risks 💣 ======
# ================================================================

# Make sure all is build into "production"
yarn build

# Show what will be deployed and ask for confirmation
rsync -auvrzn --exclude='vendor' --exclude='styleguide' ./wordpress/wp-content/themes/les-verts "$SSH_USER@$SSH_HOST:$SSH_PATH/wp-content/themes/"
rsync -auvrzn ./wordpress/wp-content/plugins/advanced-custom-fields-pro "$SSH_USER@$SSH_HOST:$SSH_PATH/wp-content/plugins/"
rsync -auvrzn ./wordpress/wp-content/plugins/polylang-pro "$SSH_USER@$SSH_HOST:$SSH_PATH/wp-content/plugins/"

read -p "The above files will be deployed. Continue? [y/n] " -n 1 -r
echo

if [[ ! $REPLY =~ ^[Yy]$ ]]
then
	[[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1 # handle exits from shell or function but don't exit interactive shell
fi

# Copy everything needed
rsync -auvrz --exclude='vendor' --exclude='styleguide' ./wordpress/wp-content/themes/les-verts "$SSH_USER@$SSH_HOST:$SSH_PATH/wp-content/themes/"
rsync -auvrz ./wordpress/wp-content/plugins/advanced-custom-fields-pro "$SSH_USER@$SSH_HOST:$SSH_PATH/wp-content/plugins/"
rsync -auvrz ./wordpress/wp-content/plugins/polylang-pro "$SSH_USER@$SSH_HOST:$SSH_PATH/wp-content/plugins/"

# Install composer dependencies
ssh "$SSH_USER@$SSH_HOST" "/opt/php7.1/bin/php -d allow_url_fopen=On composer.phar install --working-dir=$SITE_THEME_PATH"


# Run bootstrap script
ssh $SSH_USER@$SSH_HOST "cd ~ && WPCLI='/opt/php7.1/bin/php wp-cli.phar --path=$SITE_PATH' BASE_URL='$SITE_URL' bash -s " < ./scripts/wp-install-plugins.sh

# TODO: Fix file permissions
