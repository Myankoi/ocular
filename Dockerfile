# syntax=docker/dockerfile:1.7

FROM composer:2 AS composer

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.4-fpm-alpine AS php-base

RUN apk add --no-cache \
        fcgi \
        icu-libs \
        libjpeg-turbo \
        libpng \
        libwebp \
        libzip \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        oniguruma-dev \
        sqlite-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        pdo_sqlite \
        zip \
    && apk del .build-deps

WORKDIR /var/www/html

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-ocular.ini
COPY docker/php/entrypoint.sh /usr/local/bin/ocular-entrypoint
RUN chmod +x /usr/local/bin/ocular-entrypoint

ENTRYPOINT ["ocular-entrypoint"]
CMD ["php-fpm"]

FROM php-base AS development

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apk add --no-cache git unzip \
    && if [ "$GROUP_ID" != "82" ]; then addgroup -g "$GROUP_ID" -S ocular; else addgroup -S ocular; fi \
    && adduser -u "$USER_ID" -S -D -G ocular ocular

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

FROM php-base AS production

ENV APP_ENV=production \
    APP_DEBUG=false

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --no-scripts \
        --no-progress

COPY --chown=www-data:www-data app ./app
COPY --chown=www-data:www-data bootstrap ./bootstrap
COPY --chown=www-data:www-data config ./config
COPY --chown=www-data:www-data database ./database
COPY --chown=www-data:www-data public ./public
COPY --chown=www-data:www-data resources ./resources
COPY --chown=www-data:www-data routes ./routes
COPY --chown=www-data:www-data storage ./storage
COPY --chown=www-data:www-data artisan ./artisan
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1

FROM nginx:1.27-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=production /var/www/html/public /var/www/html/public

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1/up || exit 1
