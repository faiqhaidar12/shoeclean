FROM php:8.2-cli-bookworm AS composer_deps

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    bcmath \
    curl \
    gd \
    intl \
    pdo_mysql \
    xml \
    zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader

FROM node:20-bookworm-slim AS node_assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php:8.2-cli-bookworm

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libcurl4-openssl-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    bcmath \
    curl \
    gd \
    intl \
    pcntl \
    pdo_mysql \
    xml \
    zip \
    && rm -rf /var/lib/apt/lists/*

COPY . .
COPY --from=composer_deps /app/vendor ./vendor
COPY --from=node_assets /app/public/build ./public/build

RUN php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-lc", "php artisan storage:link || true; php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
