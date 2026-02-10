#!/bin/sh

# Script para renovar certificados SSL de Let's Encrypt
# Se ejecuta diariamente via cron

echo "[$(date)] Checking for certificate renewal..."

# Intentar renovar certificados
certbot renew --webroot --webroot-path=/var/www/certbot --quiet

# Si la renovación fue exitosa, recargar nginx
if [ $? -eq 0 ]; then
    echo "[$(date)] Certificate check completed successfully"
    # Recargar nginx si hay certificados nuevos
    docker exec comunitaria-nginx nginx -s reload 2>/dev/null || true
else
    echo "[$(date)] Certificate renewal check failed"
fi
