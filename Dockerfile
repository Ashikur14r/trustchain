FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM serversideup/php:8.3-fpm-nginx AS vendor
USER root
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts
COPY . .
RUN composer dump-autoload --no-dev --optimize

FROM serversideup/php:8.3-fpm-nginx
WORKDIR /var/www/html
COPY --from=vendor --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=frontend --chown=www-data:www-data /app/public/build /var/www/html/public/build

ENV PORT=8080 \
    NGINX_HTTP_PORT=8080 \
    NGINX_WEBROOT=/var/www/html/public \
    PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_MIGRATION=true

