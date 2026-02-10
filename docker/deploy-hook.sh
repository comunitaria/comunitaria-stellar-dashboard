#!/bin/sh

# Deploy hook para certbot - se ejecuta después de renovar certificados
# Este script recarga nginx para aplicar los nuevos certificados

echo "[$(date)] Certificate renewed successfully. Reloading nginx..."

# Enviar señal de reload a nginx
if docker exec comunitaria-nginx nginx -s reload 2>/dev/null; then
    echo "[$(date)] Nginx reloaded successfully"
else
    echo "[$(date)] Warning: Could not reload nginx"
fi
