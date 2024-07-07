FROM php:8.2-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/sns/php

COPY ./php/ /var/www/sns/php

COPY ./php/php.ini /usr/local/etc/php

RUN chown -R www-data:www-data /var/www/sns

RUN apt-get update && apt-get install -y \
	git \
	curl \
	zip \
	unzip \
	libmagickwand-dev \
	imagemagick \
	&& docker-php-ext-install mysqli

RUN mkdir -p /usr/src/php/ext/imagick; \
	curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
	docker-php-ext-install imagick;

COPY wait-for-it.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/wait-for-it.sh
RUN chmod +x /var/www/sns/php/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["./docker-entrypoint.sh"]
