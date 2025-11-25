#!/bin/bash

# Script para corrigir permissões do projeto (container e host)
# Nota: Pode precisar de sudo para corrigir ownership de arquivos www-data no host

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

# Corrigir permissões no container
echo "Fixing permissions in container..."
$DOCKER_COMPOSE exec -T app bash -c "
    # Encontrar e corrigir TODOS os arquivos/diretórios criados como root (exceto vendor, node_modules, .git)
    find /var/www/html -type f -user root ! -path '*/vendor/*' ! -path '*/node_modules/*' ! -path '*/.git/*' ! -path '*/storage/framework/cache/*' ! -path '*/storage/framework/sessions/*' ! -path '*/storage/framework/views/*' -exec chown www-data:www-data {} \; 2>/dev/null || true
    find /var/www/html -type d -user root ! -path '*/vendor/*' ! -path '*/node_modules/*' ! -path '*/.git/*' ! -path '*/storage/framework/cache/*' ! -path '*/storage/framework/sessions/*' ! -path '*/storage/framework/views/*' -exec chown www-data:www-data {} \; 2>/dev/null || true
    
    # Garantir permissões corretas em storage e bootstrap/cache
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    
    # Corrigir permissões de arquivos individuais
    find /var/www/html/storage /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \; 2>/dev/null || true
    find /var/www/html/storage /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; 2>/dev/null || true
    
    # Corrigir arquivos de código criados como root (app, config, routes, etc)
    find /var/www/html/app /var/www/html/config /var/www/html/routes /var/www/html/database -type f -user root -exec chown www-data:www-data {} \; 2>/dev/null || true
    find /var/www/html/app /var/www/html/config /var/www/html/routes /var/www/html/database -type d -user root -exec chown www-data:www-data {} \; 2>/dev/null || true
    
    # Corrigir arquivos de teste e cache do PHPUnit
    [ -f /var/www/html/.phpunit.result.cache ] && chown www-data:www-data /var/www/html/.phpunit.result.cache 2>/dev/null || true
"

# Corrigir permissões no host (para arquivos montados)
echo "Fixing permissions on host..."
USER_ID=$(id -u)
GROUP_ID=$(id -g)

# Função para tentar chown, usando sudo se necessário
chown_safe() {
    local target="$1"
    # Tentar sem sudo primeiro (se já for do usuário correto)
    if chown ${USER_ID}:${GROUP_ID} "$target" 2>/dev/null; then
        return 0
    # Tentar com sudo (pode pedir senha)
    elif command -v sudo >/dev/null 2>&1 && sudo chown ${USER_ID}:${GROUP_ID} "$target" 2>/dev/null; then
        return 0
    else
        # Se não conseguir mudar ownership, pelo menos garantir permissões de escrita
        chmod 664 "$target" 2>/dev/null || chmod 666 "$target" 2>/dev/null || true
        return 1
    fi
}

# Corrigir ownership de TODOS os arquivos que pertencem a www-data ou root no host
# (www-data no container não existe no host, então precisamos mudar para o usuário do host)
if [ -d "storage" ]; then
    # Corrigir arquivos que pertencem a www-data (do container) ou root
    find storage bootstrap/cache -type f \( -user www-data -o -user root \) 2>/dev/null | while read file; do
        chown_safe "$file" || true
    done
    find storage bootstrap/cache -type d \( -user www-data -o -user root \) 2>/dev/null | while read dir; do
        chown_safe "$dir" || true
    done
    
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true
fi

# Corrigir arquivos de código no host (app, config, routes, database, etc)
if [ -d "app" ]; then
    # Corrigir arquivos que pertencem a www-data (do container) ou root
    find app config routes database tests -type f \( -user www-data -o -user root \) 2>/dev/null | while read file; do
        chown_safe "$file" || true
    done
    find app config routes database tests -type d \( -user www-data -o -user root \) 2>/dev/null | while read dir; do
        chown_safe "$dir" || true
    done
    
    # Corrigir arquivos específicos que podem ter sido criados como root/www-data
    [ -f .phpunit.result.cache ] && chown_safe .phpunit.result.cache || true
fi

echo "✅ Permissions fixed!"

