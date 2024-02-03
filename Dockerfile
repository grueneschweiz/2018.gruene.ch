# Installs WordPress with wp-cli and composer
FROM wordpress:php8.1

ARG UID=1000
ARG GID=1000

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
RUN curl -o /bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x /bin/wp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cleanup
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Show php logs in stderr
RUN touch /usr/local/etc/php/conf.d/php_error.ini
RUN echo "log_errors = on" >> /usr/local/etc/php/conf.d/php_error.ini
RUN echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/php_error.ini

# Change uid and gid of apache to docker user uid/gid
RUN groupmod -g ${GID} www-data && usermod -u ${UID} -g ${GID} www-data

# Change owner of /var/www to www-data
RUN chown -R www-data:www-data /var/www

# Run container as user www-data
USER www-data

# Copy wp-config.php into container
# (dont mount it, cause it gets updated by a sed srcipt.
# mounting it therefore crashes the boot process.)
COPY wordpress/wp-config.php /var/www/html/wp-config.ph

# Set working directory
WORKDIR /var/www/html
