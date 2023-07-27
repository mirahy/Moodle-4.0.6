FROM php:7.4-apache

ENV PHP_EXTRA_CONFIGURE_ARGS: "--with-zip --with-pgsql"

RUN sed -ri -e 's!;max_input_vars = 1000!max_input_vars = 5000!g' $PHP_INI_DIR/php.ini* \
    && sed -ri -e 's!upload_max_filesize = 2M!upload_max_filesize = 1024M!g' $PHP_INI_DIR/php.ini* \
    && sed -ri -e 's!max_execution_time = 30!max_execution_time = 180!g' $PHP_INI_DIR/php.ini* \
    && sed -ri -e 's!post_max_size = 8M!post_max_size = 1024M!g' $PHP_INI_DIR/php.ini*
RUN cp "$PHP_INI_DIR/php.ini-production" $PHP_INI_DIR/php.ini 

RUN apt-get update && apt-get install -y \
        libpng-dev wget libzip-dev zlib1g-dev libicu-dev libpq-dev libxml2-dev libldap2-dev ghostscript nano git \
    && docker-php-ext-configure gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure zip \
    && docker-php-ext-install gd intl zip pgsql pdo pdo_pgsql opcache xmlrpc soap ldap exif \
    && echo "\n" | pecl install redis \ 
    && echo "extension=redis.so" > $PHP_INI_DIR/conf.d/docker-php-ext-redis.ini \
    && git clone https://gitlab+deploy-token-8:su9ZYPs8bgQG7x8rQrVi@git.ufgd.edu.br/ead/repositorio/moodle-4.0.6_2022041906.03.git /var/www/html
    
RUN mkdir /var/moodledata-local \
    && mkdir /var/moodledata-local/temp \
    && mkdir /var/moodledata-local/cache \
    && mkdir /var/moodledata-local/localcache \
    && chown www-data:www-data /var/moodledata-local -R

COPY . /var/www/html
