#!/bin/bash
set -e

# Detect docker compose command
if command -v docker &> /dev/null && docker compose version &> /dev/null; then
    DOCKER_COMPOSE="docker compose"
elif command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
else
    echo "Error: Docker Compose not found!"
    exit 1
fi

# Create .env if not exists
if [ ! -f .env ]; then
    cat > .env <<EOF
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=financial_wallet
DB_USERNAME=financial_wallet_user
DB_PASSWORD=financial_wallet_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
EOF
fi

# Start containers
$DOCKER_COMPOSE up -d

# Wait for MariaDB
sleep 10

# Setup Laravel
$DOCKER_COMPOSE exec -T app php artisan key:generate --force
$DOCKER_COMPOSE exec -T app php artisan migrate --force
$DOCKER_COMPOSE exec -T app php artisan db:seed --force

echo "Setup complete! Application available at http://localhost:8000"
