# Carteira Financeira

Sistema de carteira digital desenvolvido em Laravel que permite transferências, depósitos e gestão de saldo entre usuários.

## Como rodar

Você só precisa ter Docker e Docker Compose instalados. Depois é só executar:

```bash
./docker-init.sh
```

Esse script faz tudo: cria o `.env`, sobe os containers, roda as migrações e seeders. Quando terminar, acesse `http://localhost:8000`.

## Comandos que você pode precisar

```bash
# Iniciar/parar
docker compose up -d
docker compose down

# Corrigir permissões (se der erro)
./fix-permissions.sh

# Rodar comandos artisan
./artisan-wrapper.sh migrate
# ou
docker compose exec app php artisan [comando]

# Ver logs
docker compose logs -f app

# Entrar no container
docker compose exec app bash
```

## Credenciais

**Banco de dados:**
- Host: `localhost:3307`
- Usuário: `financial_wallet_user`
- Senha: `financial_wallet_password`
- Database: `financial_wallet`

**Usuário de teste:**
- Email: `test@example.com`
- Senha: `password`

## Testes

O projeto tem testes unitários e de integração. Para rodar:

```bash
# Todos os testes
docker compose exec app php artisan test

# Só testes unitários
docker compose exec app php artisan test --testsuite=Unit

# Só testes de integração
docker compose exec app php artisan test --testsuite=Feature
```

Atualmente temos **55 testes** cobrindo:
- Serviços de transação (transferência, depósito, estorno)
- Modelos (User, Transaction)
- Endpoints da API (autenticação, transações, wallet)
- Regras de negócio e validações

## Tecnologias

- Laravel 12
- PHP 8.2
- MariaDB 10.11
- Docker
