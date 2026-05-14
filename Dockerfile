FROM php:8.3-fpm-alpine

# Extensions PHP nécessaires
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        curl \
        zip \
        gd \
        opcache

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# Copier les fichiers
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Installer les dépendances JS et compiler les assets
RUN npm ci && npm run build

# Permissions storage et cache
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Config Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Optimiser Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 8080

# Script de démarrage
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]