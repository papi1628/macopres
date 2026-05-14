FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    nodejs \
    npm

# Install PostgreSQL extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies + build assets
RUN npm install
RUN npm run build
RUN ls -la public/build
RUN php artisan storage:link
RUN php artisan optimize:clear

# Laravel optimization
RUN php artisan config:clear

EXPOSE 10000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000