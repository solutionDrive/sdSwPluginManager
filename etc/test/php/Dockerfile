ARG PHP_VERSION
ARG PHP_SHORT_VERSION

FROM php:$PHP_VERSION-fpm

# Make it usable after FROM
ARG PHP_SHORT_VERSION


RUN set -xe \
    && apt-get update \
    && apt-get upgrade -y \
    && apt-get dist-upgrade -y \
    && apt-get install -y \
        bash \
        libicu-dev \
        libxml2-dev \
        mysql-client \
        gzip \
        libzip-dev \
        libfreetype6 \
        libfreetype6-dev \
        libjpeg-dev \
        libjpeg62-turbo-dev \
        autoconf \
        cmake \
        file \
        g++ \
        gcc \
        libc-dev \
        make \
        git \
        pkgconf \
        zlib1g-dev \
        re2c \
        wget \
        unzip \
        ed \
    && docker-php-ext-configure bcmath --enable-bcmath \
    && docker-php-ext-configure calendar --enable-calendar \
    && docker-php-ext-configure gd --enable-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql \
    && docker-php-ext-configure mbstring --enable-mbstring \
    && docker-php-ext-configure soap --enable-soap \
    && docker-php-ext-configure xml --enable-xml \
    && docker-php-ext-configure zip --enable-zip --with-libzip \
    && docker-php-ext-install \
        bcmath \
        calendar \
        gd \
        intl \
        pcntl \
        pdo_mysql \
        mbstring \
        soap \
        xml \
        zip \
    && docker-php-ext-enable opcache
