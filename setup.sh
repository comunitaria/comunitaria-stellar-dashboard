#!/bin/bash

# Comunitaria Stellar Dashboard - Setup Script
# This script helps configure the application for different environments

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Comunitaria Stellar Dashboard Setup${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""

# Function to prompt for input with default value
prompt_with_default() {
    local prompt="$1"
    local default="$2"
    local result
    read -p "$(echo -e ${YELLOW}${prompt}${NC} [${default}]: )" result
    echo "${result:-$default}"
}

# Function to prompt for password
prompt_password() {
    local prompt="$1"
    local result
    read -s -p "$(echo -e ${YELLOW}${prompt}${NC}: )" result
    echo ""
    echo "${result}"
}

# Check if .env exists
if [ -f .env ]; then
    echo -e "${YELLOW}Warning: .env file already exists.${NC}"
    read -p "Do you want to overwrite it? (y/N): " overwrite
    if [[ ! $overwrite =~ ^[Yy]$ ]]; then
        echo "Setup cancelled."
        exit 0
    fi
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    echo -e "${GREEN}Backup created.${NC}"
fi

# Create .env file
echo "Creating .env configuration..."
echo ""

# Environment selection
echo -e "${GREEN}1. Select Stellar Network${NC}"
echo "  1) Testnet (for development/testing)"
echo "  2) Public (for production)"
read -p "Select network (1/2): " network_choice

if [ "$network_choice" = "2" ]; then
    STELLAR_NETWORK="public"
    STELLAR_NODE="https://horizon.stellar.org"
    CI_ENVIRONMENT="production"
    echo -e "${RED}WARNING: You are configuring for PRODUCTION (mainnet)${NC}"
    echo -e "${RED}Make sure you have REAL XLM and have generated NEW keypairs!${NC}"
    read -p "Press Enter to continue..."
else
    STELLAR_NETWORK="testnet"
    STELLAR_NODE="https://horizon-testnet.stellar.org"
    CI_ENVIRONMENT="development"
fi

echo ""
echo -e "${GREEN}2. Application Configuration${NC}"
APP_BASE_URL=$(prompt_with_default "Application base URL" "http://localhost:8080/")
APP_NAME=$(prompt_with_default "Application title" "Gestión Comunitaria")
CLIENT_NAME=$(prompt_with_default "Organization name" "COMUNITARIA")

echo ""
echo -e "${GREEN}3. Database Configuration${NC}"
DB_HOST=$(prompt_with_default "Database host" "db")
DB_NAME=$(prompt_with_default "Database name" "comunitaria")
DB_USER=$(prompt_with_default "Database user" "comunitaria_user")
DB_PASS=$(prompt_password "Database password")
DB_ROOT_PASS=$(prompt_password "Database root password")

echo ""
echo -e "${GREEN}4. Email Configuration (for password recovery)${NC}"
MAIL_HOST=$(prompt_with_default "SMTP host" "smtp.example.com")
MAIL_PORT=$(prompt_with_default "SMTP port" "25")
MAIL_USER=$(prompt_with_default "SMTP user" "noreply@example.com")
MAIL_PASS=$(prompt_password "SMTP password")
MAIL_FROM=$(prompt_with_default "From email address" "$MAIL_USER")
MAIL_NAME=$(prompt_with_default "From name" "Comunitaria")

echo ""
echo -e "${GREEN}5. API Configuration (JWT for mobile app)${NC}"
JWT_SECRET=$(openssl rand -base64 32)
echo "Generated random JWT secret: ${JWT_SECRET:0:20}..."
JWT_EXPIRATION=$(prompt_with_default "JWT expiration in seconds" "3600")
JWT_ISSUER=$(prompt_with_default "JWT issuer" "Comunitaria")

echo ""
echo -e "${GREEN}6. Stellar Asset Configuration${NC}"
ASSET_NAME=$(prompt_with_default "Asset code (1-4 chars)" "ILLA")

echo ""
echo -e "${YELLOW}Stellar Keypair Setup${NC}"
echo "You need TWO accounts: Issuer and Distributor"
echo ""

if [ "$STELLAR_NETWORK" = "testnet" ]; then
    echo -e "${YELLOW}For TESTNET:${NC}"
    echo "  1. Go to https://laboratory.stellar.org/#account-creator?network=test"
    echo "  2. Generate keypair for ISSUER"
    echo "  3. Click 'Fund with Friendbot'"
    echo "  4. Repeat for DISTRIBUTOR"
    echo ""
fi

if [ "$STELLAR_NETWORK" = "public" ]; then
    echo -e "${RED}For MAINNET (Production):${NC}"
    echo ""
    echo "IMPORTANT: Generate NEW keypairs - NEVER reuse testnet keys!"
    echo ""
    echo "Option 1 - Stellar Laboratory (Web):"
    echo "  1. Go to: https://laboratory.stellar.org/#account-creator?network=public"
    echo "  2. Click 'Generate keypair' for ISSUER account"
    echo "  3. Save BOTH keys securely (use password manager or vault)"
    echo "  4. Repeat for DISTRIBUTOR account"
    echo ""
    echo "Option 2 - Command Line (More Secure):"
    echo "  Install Stellar SDK: npm install -g stellar-sdk"
    echo "  Generate keypair:"
    echo "    node -e \"const StellarSdk = require('stellar-sdk'); const pair = StellarSdk.Keypair.random(); console.log('Public:', pair.publicKey()); console.log('Secret:', pair.secret());\""
    echo ""
    echo "After generating keypairs:"
    echo "  1. Fund BOTH accounts with XLM (minimum 3-5 XLM each recommended)"
    echo "     - Use an exchange (Coinbase, Kraken, etc.)"
    echo "     - Or use a Stellar wallet service"
    echo "  2. Verify funding at: https://stellar.expert"
    echo "  3. NEVER share or commit these keys!"
    echo "  4. Store backups in a secure location (encrypted, offline)"
    echo ""
fi

read -p "Press Enter when you have your keypairs ready..."

echo ""
ISSUER_PUBLIC=$(prompt_with_default "Issuer PUBLIC key (without G prefix)" "")
ISSUER_PRIVATE=$(prompt_password "Issuer SECRET key (without S prefix)")

echo ""
DISTRIBUTOR_PUBLIC=$(prompt_with_default "Distributor PUBLIC key (without G prefix)" "")
DISTRIBUTOR_PRIVATE=$(prompt_password "Distributor SECRET key (without S prefix)")

echo ""
echo -e "${GREEN}7. XLM Balance Management${NC}"
XLM_MIN=$(prompt_with_default "Minimum XLM balance for user accounts" "2.8")
XLM_MAX=$(prompt_with_default "Maximum XLM balance for user accounts" "3.0")

# Generate .env file
cat > .env << EOF
#--------------------------------------------------------------------
# Environment Configuration file
# Generated on $(date)
#--------------------------------------------------------------------

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

CI_ENVIRONMENT = ${CI_ENVIRONMENT}
app.baseURL = '${APP_BASE_URL}'
app.forceGlobalSecureRequests = false
app.indexPage = ''

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = ${DB_HOST}
database.default.database = ${DB_NAME}
database.default.username = ${DB_USER}
database.default.password = ${DB_PASS}
database.default.DBDriver = MySQLi

#--------------------------------------------------------------------
# INTERFACE 
#--------------------------------------------------------------------

Config\\VstPortal.UAT = false
Config\\VstPortal.tituloWeb = '${APP_NAME}'
Config\\VstPortal.nombreCliente = '${CLIENT_NAME}'
Config\\VstPortal.contenidoPie = ''
Config\\VstPortal.menuLateral = '{"home":{"texto":"Inicio","icono":"fas fa-home","href":"\\/","aut":""},"transacciones":{"texto":"Movimientos","icono":"fas fa-money-bill","href":"\\/transacciones","aut":"2"},"donaciones":{"texto":"Donaciones","icono":"fas fa-hand-holding-usd","href":"\\/donaciones","aut":"2,4"},"reintegros":{"texto":"Reintegros","icono":"fas fa-sync","href":"\\/reintegros","aut":"2,4"},"menu_convenios":{"texto":"Convenios","icono":"fas fa-hands-helping","href":"","aut":"2","menu":{"registro_beneficiarios":{"texto":"Beneficiarios","icono":"fas fa-user-friends","href":"\\/beneficiarios","aut":"2"},"registro_comercios":{"texto":"Comercios","icono":"fas fa-store","href":"\\/comercios","aut":"2"}}}}'
Config\\VstPortal.menuSuperior = '[]'
Config\\VstPortal.menuConfig = '{"usuarios":{"texto":"Usuarios","icono":"fas fa-user","href":"\\/usuarios","aut":"2"},"general":{"texto":"General","icono":"fas fa-cog","href":"\\/configuraciongeneral","aut":"3"}}'
Config\\VstPortal.permisos=''
Config\\VstPortal.tamanoTexto = 'sm'
Config\\VstPortal.tonalidad = 'light-mode'
Config\\VstPortal.acento = 'success'
Config\\VstPortal.lateralDark = 'light'
Config\\VstPortal.lateralDestacado = 'lime'
Config\\VstPortal.superiorDark = 'light'
Config\\VstPortal.configuracionDark = 'light'

#--------------------------------------------------------------------
# MAIL
#--------------------------------------------------------------------

mail.SMTPHost='${MAIL_HOST}'
mail.SMTPUser='${MAIL_USER}'
mail.fromUser='${MAIL_FROM}'
mail.fromName='${MAIL_NAME}'
mail.SMTPPass='${MAIL_PASS}'
mail.SMTPPort=${MAIL_PORT}

#--------------------------------------------------------------------
# API
#--------------------------------------------------------------------

api.JWT_secreto = '${JWT_SECRET}'
api.expiracion_s=${JWT_EXPIRATION}
api.emisor="${JWT_ISSUER}"
api.audiencia="App movil Comunitaria"
api.objeto="Autentificacion acceso a datos app movil"

#--------------------------------------------------------------------
# COIN
#--------------------------------------------------------------------

moneda.red='${STELLAR_NETWORK}'
moneda.nombre = '${ASSET_NAME}'
moneda.emisora.publica='${ISSUER_PUBLIC}'
moneda.emisora.privada='${ISSUER_PRIVATE}'
moneda.distribuidora.publica='${DISTRIBUTOR_PUBLIC}'
moneda.distribuidora.privada='${DISTRIBUTOR_PRIVATE}'
moneda.XLM.minimo=${XLM_MIN}
moneda.XLM.maximo=${XLM_MAX}
moneda.nodo.testnet = "https://horizon-testnet.stellar.org"
moneda.nodo.public = "https://horizon.stellar.org"

# Force http for base_url helper in development
FORCE_HTTP=$([ "$CI_ENVIRONMENT" = "development" ] && echo "true" || echo "false")

# CORS Configuration (production: restrict to your domains)
CORS_ALLOWED_ORIGINS=$([ "$CI_ENVIRONMENT" = "production" ] && echo "https://yourdomain.com,https://app.yourdomain.com" || echo "*")
EOF

# Create docker-compose.override.yml for environment variables
cat > docker-compose.override.yml << EOF
version: '3.8'

services:
  app:
    environment:
      - DB_ROOT_PASSWORD=${DB_ROOT_PASS}
      - DB_DATABASE=${DB_NAME}
      - DB_USERNAME=${DB_USER}
      - DB_PASSWORD=${DB_PASS}
      - APP_PORT=8080
  
  db:
    environment:
      - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASS}
      - MYSQL_DATABASE=${DB_NAME}
      - MYSQL_USER=${DB_USER}
      - MYSQL_PASSWORD=${DB_PASS}
