# Docker Deployment Guide

## Quick Start

### 1. Run the Interactive Setup
```bash
./setup.sh
```

This will guide you through:
- Selecting Stellar network (testnet/mainnet)
- Configuring database credentials
- Setting up email for password recovery
- Generating JWT secrets for API
- Configuring Stellar keypairs (issuer/distributor)
- Setting XLM balance thresholds

### 2. Start the Application
```bash
docker-compose up -d
```

### 3. Initialize the Stellar Asset
```bash
# For testnet with 10,000 initial supply
docker-compose exec app php scripts/setup_illa.php 10000

# For mainnet - use your desired initial supply
docker-compose exec app php scripts/setup_illa.php 1000000
```

### 4. Access the Dashboard
- Open your browser to the configured base URL (default: http://localhost:8080)
- Login with default credentials: `adm` / `1`
- **Change the password immediately!**

---

## Manual Setup (Alternative)

If you prefer to configure manually instead of using `setup.sh`:

### 1. Copy Environment Template
```bash
cp .env.example .env
```

### 2. Edit .env Configuration

#### Required Changes:

**Database:**
```bash
database.default.hostname = db
database.default.database = comunitaria
database.default.username = comunitaria_user
database.default.password = YOUR_SECURE_PASSWORD
```

**Stellar Network:**
```bash
# For testnet
moneda.red='testnet'
moneda.nodo.testnet = "https://horizon-testnet.stellar.org"

# For mainnet
moneda.red='public'
moneda.nodo.public = "https://horizon.stellar.org"
```

**Stellar Keypairs:**
```bash
# Generate at https://laboratory.stellar.org/#account-creator
# Store PUBLIC keys WITHOUT the 'G' prefix
# Store SECRET keys WITHOUT the 'S' prefix

moneda.emisora.publica='YOUR_ISSUER_PUBLIC_WITHOUT_G'
moneda.emisora.privada='YOUR_ISSUER_SECRET_WITHOUT_S'
moneda.distribuidora.publica='YOUR_DISTRIBUTOR_PUBLIC_WITHOUT_G'
moneda.distribuidora.privada='YOUR_DISTRIBUTOR_SECRET_WITHOUT_S'
```

### 3. Create docker-compose.override.yml
```yaml
version: '3.8'

services:
  app:
    environment:
      - DB_ROOT_PASSWORD=secure_root_password
      - DB_DATABASE=comunitaria
      - DB_USERNAME=comunitaria_user
      - DB_PASSWORD=secure_password
      - APP_PORT=8080
  
  db:
    environment:
      - MYSQL_ROOT_PASSWORD=secure_root_password
      - MYSQL_DATABASE=comunitaria
      - MYSQL_USER=comunitaria_user
      - MYSQL_PASSWORD=secure_password
```

---

## Stellar Network Configuration

### Testnet Setup (Development)

1. **Generate Keypairs:**
   - Visit: https://laboratory.stellar.org/#account-creator?network=test
   - Generate two keypairs (Issuer and Distributor)
   - Click "Fund with Friendbot" for each account

2. **Configure .env:**
   ```bash
   moneda.red='testnet'
   ```

3. **No additional funding required** - Friendbot provides test XLM

### Mainnet Setup (Production)

1. **Generate Keypairs:**
   - Visit: https://laboratory.stellar.org/#account-creator
   - Generate two keypairs securely
   - **Store backup copies in a secure vault**

2. **Fund Accounts:**
   - Send at least 3 XLM to each account (Issuer and Distributor)
   - You can use exchanges or Stellar services

3. **Configure .env:**
   ```bash
   moneda.red='public'
   ```

4. **Security Checklist:**
   - [ ] Never commit .env to version control
   - [ ] Set file permissions: `chmod 600 .env`
   - [ ] Enable HTTPS/SSL in production
   - [ ] Restrict CORS origins
   - [ ] Use secrets manager for production keys
   - [ ] Set up regular backups

---

## Asset Configuration

### Token Parameters

```bash
# Asset code: 1-4 alphanumeric characters
moneda.nombre = 'ILLA'

# XLM auto-replenishment thresholds
moneda.XLM.minimo=2.8  # Trigger refill below this
moneda.XLM.maximo=3.0  # Refill up to this amount
```

### Issuer vs Distributor

- **Issuer Account:** Creates the asset and controls authorization
- **Distributor Account:** Holds supply and distributes to users
- This two-account model provides better security and accounting

### Initial Asset Setup

The `scripts/setup_illa.php` script performs:
1. Creates trustline from Distributor to Asset
2. Issues initial supply from Issuer to Distributor
3. Verifies configuration

```bash
# Run after starting docker-compose
docker-compose exec app php scripts/setup_illa.php <amount>
```

---

## Docker Commands

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f db
```

### Access Application Container
```bash
docker-compose exec app bash
```

### Access Database
```bash
docker-compose exec db mysql -u comunitaria_user -p comunitaria
```

### Restart Services
```bash
docker-compose restart
```

### Rebuild After Changes
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

---

## Maintenance

### Database Backup
```bash
docker-compose exec db mysqldump -u root -p comunitaria > backup_$(date +%Y%m%d).sql
```

### Database Restore
```bash
cat backup_20250110.sql | docker-compose exec -T db mysql -u root -p comunitaria
```

### View Cron Logs
```bash
docker-compose exec app tail -f /var/www/html/writable/logs/cron.log
```

### Update Application
```bash
git pull
docker-compose down
docker-compose build
docker-compose up -d
```

---

## Troubleshooting

### Database Connection Issues
```bash
# Check if database is ready
docker-compose exec db mysql -u root -p -e "SHOW DATABASES;"

# Verify credentials match .env
docker-compose exec app env | grep DB_
```

### Permission Issues
```bash
# Fix writable directory permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/writable
docker-compose exec app chmod -R 777 /var/www/html/writable
```

### Stellar Connection Issues
```bash
# Verify network configuration
docker-compose exec app php -r "echo getenv('moneda.red');"

# Test Horizon connectivity
docker-compose exec app curl -I https://horizon-testnet.stellar.org/
```

### View PHP Errors
```bash
docker-compose exec app tail -f /var/www/html/writable/logs/*.log
```

---

## Production Deployment

### SSL/TLS Setup

Use a reverse proxy like nginx or Traefik:

**Example nginx-proxy:**
```yaml
services:
  nginx-proxy:
    image: jwilder/nginx-proxy
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - /var/run/docker.sock:/tmp/docker.sock:ro
      - ./certs:/etc/nginx/certs

  app:
    environment:
      - VIRTUAL_HOST=yourdomain.com
      - LETSENCRYPT_HOST=yourdomain.com
      - LETSENCRYPT_EMAIL=admin@yourdomain.com
```

### Security Hardening

1. **Update .env for production:**
   ```bash
   CI_ENVIRONMENT = production
   app.forceGlobalSecureRequests = true
   FORCE_HTTP = false
   CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
   ```

2. **File permissions:**
   ```bash
   chmod 600 .env
   chmod 600 docker-compose.override.yml
   ```

3. **Firewall rules:**
   ```bash
   # Only allow necessary ports
   ufw allow 80/tcp
   ufw allow 443/tcp
   ufw deny 3306/tcp  # Don't expose database publicly
   ```

4. **Regular updates:**
   ```bash
   # Schedule weekly
   docker-compose pull
   docker-compose up -d
   ```

---

## Environment Variables Reference

### Required Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `database.default.hostname` | Database host | `db` |
| `database.default.database` | Database name | `comunitaria` |
| `database.default.username` | Database user | `comunitaria_user` |
| `database.default.password` | Database password | `secure_password` |
| `moneda.red` | Stellar network | `testnet` or `public` |
| `moneda.nombre` | Asset code (1-4 chars) | `ILLA` |
| `moneda.emisora.publica` | Issuer public key (no G) | `DZFV...ENYG` |
| `moneda.emisora.privada` | Issuer secret key (no S) | `CMWS...BKQR` |
| `moneda.distribuidora.publica` | Distributor public (no G) | `CERS...P464` |
| `moneda.distribuidora.privada` | Distributor secret (no S) | `CQL2...IENW` |

### Optional Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_PORT` | External port for app | `8080` |
| `DB_PORT` | External port for database | `3306` |
| `moneda.XLM.minimo` | Min XLM per account | `2.8` |
| `moneda.XLM.maximo` | Max XLM per account | `3.0` |
| `FORCE_HTTP` | Force HTTP in dev | `true` |
| `CORS_ALLOWED_ORIGINS` | Allowed origins | `*` |

---

## Support

For issues or questions:
- Check logs: `docker-compose logs -f`
- Review configuration: `docker-compose config`
- Test Stellar connectivity: Visit Laboratory (https://laboratory.stellar.org)
- Database status: `docker-compose exec db mysqladmin status -u root -p`
