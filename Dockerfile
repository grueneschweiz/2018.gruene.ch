#
# Installs WordPress with wp-cli (wp.cli.org) installed
# Docker Hub: https://registry.hub.docker.com/u/conetix/wordpress-with-wp-cli/
# Github Repo: https://github.com/conetix/docker-wordpress-wp-cli

FROM wordpress:4.9-php7.1-apache

# Add sudo in order to run wp-cli as the www-data user
RUN apt-get update && apt-get install -y \
	sudo \
	less \
	git \
	zip \
	unzip \
	zlib1g-dev \
	libicu-dev \
	g++ \
	ssmtp \
	nano

# Add PHP extensions
RUN docker-php-ext-install intl

# Add WP-CLI
RUN curl -o /bin/wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
COPY wp-su.sh /bin/wp
RUN chmod +x /bin/wp-cli.phar /bin/wp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Show php logs in stderr
RUN touch /usr/local/etc/php/conf.d/php_error.ini
RUN echo "log_errors = on" >> /usr/local/etc/php/conf.d/php_error.ini
RUN echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/php_error.ini

# Change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Copy some config files
COPY wordpress/.htaccess /var/www/html/.htaccess
COPY wordpress/wp-config.php /var/www/html/wp-config.php

# Create directory for wp-cli packages
RUN mkdir -p /var/www/.wp-cli/packages
RUN chmod -R 777 /var/www/.wp-cli

# Cleanup
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Xdebug
RUN pecl install xdebug
RUN apachectl restart
