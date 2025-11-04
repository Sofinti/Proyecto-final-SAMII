# Usamos Alpine, la imagen más ligera de Linux
FROM alpine:latest

# 1. Instalamos las herramientas:
RUN apk add --no-cache dcron mysql-client

# 2. Copiamos nuestro script de backup dentro del contenedor
COPY backup.sh /usr/local/bin/backup.sh

# 3. Le damos permisos de ejecución
RUN chmod +x /usr/local/bin/backup.sh

# 4. Creamos el archivo de log vacío (¡ESTA ES LA LÍNEA NUEVA!)
RUN touch /var/log/cron.log

# 5. Creamos la tarea programada (cronjob)
RUN echo "0 3 * * * /usr/local/bin/backup.sh >> /var/log/cron.log 2>&1" > /etc/crontabs/root

# 6. Comando para iniciar el servicio 'cron'
CMD crond -l 8 && tail -f /var/log/cron.log
