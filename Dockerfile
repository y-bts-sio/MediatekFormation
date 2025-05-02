
Yass <yass102710@gmail.com>
06:22 (il y a 0 minute)
À moi

FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql

# Activer le module Apache rewrite
RUN a2enmod rewrite

# Installer Composer manuellement
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tous les fichiers du projet Symfony
COPY . .

# Installer les dépendances Symfony
RUN composer install --no-dev --optimize-autoloader

# Nettoyer le cache Symfony
RUN php bin/console cache:clear --env=prod

# Donner les bons droits
RUN chown -R www-data:www-data var

# Exposer le port
EXPOSE 80
