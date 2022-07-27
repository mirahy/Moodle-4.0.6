FROM php:7.4-apache

ENV PHP_EXTRA_CONFIGURE_ARGS: "--with-zip --with-pgsql"

RUN sed -ri -e 's!;max_input_vars = 1000!max_input_vars = 5000!g' $PHP_INI_DIR/php.ini* \
    && sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 1024M!g' $PHP_INI_DIR/php.ini* \
    && sed -ri -e 's!post_max_size = 8M!post_max_size = 1024M!g' $PHP_INI_DIR/php.ini*
RUN cp "$PHP_INI_DIR/php.ini-production" $PHP_INI_DIR/php.ini 

RUN apt-get update && apt-get install -y \
        libpng-dev wget libzip-dev zlib1g-dev libicu-dev libpq-dev libxml2-dev libldap2-dev ghostscript \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install gd intl zip pgsql pdo pdo_pgsql opcache xmlrpc soap ldap \
    && wget -O /tmp/moodle.tgz https://download.moodle.org/stable311/moodle-latest-311.tgz \
    && tar -xzf /tmp/moodle.tgz --strip-components=1 -C /var/www/html

COPY . /var/www/html