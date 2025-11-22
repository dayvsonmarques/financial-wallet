#!/bin/bash

# Script para corrigir permissões do projeto (container e host)

echo "Fixing permissions..."

# Detectar comando docker compose
if command -v docker &> /dev/null && docker compose version &> /dev/null; then
    DOCKER_COMPOSE="docker compose"
elif command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
else
    echo "Docker Compose not found"
    exit 1
fi

# Corrigir permissões no container (incluindo arquivos de root)
echo "Fixing permissions in container..."
$DOCKER_COMPOSE exec -T app bash -c "
    # Encontrar e corrigir arquivos/diretórios criados como root
    find /var/www/html/storage /var/www/html/bootstrap/cache -type f -user root -exec chown www-data:www-data {} \; 2>/dev/null || true
    find /var/www/html/storage /var/www/html/bootstrap/cache -type d -user root -exec chown www-data:www-data {} \; 2>/dev/null || true
    
    # Garantir que todos os arquivos e diretórios tenham as permissões corretas
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    
    # Corrigir permissões de arquivos individuais
    find /var/www/html/storage /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
    find /var/www/html/storage /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
"

# Corrigir permissões no host (para arquivos montados)
echo "Fixing permissions on host..."
if [ -d "storage" ]; then
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
    # Tentar corrigir ownership se possível (pode falhar se não for root)
    chown -R $(id -u):$(id -g) storage bootstrap/cache 2>/dev/null || true
fi

echo "✅ Permissions fixed!"

