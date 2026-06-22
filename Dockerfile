# ── Stage 1: Build aset frontend (Vite) ──────────────────────────────────────
FROM node:22-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY resources/ resources/
COPY vite.config.js tailwind.config.js ./
# File-file yang dibutuhkan oleh laravel-vite-plugin
COPY public/ public/

RUN npm run build

# ── Stage 2: Aplikasi PHP ─────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS app

# Ekstensi PHP yang dibutuhkan Laravel
RUN apk add --no-cache \
        linux-headers \
        $PHPIZE_DEPS \
        postgresql-dev \
        oniguruma-dev \
        libzip-dev \
        curl-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring \
        zip \
        pcntl \
        bcmath \
    && apk del $PHPIZE_DEPS linux-headers

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Pasang dependensi PHP terlebih dahulu agar layer di-cache
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-interaction \
        --optimize-autoloader \
        --no-scripts

# Salin seluruh source code
COPY . .

# Salin hasil build Vite dari stage 1
COPY --from=node-builder /app/public/build public/build

# Atur permission
RUN chown -R www-data:www-data storage bootstrap/cache

# Jalankan post-install scripts Composer (package:discover, dll.)
RUN composer run-script post-autoload-dump --no-interaction

USER www-data

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
