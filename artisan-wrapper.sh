#!/bin/bash

# Wrapper para executar comandos artisan como www-data com correção automática de permissões

# Detectar comando docker compose
if command -v docker &> /dev/null && docker compose version &> /dev/null; then
    DOCKER_COMPOSE="docker compose"
elif command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
else
    echo "Docker Compose not found"
    exit 1
fi

# Executar comando artisan como www-data
$DOCKER_COMPOSE exec -u www-data app bash -c "umask 0002 && php artisan $*"

# Corrigir permissões após o comando (caso algum arquivo tenha sido criado como root)
$DOCKER_COMPOSE exec -T app bash -c "
    # Corrigir arquivos criados como root em todo o projeto (exceto vendor, node_modules, .git)
    find /var/www/html -type f -user root ! -path '*/vendor/*' ! -path '*/node_modules/*' ! -path '*/.git/*' ! -path '*/storage/framework/cache/*' ! -path '*/storage/framework/sessions/*' ! -path '*/storage/framework/views/*' -exec chown www-data:www-data {} \; 2>/dev/null || true
    find /var/www/html -type d -user root ! -path '*/vendor/*' ! -path '*/node_modules/*' ! -path '*/.git/*' ! -path '*/storage/framework/cache/*' ! -path '*/storage/framework/sessions/*' ! -path '*/storage/framework/views/*' -exec chown www-data:www-data {} \; 2>/dev/null || true
    
    # Garantir permissões corretas em storage e bootstrap/cache
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    find /var/www/html/storage /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
    find /var/www/html/storage /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
" 2>/dev/null || true

