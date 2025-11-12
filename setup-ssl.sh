#!/bin/bash

# SSL Certificate Setup Script for Production
# This script helps obtain Let's Encrypt SSL certificates for dashboard.comunitaria.com

set -e

echo "=============================================="
echo "SSL Certificate Setup for Comunitaria Dashboard"
echo "=============================================="
echo ""

# Check if running as root/sudo
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run with sudo"
    exit 1
fi

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

COMPOSE_CMD="docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml"

echo "📋 Prerequisites:"
echo "   1. Domain dashboard.comunitaria.com must point to this server's IP"
echo "   2. Ports 80 and 443 must be open in your firewall"
echo "   3. No other service should be using port 80 or 443"
echo ""

read -p "Have you completed the prerequisites above? (yes/no): " READY
if [ "$READY" != "yes" ]; then
    echo "Please complete the prerequisites first."
    exit 1
fi

echo ""
echo "🔄 Step 1: Starting with HTTP-only nginx configuration..."

# Stop any running containers
echo "Stopping existing containers..."
$COMPOSE_CMD down 2>/dev/null || true

# Backup existing nginx config and use initial config
if [ -f "docker/nginx.conf" ]; then
    cp docker/nginx.conf docker/nginx.conf.backup
    echo "✅ Backed up nginx.conf to nginx.conf.backup"
fi

cp docker/nginx-initial.conf docker/nginx.conf
echo "✅ Using HTTP-only nginx configuration"

# Create ssl and certbot directories
mkdir -p docker/ssl
mkdir -p docker/certbot-webroot
echo "✅ Created SSL and certbot directories"

echo ""
echo "🚀 Step 2: Starting containers with HTTP-only configuration..."
$COMPOSE_CMD up -d

# Wait for services to be ready
echo "⏳ Waiting for services to start..."
sleep 10

# Check if nginx is responding
echo "🔍 Checking if nginx is accessible..."
if curl -f http://localhost/ > /dev/null 2>&1; then
    echo "✅ Nginx is responding on HTTP"
else
    echo "❌ Nginx is not responding. Checking logs..."
    $COMPOSE_CMD logs nginx
    exit 1
fi

echo ""
echo "🔐 Step 3: Obtaining SSL certificate from Let's Encrypt..."
echo "This may take a minute..."

# Run certbot to obtain certificate
if $COMPOSE_CMD run --rm certbot certonly --webroot --webroot-path=/var/www/certbot \
    --email admin@comunitaria.com \
    --agree-tos \
    --no-eff-email \
    -d dashboard.comunitaria.com; then
    echo "✅ SSL certificate obtained successfully!"
else
    echo "❌ Failed to obtain SSL certificate."
    echo ""
    echo "Common issues:"
    echo "  - DNS not pointing to this server"
    echo "  - Firewall blocking port 80"
    echo "  - Domain not accessible from internet"
    echo ""
    echo "Check logs with: $COMPOSE_CMD logs certbot"
    exit 1
fi

echo ""
echo "🔄 Step 4: Switching to HTTPS configuration..."

# Restore full nginx config with HTTPS
if [ -f "docker/nginx.conf.backup" ]; then
    cp docker/nginx.conf.backup docker/nginx.conf
    echo "✅ Restored full nginx configuration with HTTPS"
fi

# Restart nginx to apply new configuration
echo "🔄 Restarting nginx with HTTPS enabled..."
$COMPOSE_CMD restart nginx

# Wait for nginx to start
sleep 5

# Check if nginx is running
if $COMPOSE_CMD ps | grep -q "nginx.*Up"; then
    echo "✅ Nginx is running with HTTPS"
else
    echo "❌ Nginx failed to start. Check logs with: $COMPOSE_CMD logs nginx"
    exit 1
fi

echo ""
echo "=============================================="
echo "✅ SSL Setup Complete!"
echo "=============================================="
echo ""
echo "Your dashboard should now be accessible at:"
echo "  https://dashboard.comunitaria.com"
echo ""
echo "SSL certificate will auto-renew. Certbot runs daily via cron."
echo ""
echo "To manually renew: sudo $COMPOSE_CMD run --rm certbot renew"
echo "To check status: sudo $COMPOSE_CMD ps"
echo "To view logs: sudo $COMPOSE_CMD logs -f"
echo ""
