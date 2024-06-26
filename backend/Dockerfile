FROM php:8.2-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/sns/

COPY ./backend/ /var/www/sns/

RUN chown -R www-data:www-data /var/www/

EXPOSE 9000

CMD ["php-fpm"]
