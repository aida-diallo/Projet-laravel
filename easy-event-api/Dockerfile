# Utilisation de l'image PHP avec FPM
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

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers `composer.json` et `composer.lock` avant l'installation
COPY composer.json composer.lock ./

# Donner les permissions appropriées aux répertoires de cache Laravel
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache
RUN cp .env.example .env || true


# Optimisation pour la production
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Exposition du port 9000 pour PHP-FPM
EXPOSE 9000

# Copie du script d'entrée et attribution des permissions
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Définition du script d'entrée
ENTRYPOINT ["docker-entrypoint.sh"]
