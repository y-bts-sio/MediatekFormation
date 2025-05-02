FROM composer:2.6 as vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql

# Activer rewrite
RUN a2enmod rewrite

# Copier les fichiers de ton projet Symfony
WORKDIR /var/www/html
COPY . .

# Copier les dépendances installées depuis l’étape précédente
COPY --from=vendor /app/vendor /var/www/html/vendor

# Nettoyer le cache Symfony (sans bloquer le build si erreur)
RUN php bin/console cache:clear --env=prod || true

# Droits d’écriture
RUN chown -R www-data:www-data var

EXPOSE 80
