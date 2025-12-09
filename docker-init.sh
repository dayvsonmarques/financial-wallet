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

# Create .env if not exists (basic defaults for local dev)
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

APP_CONTAINER="financial-wallet-app"
DB_CONTAINER="financial-wallet-mariadb"

echo "[1/4] Subindo banco (MariaDB)…"
$DOCKER_COMPOSE up -d mariadb

# Aguarda health=healthy do MariaDB (até 90s)
echo "Aguardando MariaDB ficar saudável…"
for i in {1..30}; do
    status=$(docker inspect -f '{{if .State.Health}}{{.State.Health.Status}}{{end}}' "$DB_CONTAINER" || echo "")
    if [ "$status" = "healthy" ]; then
        echo "MariaDB saudável."
        break
    fi
    sleep 3
    if [ "$i" -eq 30 ]; then
        echo "Erro: MariaDB não ficou saudável a tempo." >&2
        $DOCKER_COMPOSE logs mariadb || true
        exit 1
    fi
done

echo "[2/4] Subindo app (Laravel)…"
$DOCKER_COMPOSE up -d app

# Aguarda app ficar em 'running' (até 90s)
echo "Aguardando o app iniciar…"
for i in {1..30}; do
    state=$(docker inspect -f '{{.State.Status}}' "$APP_CONTAINER" 2>/dev/null || echo "")
    if [ "$state" = "running" ]; then
        echo "App em execução."
        break
    fi
    sleep 3
    if [ "$i" -eq 30 ]; then
        echo "Erro: app não iniciou a tempo." >&2
        $DOCKER_COMPOSE logs app || true
        exit 1
    fi
done

echo "[3/4] Executando migrações…"
./artisan-wrapper.sh migrate --force

echo "[4/4] Rodando seeders…"
./artisan-wrapper.sh db:seed --force

echo "Pronto! A aplicação está em http://localhost:8000"
