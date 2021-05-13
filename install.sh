#!/bin/bash

rm -r /var/www/html
cd /var/www/fullplate

apt update && apt install -y \
    apache2 \
    mysql-server \
    php \
    php-cli \
    php-fpm \
    php-json \
    php-common \
    php-mysql \
    php-zip \
    php-gd \
    php-mbstring \
    php-curl \
    php-xml \
    php-pear \
    php-bcmath \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

apt clean && rm -rf /var/lib/apt/lists/*
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
chown -R www-data:www-data /var/www/fullplate

composer install
echo "=====MIGRATING====="
php artisan migrate --seed && php artisan passport:install

echo "=====CONFIGURATION====="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "======GENERATING DOC======"
php artisan l5-swagger:generate

echo "======OPTIMIZE======"
php artisan optimize


