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

# Installer les dépendances PHP avec Composer
RUN composer install --optimize-autoloader --no-dev --ignore-platform-reqs -vvv

# Copier le reste du projet et vérifier le contenu
COPY . .
RUN ls -la && \
    pwd && \
    echo "Checking for .env file:" && \
    ls -la .env || echo ".env file not found"

# Copier explicitement le .env
COPY .env .env
RUN ls -la .env

# Donner les permissions appropriées aux répertoires
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 644 .env

# Générer la clé, configurer le cache et les routes
RUN php artisan key:generate && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Exposition du port 8000 pour Laravel
EXPOSE 8000

# Copie du script d'entrée et attribution des permissions
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Définition du script d'entrée
ENTRYPOINT ["docker-entrypoint.sh"]