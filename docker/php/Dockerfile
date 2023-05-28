# Coddict Docker Boilerplate Version 2.1

FROM php:8.0-fpm-alpine

ARG TIMEZONE

COPY docker/php/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

RUN apk add --no-cache \
    wget \
    gnupg \
    g++ \
    gcc \
    python3 \
    procps \
    openssl \
    git \
    unzip \
    zlib-dev \
    libzip-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    icu-dev \
    oniguruma-dev \
    libxslt-dev \
    acl \
    jpegoptim

RUN docker-php-ext-configure gd
RUN apk add --no-cache gcc g++ autoconf libtool make imagemagick-dev
RUN pecl install imagick \
	&& docker-php-ext-enable imagick
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    xsl \
    gd \
    intl \
    opcache \
    exif \
    mbstring

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer self-update --${COMPOSER_VERSION}
RUN apk add bash
RUN curl https://get.symfony.com/cli/installer --output installer \
    && chmod +x installer \
    && ./installer
RUN mkdir -p /root/.composer
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# install node js
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.9/main/ nodejs \
 nodejs-npm \
 && npm install --g \
    yarn

WORKDIR /var/www/codebase
