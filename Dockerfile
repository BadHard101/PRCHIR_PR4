FROM php:apache

RUN docker-php-ext-install mysqli

COPY ./html /var/www/html