EOF

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}Configuration completed!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo "Created files:"
echo "  - .env (application configuration)"
echo "  - docker-compose.override.yml (docker environment)"
echo ""

if [ "$STELLAR_NETWORK" = "testnet" ]; then
    echo -e "${YELLOW}Next steps for TESTNET:${NC}"
    echo "  1. Start the application: docker-compose up -d"
    echo "  2. Initialize the asset:"
    echo "     docker-compose exec app php scripts/setup_illa.php 10000"
    echo "  3. Access the dashboard at: ${APP_BASE_URL}"
    echo "  4. Default credentials: adm / 1 (change immediately!)"
else
    echo -e "${RED}Next steps for MAINNET:${NC}"
    echo "  1. VERIFY your Issuer and Distributor accounts are funded with XLM"
    echo "  2. Start the application: docker-compose up -d"
    echo "  3. Initialize the asset:"
    echo "     docker-compose exec app php scripts/setup_illa.php <amount>"
    echo "  4. Set up SSL/TLS (use nginx-proxy or traefik)"
    echo "  5. Configure CORS_ALLOWED_ORIGINS in .env with your real domains"
    echo "  6. Access the dashboard at: ${APP_BASE_URL}"
    echo "  7. Default credentials: adm / 1 (CHANGE IMMEDIATELY!)"
    echo ""
    echo -e "${RED}SECURITY WARNINGS:${NC}"
    echo "  - Never commit .env to version control"
    echo "  - Set file permissions: chmod 600 .env"
    echo "  - Use a secrets manager for production"
    echo "  - Enable app.forceGlobalSecureRequests in .env"
    echo "  - Set up regular database backups"
fi

echo ""
echo -e "${GREEN}Setup complete!${NC}"
