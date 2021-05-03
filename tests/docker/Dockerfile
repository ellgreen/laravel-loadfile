FROM php:7.4-cli

RUN docker-php-ext-install pdo pdo_mysql && \
    pecl install pcov && \
    docker-php-ext-enable pdo_mysql pcov
