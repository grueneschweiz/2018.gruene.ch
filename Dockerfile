# Installs WordPress with wp-cli (wp.cli.org) installed

FROM wordpress:php8.1

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
	msmtp \
	libgmp-dev \
	vim

# Add PHP extensions
RUN docker-php-ext-install intl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp

# install & enable xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Add WP-CLI
RUN curl -o /bin/wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
COPY wp-su.sh /bin/wp
RUN chmod +x /bin/wp-cli.phar /bin/wp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy wp-config.php into container
# (dont mount it, cause it gets updated by a sed srcipt.
# mounting it therefore crashes the boot process.)	
COPY wordpress/wp-config.php /var/www/html/wp-config.php

# Show php logs in stderr
RUN touch /usr/local/etc/php/conf.d/php_error.ini
RUN echo "log_errors = on" >> /usr/local/etc/php/conf.d/php_error.ini
RUN echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/php_error.ini

# Change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Cleanup
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set working directory
WORKDIR /var/www/html
