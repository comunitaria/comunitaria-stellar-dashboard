#!/bin/bash

# Script para renovar certificados SSL
# Ejecutar este script en un cron job del host
# Recomendado: agregar a crontab para ejecutar diariamente
#
# Ejemplo de crontab (ejecutar diariamente a las 3 AM):
# 0 3 * * * /path/to/comunitaria-stellar-dashboard/renew-certificates.sh >> /var/log/certbot-renewal.log 2>&1

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

echo "[$(date)] Checking for certificate renewal..."

# Ejecutar renovación de certificados
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml run --rm --no-deps certbot renew --webroot --webroot-path=/var/www/certbot --force-renewal

# Si la renovación fue exitosa, recargar nginx
if [ $? -eq 0 ]; then
    echo "[$(date)] Certificate renewal successful. Reloading nginx..."
    docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec -T nginx nginx -s reload
    echo "[$(date)] Nginx reloaded successfully"
else
    echo "[$(date)] Certificate renewal failed"
    exit 1
fi

echo "[$(date)] Certificate renewal process completed"
