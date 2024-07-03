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
    && docker-php-ext-install mysqli

COPY wait-for-it.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/wait-for-it.sh
RUN chmod +x /var/www/sns/php/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["./docker-entrypoint.sh"]
