# Configuração do Projeto Financial Wallet

## Informações do Projeto

- **Laravel**: 12.39.0 (versão mais recente)
- **Banco de Dados**: MariaDB 10.11.14
- **PHP**: 8.2.29

## Configuração do Banco de Dados

O projeto está configurado para usar MariaDB. Para finalizar a configuração, você precisa criar o banco de dados.

### Opção 1: Usando o script automatizado

Execute o script fornecido:

```bash
./setup-database.sh
```

Este script irá:
- Criar o banco de dados `financial_wallet`
- Criar um usuário específico para o projeto
- Atualizar o arquivo `.env` com as credenciais

### Opção 2: Configuração manual

1. Acesse o MariaDB como root:

```bash
sudo mysql -u root
```

2. Execute os seguintes comandos SQL:

```sql
CREATE DATABASE IF NOT EXISTS financial_wallet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'financial_wallet_user'@'localhost' IDENTIFIED BY 'financial_wallet_password';
GRANT ALL PRIVILEGES ON financial_wallet.* TO 'financial_wallet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

3. Atualize o arquivo `.env` com as credenciais:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=financial_wallet
DB_USERNAME=financial_wallet_user
DB_PASSWORD=financial_wallet_password
```

### Executar Migrações

Após configurar o banco de dados, execute as migrações:

```bash
php artisan migrate
```

## Estrutura do Projeto

O projeto Laravel foi instalado com a estrutura padrão:

- `app/` - Código da aplicação
- `config/` - Arquivos de configuração
- `database/` - Migrações e seeders
- `routes/` - Rotas da aplicação
- `resources/` - Views, CSS, JS
- `public/` - Arquivos públicos
- `storage/` - Arquivos de armazenamento
- `tests/` - Testes automatizados

## Comandos Úteis

- Iniciar servidor de desenvolvimento: `php artisan serve`
- Executar migrações: `php artisan migrate`
- Criar nova migration: `php artisan make:migration nome_da_migration`
- Criar controller: `php artisan make:controller NomeController`
- Limpar cache: `php artisan cache:clear`
- Limpar configuração: `php artisan config:clear`

## Próximos Passos

1. Configure o banco de dados usando uma das opções acima
2. Execute as migrações: `php artisan migrate`
3. Inicie o servidor: `php artisan serve`
4. Acesse: `http://localhost:8000`

