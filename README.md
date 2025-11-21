# Financial Wallet

Aplicação Laravel 12 com MariaDB configurada para rodar com Docker.

## Pré-requisitos

- Docker
- Docker Compose

## Instalação e Execução

1. **Clone o repositório e entre no diretório:**
   ```bash
   cd financial-wallet
   ```

2. **Execute o script de inicialização:**
   ```bash
   ./docker-init.sh
   ```

   Este script irá:
   - Criar o arquivo `.env` se não existir
   - Iniciar os containers (Laravel + MariaDB)
   - Gerar a chave da aplicação
   - Executar as migrações
   - Executar os seeders

3. **Acesse a aplicação:**
   ```
   http://localhost:8000
   ```

## Comandos Úteis

### Iniciar/Parar containers
```bash
docker compose up -d          # Iniciar
docker compose down           # Parar
```

### Executar comandos Artisan
```bash
docker compose exec app php artisan [comando]
```

### Ver logs
```bash
docker compose logs -f app
```

### Acessar o container
```bash
docker compose exec app bash
```

### Acessar o MariaDB
```bash
docker compose exec mariadb mysql -u financial_wallet_user -pfinancial_wallet_password financial_wallet
```

## Configuração

- **Aplicação**: http://localhost:8000
- **MariaDB**: localhost:3307
- **Credenciais DB**:
  - Usuário: `financial_wallet_user`
  - Senha: `financial_wallet_password`
  - Database: `financial_wallet`

## Tecnologias

- Laravel 12.39.0
- PHP 8.2
- MariaDB 10.11
- Docker & Docker Compose
