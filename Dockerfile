# HvacQuoteCraft
# Author: Imran Ahmed <me@imrandev.bd>
# Website: https://imrandev.bd | Contact: https://imrandev.bd/contact

FROM php:8.4-cli-bookworm AS vendor
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpng-dev libicu-dev libonig-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql zip intl mbstring gd bcmath \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --prefer-dist --no-interaction --no-scripts --optimize-autoloader

FROM php:8.4-apache-bookworm
LABEL org.opencontainers.image.title="HvacQuoteCraft" \
      org.opencontainers.image.description="AI Estimate & Proposal Assistant for HVAC Contractors." \
      org.opencontainers.image.authors="Imran Ahmed <me@imrandev.bd>" \
      org.opencontainers.image.url="https://imrandev.bd/contact" \
      org.opencontainers.image.source="https://github.com/imranbru99/hvac-quote-craft-ai-estimate-proposal-assistant-hvac-contractors-laravel-filament" \
      org.opencontainers.image.vendor="Imran Ahmed" \
      org.opencontainers.image.licenses="MIT"

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpng-dev libicu-dev libonig-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql zip intl mbstring gd bcmath opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && chmod +x /var/www/html/docker/entrypoint.sh

COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint \
    && sed -i 's/\r$//' /usr/local/bin/app-entrypoint /var/www/html/docker/entrypoint.sh || true

EXPOSE 80
ENTRYPOINT ["app-entrypoint"]
CMD ["apache2-foreground"]
