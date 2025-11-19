# Imagen base
from php:8.2-apache

# Activar el módulo de rewrite de Apache
RUN a2enmod rewrite

# Instalar extensiones de PHP para MySQL
RUN docker-php-ext-install pdo pdo_mysql