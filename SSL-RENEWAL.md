# SSL Certificate Auto-Renewal

Los certificados de Let's Encrypt vencen cada **90 días** y deben renovarse periódicamente. Este documento explica las opciones disponibles para renovación automática.

## ⚙️ Opciones Disponibles

### Opción 1: Renovación Automática con Contenedor (Recomendada para simplicidad)

El servicio `certbot` en docker-compose.prod.yml está configurado para:
- Verificar renovación cada 12 horas
- Renovar automáticamente cuando falten menos de 30 días para vencimiento
- Recargar nginx después de renovación exitosa

**Ventajas:**
- Completamente automático
- No requiere configuración adicional
- Se ejecuta dentro del ecosistema Docker

**Desventajas:**
- Monta el socket de Docker (puede ser considerado un riesgo de seguridad)

**Estado:** Ya está configurado en docker-compose.prod.yml

### Opción 2: Renovación con Cron en el Host (Recomendada para producción)

Usar un cron job en el servidor host para ejecutar el script de renovación.

**Ventajas:**
- Más seguro (no requiere montar Docker socket en contenedor)
- Control total desde el host
- Logs centralizados

**Configuración:**

1. Abrir el crontab del usuario:
```bash
crontab -e
```

2. Agregar esta línea para ejecutar diariamente a las 3 AM:
```bash
0 3 * * * /Users/luciano/Documents/comunitaria/comunitaria-stellar-dashboard/renew-certificates.sh >> /var/log/certbot-renewal.log 2>&1
```

O ejecutar dos veces al día (recomendado por Let's Encrypt):
```bash
0 3,15 * * * /Users/luciano/Documents/comunitaria/comunitaria-stellar-dashboard/renew-certificates.sh >> /var/log/certbot-renewal.log 2>&1
```

3. Verificar que el cron job está configurado:
```bash
crontab -l
```

## 🔧 Configuración Actual

El [docker-compose.prod.yml](docker-compose.prod.yml:1) ahora incluye dos servicios:

- **`certbot-init`**: Obtiene el certificado inicial (profile "setup", se ejecuta manualmente solo la primera vez)
- **`certbot`**: Servicio continuo que verifica y renueva certificados cada 12 horas (se inicia automáticamente)

### Primera vez - Obtener certificado inicial:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml --profile setup run --rm certbot-init
```

### Después - Iniciar servicios normalmente:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml up -d
```

## 🧪 Probar Renovación Manualmente

### Desde el host (Opción 2):
```bash
./renew-certificates.sh
```

### Con Docker Compose:
```bash
# El servicio certbot renueva automáticamente cada 12h
# Para forzar renovación inmediata:
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec certbot certbot renew --force-renewal
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec nginx nginx -s reload
```

### Dry-run (simular renovación sin hacerla):
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec certbot certbot renew --dry-run
```

## 📊 Verificar Estado de Certificados

### Ver fecha de vencimiento:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec certbot certbot certificates
```

### Ver información detallada:
```bash
sudo openssl x509 -in docker/ssl/live/dashboard.comunitaria.com/cert.pem -noout -dates
```

## 🔍 Monitoreo y Logs

### Ver logs del servicio certbot:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml logs certbot
```

### Ver logs de renovación (si usas cron):
```bash
tail -f /var/log/certbot-renewal.log
```

### Ver logs de nginx:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml logs nginx
```

## 🚨 Troubleshooting

### "Another instance of Certbot is already running"

Si ves este error, detén los servicios y reinicia:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml down
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml up -d
```

El servicio `certbot-init` ahora usa profile "setup" y no se ejecuta automáticamente.

### "Connection refused" al obtener certificado

Let's Encrypt no puede acceder a tu dominio. Verifica:
1. DNS apunta a tu servidor: `dig dashboard.comunitaria.com +short`
2. Puerto 80 abierto: `sudo ufw status` o `sudo firewall-cmd --list-all`
3. Nginx corriendo: `sudo docker compose ps nginx`
4. Accesible desde internet: `curl http://dashboard.comunitaria.com`

### Certificados no se renuevan

1. Verificar que el contenedor certbot está corriendo:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml ps certbot
```

2. Ver logs para identificar errores:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml logs certbot --tail=100
```

3. Verificar conectividad con Let's Encrypt:
```bash
curl -I http://dashboard.comunitaria.com/.well-known/acme-challenge/test
```

### Nginx no recarga después de renovación

1. Verificar que nginx está corriendo:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml ps nginx
```

2. Recargar nginx manualmente:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec nginx nginx -s reload
```

3. Si el reload falla, reiniciar nginx:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml restart nginx
```

### Renovación falla por rate limit

Let's Encrypt tiene límites de tasa:
- 50 certificados por dominio registrado por semana
- 5 renovaciones duplicadas por semana

Si alcanzas el límite, espera una semana o usa certificados staging para pruebas:
```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml run --rm certbot-init certonly --staging --webroot --webroot-path=/var/www/certbot -d dashboard.comunitaria.com
```

## 📋 Checklist de Despliegue

- [ ] Certificado inicial obtenido correctamente
- [ ] Nginx configurado con HTTPS funcional
- [ ] Servicio certbot corriendo (Opción 1) o Cron configurado (Opción 2)
- [ ] Renovación manual probada exitosamente
- [ ] Logs de renovación monitoreados
- [ ] Alertas configuradas para fallos de renovación (opcional)

## 🔐 Consideraciones de Seguridad

### Opción 1 (Contenedor con Docker socket):
- El socket de Docker da acceso privilegiado al contenedor
- Solo usar en ambientes de confianza
- Considerar usar [docker-socket-proxy](https://github.com/Tecnativa/docker-socket-proxy) para mayor seguridad

### Opción 2 (Cron en host):
- Más seguro: no expone Docker socket a contenedores
- Recomendado para ambientes de producción
- Los logs están en el host, más fáciles de auditar

## 📚 Referencias

- [Let's Encrypt - How It Works](https://letsencrypt.org/how-it-works/)
- [Certbot Documentation](https://eff-certbot.readthedocs.io/)
- [Let's Encrypt Rate Limits](https://letsencrypt.org/docs/rate-limits/)
