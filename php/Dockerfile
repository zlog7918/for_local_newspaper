FROM php:8.2-rc-fpm-alpine3.18
# RUN /bin/sh -c set -eux; [ ! -d /var/www/html ]; mkdir -p /var/www/html; chown www-data:www-data /var/www/html; chmod 1777 /var/www/html
ARG HOME_DIR
RUN set -ex && apk --no-cache add postgresql-dev
RUN docker-php-ext-install pdo_pgsql
WORKDIR ${HOME_DIR}