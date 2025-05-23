FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        git \
        curl \
        unzip \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        libpng-dev \
        libxml2-dev \
        zlib-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install \
        pdo_mysql \
        intl \
        zip \
        opcache \
    && docker-php-source delete

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh \
    && apk add --no-cache symfony-cli

WORKDIR /var/www

RUN rm -rf /var/cache/apk/* /tmp/* /var/tmp/*
