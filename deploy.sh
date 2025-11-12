#!/bin/bash

# Quick deployment script for Comunitaria Stellar Dashboard
# This script automates the entire deployment process for testnet, NOT PRODUCTION

set -e

echo "============================================"
echo "Comunitaria Stellar Dashboard - Quick Deploy"
echo "============================================"
echo ""

# Check if docker and docker-compose are installed
if ! command -v docker &> /dev/null; then
    echo "Error: Docker is not installed. Please install Docker first."
    echo "Visit: https://docs.docker.com/get-docker/"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "Error: docker-compose is not installed. Please install docker-compose first."
    echo "Visit: https://docs.docker.com/compose/install/"
    exit 1
fi

# Check if .env exists
if [ ! -f .env ]; then
    echo "No .env file found. Running setup..."
    ./setup.sh
else
    echo "Found existing .env configuration."
    read -p "Do you want to reconfigure? (y/N): " reconfig
    if [[ $reconfig =~ ^[Yy]$ ]]; then
        ./setup.sh
    fi
fi

echo ""
echo "Starting Docker containers..."
docker-compose up -d

echo ""
echo "Waiting for database to be ready..."
sleep 10

# Check if database is ready
echo "Checking database connection..."
until docker-compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD:-root_password} -e "SELECT 1" &> /dev/null; do
    echo "Waiting for database..."
    sleep 5
done

echo ""
echo "Database is ready!"
echo ""

# Ask if user wants to initialize the asset
read -p "Do you want to initialize the Stellar asset now? (y/N): " init_asset
if [[ $init_asset =~ ^[Yy]$ ]]; then
    read -p "Enter initial supply amount: " amount
    if [ ! -z "$amount" ]; then
        echo "Initializing asset with supply: $amount"
        docker-compose exec app php scripts/setup_illa.php $amount
    fi
fi

echo ""
echo "============================================"
echo "Deployment Complete!"
echo "============================================"
echo ""
echo "Your application is running at:"
echo "  $(grep 'app.baseURL' .env | cut -d "'" -f 2)"
echo ""
echo "Default credentials:"
echo "  Username: adm"
echo "  Password: 1"
echo ""
echo "⚠️  IMPORTANT: Change the default password immediately!"
echo ""
echo "Useful commands:"
echo "  View logs:      docker-compose logs -f"
echo "  Stop services:  docker-compose down"
echo "  Restart:        docker-compose restart"
echo ""
