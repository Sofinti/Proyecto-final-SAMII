# 1. Usamos la imagen FPM
FROM php:8.4-fpm-alpine

# 2. Instalamos las extensiones de PHP para MySQL
# (Esta es la línea que soluciona el error)
RUN docker-php-ext-install pdo_mysql

# 3. Establecemos el directorio de trabajo
WORKDIR /var/www/html
