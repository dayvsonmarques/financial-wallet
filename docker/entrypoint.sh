#!/bin/bash
set -e

echo "🚀 Starting Carteira Financeira..."

# Verificar se as variáveis MySQL estão definidas
if [ -z "$MYSQLHOST" ]; then
    echo "❌ ERROR: MySQL environment variables not found!"
    echo "Please ensure MySQL database is connected in Railway"
    exit 1
fi

echo "📊 Database Configuration:"
echo "  Host: $MYSQLHOST"
echo "  Port: $MYSQLPORT"
echo "  Database: $MYSQLDATABASE"
echo "  User: $MYSQLUSER"

# Aguardar banco de dados estar pronto (com timeout)
echo "⏳ Waiting for MySQL database..."
max_attempts=30
attempt=1

while [ $attempt -le $max_attempts ]; do
    if php -r "
        try {
            \$pdo = new PDO(
                'mysql:host=' . getenv('MYSQLHOST') . ';port=' . getenv('MYSQLPORT') . ';dbname=' . getenv('MYSQLDATABASE'),
                getenv('MYSQLUSER'),
                getenv('MYSQLPASSWORD')
            );
            exit(0);
        } catch (PDOException \$e) {
            exit(1);
        }
    "; then
        echo "✅ MySQL is ready!"
        break
    else
        echo "Database is unavailable - attempt $attempt/$max_attempts"
        sleep 2
        attempt=$((attempt + 1))
    fi
done

if [ $attempt -gt $max_attempts ]; then
    echo "❌ Failed to connect to MySQL after $max_attempts attempts"
    exit 1
fi

# Executar migrações
echo "📦 Running migrations..."
php artisan migrate --force --no-interaction

# Executar seeders apenas se APP_ENV não for production
if [ "$APP_ENV" != "production" ]; then
    echo "🌱 Running seeders..."
    php artisan db:seed --force --no-interaction || true
fi

# Cache de configuração
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico de storage
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

echo "✅ Application is ready!"
echo "🌐 Listening on port 8080"

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
