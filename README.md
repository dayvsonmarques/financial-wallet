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

# Corrigir permissões (execute sempre que houver problemas de permissão)
./fix-permissions.sh

# Rodar comandos artisan (SEMPRE use este wrapper para evitar problemas de permissão)
./artisan-wrapper.sh migrate
./artisan-wrapper.sh make:controller NomeController
# ou manualmente (não recomendado):
docker compose exec -u www-data app php artisan [comando]

# Ver logs
docker compose logs -f app

# Entrar no container
docker compose exec app bash
```

**⚠️ Importante sobre permissões:**
- Sempre use `./artisan-wrapper.sh` para comandos artisan
- Se houver erros de permissão, execute `./fix-permissions.sh`
- Se o script pedir senha sudo, é normal - ele precisa corrigir ownership de arquivos
- Arquivos criados manualmente podem precisar de correção de permissões
- **Solução rápida**: Se persistir, execute `sudo ./fix-permissions.sh` uma vez

## Credenciais

**Banco de dados:**
- Host: `localhost:3307`
- Usuário: `financial_wallet_user`
- Senha: `financial_wallet_password`
- Database: `financial_wallet`

**Usuário de teste (criado automaticamente):**
- Email: `test@example.com`
- Senha: `password`
- Saldo inicial: `R$ 0,00`

Esse usuário é criado automaticamente quando você roda o `docker-init.sh`. Você pode usar essas credenciais para fazer login na aplicação e testar as funcionalidades.

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

## Observabilidade

O projeto tem logging estruturado e monitoramento implementado:

**Logs de transações:**
- Todas as transações são logadas em `storage/logs/transactions.log`
- Inclui informações sobre início, conclusão e falhas
- Mantém histórico por 30 dias

**Monitoramento de requisições:**
- Middleware registra todas as requisições com métricas de performance
- Logs incluem: tempo de resposta, uso de memória, status HTTP

**Health checks:**
- `/up` - Health check básico do Laravel
- `/api/health` - Health check detalhado (database, cache, tempo de resposta)

**Ver logs:**
```bash
# Logs gerais
docker compose exec app tail -f storage/logs/laravel.log

# Logs de transações
docker compose exec app tail -f storage/logs/transactions.log
```

## Tecnologias

- Laravel 12
- PHP 8.2
- MariaDB 10.11
- Docker
