# --- build do front (login/configurações em React) ---
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- app ---
FROM serversideup/php:8.3-fpm-nginx
USER root
RUN install-php-extensions gd intl
USER www-data
WORKDIR /var/www/html
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY --chown=www-data:www-data . .
COPY --from=assets --chown=www-data:www-data /app/public/build public/build
RUN composer install --no-dev --optimize-autoloader \
 && rm -rf node_modules
ENV AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_STORAGE_LINK=true \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_MIGRATION_ISOLATION=false \
    PHP_OPCACHE_ENABLE=1 \
    PHP_UPLOAD_MAX_FILESIZE=32M \
    PHP_POST_MAX_SIZE=100M
