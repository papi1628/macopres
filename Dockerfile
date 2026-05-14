FROM php:8.3-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Node install (clean)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN node -v && npm -v

# Front build
RUN npm install
RUN npm run build

# Check build
RUN ls -la public/build
RUN test -f public/build/manifest.json

EXPOSE 10000

CMD php artisan migrate --force && \
    php artisan storage:link || true && \
    php artisan serve --host=0.0.0.0 --port=10000