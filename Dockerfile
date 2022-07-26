FROM php:7.4-apache

ENV PHP_EXTRA_CONFIGURE_ARGS: "--with-zip --with-pgsql"

RUN sed -ri -e 's!;max_input_vars = 1000!max_input_vars = 5000!g' $PHP_INI_DIR/php.ini*
RUN sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 1024M!g' $PHP_INI_DIR/php.ini*
RUN cp "$PHP_INI_DIR/php.ini-production" $PHP_INI_DIR/php.ini 

RUN apt-get update && apt-get install -y \
        libpng-dev libzip-dev zlib1g-dev libicu-dev libpq-dev libxml2-dev libldap2-dev ghostscript \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install gd intl zip pgsql pdo pdo_pgsql opcache xmlrpc soap ldap

COPY . /var/www/html