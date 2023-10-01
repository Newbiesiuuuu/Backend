# Base Image
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

COPY . /var/www/html

## update packages
RUN apk update

## install curl
RUN apk add curl

## install composer
RUN curl -sS https://getcomposer.org/installer | php

## move file to /usr/bin/composer
RUN mv composer.phar /usr/bin/composer

## install packages
RUN composer install

## use 8000 port
EXPOSE 8000

### run php-fpm
CMD ["php-fpm"]



