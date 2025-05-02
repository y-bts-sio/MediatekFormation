
FROM php:8.2-apache

# Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql

# Activer le module Apache rewrite pour Symfony
RUN a2enmod rewrite

# Installer Composer manuellement
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier les fichiers du projet dans le conteneur
COPY . .

# Installer les dépendances Symfony (sans les dev, version optimisée)
RUN composer install --no-dev --optimize-autoloader

# Vider le cache Symfony en environnement prod (optionnel mais conseillé)
RUN php bin/console cache:clear --env=prod || true

# Donner les bons droits à Symfony
RUN chown -R www-data:www-data var

# Exposer le port HTTP
EXPOSE 80
