# Docker Deployment Guide

## Quick Start

### Production Deployment (with SSL)

**Complete deployment sequence for mainnet with HTTPS:**

```bash
# 1. Run the interactive setup
./setup.sh
# - Choose: mainnet
# - Configure database, Stellar accounts, etc.
# - When asked "Build and start containers?", answer NO

# 2. Build the containers (don't start yet)
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml build

# 3. Run SSL setup script (handles certificate acquisition)
sudo ./setup-ssl.sh
# This will:
# - Start containers with HTTP-only nginx
# - Obtain SSL certificate from Let's Encrypt
# - Switch to HTTPS configuration automatically

# 4. Initialize the Stellar asset
sudo docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec app \
  php scripts/setup_illa.php 1000000
```

**Access your dashboard at:** `https://dashboard.comunitaria.com`

---

### Testnet Deployment (Simple, No SSL)

**For development/testing:**

```bash
# 1. Run the interactive setup
./setup.sh
# - Choose: testnet
# - Configure database, Stellar accounts, etc.
# - When asked "Build and start containers?", answer YES

# 2. Initialize the Stellar asset (if not done by setup.sh)
docker compose -f docker-compose.yml exec app php scripts/setup_illa.php 10000
```

**Access your dashboard at:** `http://localhost:8080`

---

### Alternative: Using deploy.sh

**⚠️ Note:** `deploy.sh` is for **testnet only** and does NOT support SSL setup.

```bash
# Only for testnet/development
./deploy.sh
```

**For production with SSL, use the manual steps above or `setup-ssl.sh`.**

---

## Detailed Setup Instructions

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
- Optional: Automatically build and start containers (recommended for testnet)
- Optional: Initialize the Stellar asset

**Important for Production:** When deploying mainnet with SSL, answer NO when asked to build/start containers. You'll use `setup-ssl.sh` instead.

**Note:** The script uses `compose.env` for Docker Compose environment variables (separate from application `.env`).

### 2. Start the Application (if not done by setup.sh)

**For Testnet (development):**
```bash
# Build images
docker compose -f docker-compose.yml build --no-cache

# Start containers
docker compose -f docker-compose.yml up -d
```

**For Mainnet (production):**
```bash
# Build images (this installs all Composer dependencies)
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml build --no-cache

# Start containers
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml up -d
```

**Important:** 
- Use `--env-file compose.env` for production to avoid conflicts with the application's `.env` file
- The `build` step automatically installs all PHP dependencies via Composer
- Permissions are automatically fixed on container startup

### 3. Initialize the Stellar Asset (if not done by setup.sh)

**For Testnet:**
```bash
docker compose -f docker-compose.yml exec app php scripts/setup_illa.php 10000
```

**For Mainnet:**
```bash
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec app php scripts/setup_illa.php 1000000
```

### 4. Access the Dashboard
- Open your browser to the configured base URL (default: http://localhost:8080)
- Login with default credentials: `adm` / `1`
- **Change the password immediately!**

---

## Environment Files

This setup uses TWO environment files:

1. **`.env`** - Application configuration (CodeIgniter, database, Stellar keys)
   - Created by `setup.sh`
   - Used by the PHP application at runtime
   - Contains Stellar keys, JWT secrets, database credentials

2. **`compose.env`** (optional, for production) - Docker Compose environment variables
   - Only needed for production with `docker-compose.prod.yml`
   - Contains variables for Docker networking and container configuration
   - Separate from application config to avoid conflicts

**Security:** Both files should have `chmod 600` permissions and never be committed to version control.

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

**Testnet:**
```bash
docker compose -f docker-compose.yml exec app php scripts/setup_illa.php <amount>
```

**Mainnet:**
```bash
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec app php scripts/setup_illa.php <amount>
```

---

## Docker Commands

All commands below use the appropriate compose file(s) for your environment.

**For Testnet (development):**
- Use: `docker compose -f docker-compose.yml`

**For Mainnet (production):**
- Use: `docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml`

### Start Services

**Testnet:**
```bash
docker compose -f docker-compose.yml up -d
```

**Mainnet:**
```bash
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### Stop Services

**Testnet:**
```bash
docker compose -f docker-compose.yml down
```

**Mainnet:**
```bash
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml down
```

### View Logs

**Testnet:**
```bash
# All services
docker compose -f docker-compose.yml logs -f

# Specific service
docker compose -f docker-compose.yml logs -f app
```

**Mainnet:**
```bash
# All services
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml logs -f

# Specific service
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml logs -f app
```

### Access Application Container

**Testnet:**
```bash
docker compose -f docker-compose.yml exec app bash
```

**Mainnet:**
```bash
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec app bash
```

### Access Database

**Testnet:**
```bash
docker compose -f docker-compose.yml exec db mysql -u comunitaria_user -p comunitaria
```

**Mainnet:**
```bash
docker compose --env-file compose.env -f docker-compose.yml -f docker-compose.prod.yml exec db mysql -u comunitaria_user -p comunitaria
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
