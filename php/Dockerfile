FROM php:8.1-fpm-alpine3.18
# FROM php:8.0-fpm-alpine3.16
ARG HOME_DIR
ARG KEY_DIR
# RUN apk --no-cache 8.5.0-r0(curl) 1.1.1w-r1(openssl)
RUN set -ex && apk --no-cache add postgresql-dev
RUN docker-php-ext-install pdo_pgsql

RUN mkdir -p "${KEY_DIR}"; chown www-data: "${KEY_DIR}" && chmod 770 "${KEY_DIR}"
WORKDIR ${HOME_DIR}