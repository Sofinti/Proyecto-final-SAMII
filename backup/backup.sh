#!/bin/sh

# Esta línea es CRÍTICA.
# Hará que el script falle si mysqldump falla.
set -o pipefail

FILENAME=$(date +"%Y-%m-%d_%H-%M-%S").sql.gz

echo "Iniciando respaldo de la base de datos ${MYSQL_DATABASE}..."

# Añadimos --no-tablespaces para evitar el error de privilegios
mysqldump --skip-ssl --no-tablespaces -h ${DB_HOST} -u ${MYSQL_USER} -p${MYSQL_PASSWORD} ${MYSQL_DATABASE} | gzip > /backups/${FILENAME}

# Ahora, gracias a 'pipefail', $? SÍ reportará el error
if [ $? -eq 0 ]; then
  echo "Respaldo ${FILENAME} creado con éxito."
else
  echo "ERROR: El respaldo de ${MYSQL_DATABASE} falló."
  rm /backups/${FILENAME} # Borramos el archivo vacío
fi

echo "Borrando respaldos de más de 7 días..."
find /backups/ -name "*.sql.gz" -mtime +7 -exec rm {} \;

echo "Proceso de respaldo finalizado."
