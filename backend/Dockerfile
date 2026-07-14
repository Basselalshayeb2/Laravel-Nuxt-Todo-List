FROM composer:2.9 AS vendor
WORKDIR /app
COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader
COPY backend .
RUN rm -f config/scribe.php \
    && composer dump-autoload --no-dev --classmap-authoritative --no-interaction

FROM php:8.4-fpm-alpine AS runtime
RUN apk add --no-cache fcgi sqlite-libs \
    && apk add --no-cache --virtual .pdo-sqlite-build-deps sqlite-dev \
    && docker-php-ext-install pdo_sqlite \
    && apk del .pdo-sqlite-build-deps
WORKDIR /var/www/html
COPY --from=vendor /app /var/www/html
COPY docker/laravel-entrypoint.sh /usr/local/bin/taskflow-entrypoint
RUN chmod +x /usr/local/bin/taskflow-entrypoint \
    && mkdir -p /var/www/runtime storage/framework/cache storage/framework/sessions storage/framework/views storage/logs database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/database /var/www/runtime
USER www-data
ENTRYPOINT ["/usr/local/bin/taskflow-entrypoint"]
CMD ["php-fpm", "-F"]

FROM runtime AS production
