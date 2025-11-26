#!/bin/bash
set -e

echo "🚀 Starting Carteira Financeira..."

# Aguardar banco de dados estar pronto
echo "⏳ Waiting for database..."
max_attempts=30
attempt=0

until php artisan db:show 2>/dev/null || [ $attempt -eq $max_attempts ]; do
    echo "Database is unavailable - attempt $((attempt+1))/$max_attempts"
    attempt=$((attempt+1))
    sleep 2
done

if [ $attempt -eq $max_attempts ]; then
    echo "❌ Failed to connect to database after $max_attempts attempts"
    exit 1
fi

echo "✅ Database is ready!"

# Executar migrações
echo "📦 Running migrations..."
php artisan migrate --force --no-interaction

# Executar seeders apenas se não for production
if [ "$APP_ENV" != "production" ]; then
    echo "🌱 Running seeders..."
    php artisan db:seed --force --no-interaction
else
    echo "⚠️  Skipping seeders (production mode)"
fi

# Cache de configuração
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico de storage
echo "🔗 Creating storage link..."
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

echo "✅ Application is ready!"
echo "🌐 Listening on port 8080"

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
