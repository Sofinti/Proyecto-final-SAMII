# Dockerfile

# Usa la misma imagen base que tenías en docker-compose
FROM php:8.2-fpm

# Instala las dependencias necesarias y la extensión pdo_mysql
RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        libonig-dev \
        libicu-dev \
        libpq-dev \
        # Extensión para MySQL/MariaDB
        default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Instala y habilita la extensión PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql
