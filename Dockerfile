# --- Сборка фронтенда (Vite) ---
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- PHP-приложение ---
FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip intl gd bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# backpack/crud 7.x помечен security-advisories, без этого composer откажется его ставить
RUN composer config --global policy.advisories.block false

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-scripts \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && sed -i 's/\r$//' docker/entrypoint.sh && chmod +x docker/entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["docker/entrypoint.sh"]
# Не `artisan serve`: его воркеры не наследуют переменные окружения контейнера и берут DB_* из .env
CMD ["sh", "-c", "cd public && exec php -S 0.0.0.0:8000 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php"]
