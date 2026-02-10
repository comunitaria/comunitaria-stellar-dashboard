# SSL Certificate Setup Guide

## Problem Overview

When deploying to production with HTTPS, there's a **chicken-and-egg problem**:
- Nginx requires SSL certificates to start (in HTTPS mode)
- Certbot requires nginx to be running to obtain certificates
- The domain must be accessible on port 80 from the internet

## Issues You May Encounter

1. **Nginx crash-loop**: Cannot load missing SSL certificates
2. **Certbot failure**: Domain not accessible on port 80 from internet
3. **Connection refused**: Port 80 not open or domain DNS not configured

## Solution: Staged SSL Setup

### Quick Fix (Automated)

Run the provided setup script:

```bash
sudo ./setup-ssl.sh
```

This script will:
1. Start with HTTP-only nginx configuration
2. Obtain SSL certificate from Let's Encrypt
3. Switch to HTTPS configuration automatically

### Manual Setup (Step-by-step)

If you prefer manual control:

#### Step 1: Start with HTTP-only

```bash
# Stop current containers
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml down

# Use initial HTTP-only config
cp docker/nginx-initial.conf docker/nginx.conf

# Create SSL directory
mkdir -p docker/ssl

# Start containers
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml up -d
```

#### Step 2: Verify HTTP access

```bash
# Check if nginx is running
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml ps

# Test HTTP access locally
curl http://localhost/

# Test from another machine (replace with your server IP)
curl http://YOUR_SERVER_IP/
```

**Important**: Make sure port 80 is accessible from the internet before proceeding.

#### Step 3: Obtain SSL certificate

```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml --profile setup run --rm certbot-init
```

O con parámetros personalizados:

```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml --profile setup run --rm certbot-init \
  certonly --webroot \
  --webroot-path=/var/www/certbot \
  --email admin@comunitaria.com \
  --agree-tos \
  --no-eff-email \
  -d dashboard.comunitaria.com
```

#### Step 4: Switch to HTTPS

Once certificates are obtained:

```bash
# Restore full nginx config with HTTPS
# The original HTTPS config should be in git or backed up
if [ -f "docker/nginx-https.conf" ]; then
    cp docker/nginx-https.conf docker/nginx.conf
else
    git checkout docker/nginx.conf
fi

# Verify HTTPS is configured
grep "listen 443" docker/nginx.conf

# Restart nginx
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml restart nginx

# Verify HTTPS works
curl https://dashboard.comunitaria.com
```

**Important:** Make sure `docker/nginx.conf` in your repository contains the full HTTPS configuration with SSL certificates before running `setup-ssl.sh`.

## Prerequisites

Before running the SSL setup:

1. **DNS Configuration**: Ensure `dashboard.comunitaria.com` points to your server's public IP
   ```bash
   # Check DNS resolution
   dig dashboard.comunitaria.com +short
   nslookup dashboard.comunitaria.com
   ```

2. **Firewall Rules**: Open ports 80 and 443
   ```bash
   # For UFW (Ubuntu)
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   
   # For firewalld (CentOS/RHEL)
   sudo firewall-cmd --permanent --add-service=http
   sudo firewall-cmd --permanent --add-service=https
   sudo firewall-cmd --reload
   ```

3. **No Port Conflicts**: Ensure no other service is using ports 80/443
   ```bash
   # Check what's using port 80
   sudo lsof -i :80
   
   # Check what's using port 443
   sudo lsof -i :443
   ```

4. **Internet Accessibility**: Your server must be reachable from the internet
   ```bash
   # Test from external service (https://ping.eu/port-chk/)
   # Or from another machine:
   telnet YOUR_SERVER_IP 80
   ```

## Troubleshooting

### Certbot still fails

If certbot continues to fail with "Connection refused":

1. **Check DNS**: Verify domain points to your server
   ```bash
   dig dashboard.comunitaria.com +short
   # Should show your server's public IP
   ```

2. **Check firewall**: Ensure port 80 is open
   ```bash
   sudo netstat -tulpn | grep :80
   ```

3. **Check nginx logs**:
   ```bash
   sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml logs nginx
   ```

4. **Test ACME challenge manually**:
   ```bash
   # Create test file
   sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec nginx \
     sh -c "mkdir -p /var/www/certbot/.well-known/acme-challenge && echo 'test' > /var/www/certbot/.well-known/acme-challenge/test.txt"
   
   # Try to access it
   curl http://dashboard.comunitaria.com/.well-known/acme-challenge/test.txt
   ```

### Nginx won't start with HTTPS

If nginx fails after obtaining certificates:

1. **Check certificate files**:
   ```bash
   ls -la docker/ssl/live/dashboard.comunitaria.com/
   ```

2. **Verify permissions**:
   ```bash
   sudo chmod -R 755 docker/ssl
   ```

3. **Check nginx config syntax**:
   ```bash
   sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec nginx nginx -t
   ```

### Testing locally without internet access

For development/testing without a real domain:

1. Use docker-compose.yml only (skip production overlay):
   ```bash
   docker compose -f docker-compose.yml up -d
   ```

2. Access directly via http://localhost or http://YOUR_LOCAL_IP

## Certificate Renewal

Los certificados están configurados para renovarse automáticamente. Ver [SSL-RENEWAL.md](SSL-RENEWAL.md) para más detalles sobre las opciones de renovación automática.

Para renovar manualmente:

```bash
./renew-certificates.sh
```

O forzando renovación inmediata:

```bash
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec certbot certbot renew --force-renewal
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec nginx nginx -s reload
```

## Files Created/Modified

1. **docker/nginx-initial.conf**: HTTP-only nginx config for initial setup
2. **docker/nginx.conf**: Full HTTPS configuration (updated http2 directive)
3. **Dockerfile**: Added `cron` package for crontab command
4. **docker/apache-vhost.conf**: Added `ServerName dashboard.comunitaria.com`
5. **docker-compose.yml**: Removed deprecated `version: '3.8'`
6. **setup-ssl.sh**: Automated SSL setup script

## Next Steps

After SSL is working:

1. Initialize Stellar asset (if not done):
   ```bash
   sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec app \
     php scripts/setup_illa.php 10000000
   ```

2. Verify dashboard access:
   ```bash
   curl -I https://dashboard.comunitaria.com
   ```

3. Monitor logs:
   ```bash
   sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml logs -f
   ```

4. Change default credentials immediately (login as `adm` / `1`)
