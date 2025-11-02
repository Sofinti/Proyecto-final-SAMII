# Usamos una imagen base de PHP:8.4.14 CLI o FPM (lo que uses).
# Usaremos el CLI (Command Line Interface) si no usas PHP-FPM
FROM php:8.4-cli-alpine

# Copiamos todo el código
COPY . /usr/src/app

# Establecemos el directorio de trabajo
WORKDIR /usr/src/app

# Este es el comando crítico: usa $PORT y sirve la app desde la raíz ('.')
CMD php -S 0.0.0.0:$PORT -t .
