FROM php:8.2-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/sns/backend

COPY ./backend/ /var/www/sns/backend

COPY ./backend/php.ini /usr/local/etc/php

RUN chown -R www-data:www-data /var/www/sns

RUN apt-get update && apt-get install -y \
	git \
	curl \
	zip \
	unzip \
    && docker-php-ext-install mysqli

# RUN php app/console.php migrate --init

EXPOSE 9000
