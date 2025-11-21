#!/bin/bash

# Script para configurar o banco de dados MariaDB para o projeto Financial Wallet

echo "Configurando banco de dados MariaDB..."

# Criar banco de dados
sudo mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS financial_wallet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'financial_wallet_user'@'localhost' IDENTIFIED BY 'financial_wallet_password';
GRANT ALL PRIVILEGES ON financial_wallet.* TO 'financial_wallet_user'@'localhost';
FLUSH PRIVILEGES;
EOF

if [ $? -eq 0 ]; then
    echo "Banco de dados criado com sucesso!"
    echo ""
    echo "Atualizando arquivo .env..."
    
    # Atualizar .env com o novo usuário
    cd /var/www/html/financial-wallet
    sed -i 's/^DB_USERNAME=root/DB_USERNAME=financial_wallet_user/' .env
    sed -i 's/^DB_PASSWORD=$/DB_PASSWORD=financial_wallet_password/' .env
    
    echo "Configuração concluída!"
    echo ""
    echo "Você pode executar as migrações com: php artisan migrate"
else
    echo "Erro ao criar banco de dados. Verifique as permissões do MySQL."
fi

