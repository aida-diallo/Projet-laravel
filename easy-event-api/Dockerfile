# Dockerfile
FROM php:8.2-fpm

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Installation des extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . .

# Installation des dépendances
RUN composer install --optimize-autoloader --no-dev

# Optimisation pour la production
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Permissions pour le stockage
RUN chown -R www-data:www-data storage bootstrap/cache

# Exposition du port
EXPOSE 8080

# Script de démarrage
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]