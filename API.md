# Financial Wallet API

## Autenticação

Todas as rotas protegidas requerem o header:
```
Authorization: Bearer {token}
```

## Endpoints

### Autenticação

#### POST `/api/auth/register`
Registra um novo usuário.

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### POST `/api/auth/login`
Autentica um usuário.

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "user": {...},
  "token": "1|..."
}
```

#### POST `/api/auth/logout`
Desloga o usuário atual.

#### GET `/api/auth/me`
Retorna informações do usuário autenticado.

### Carteira

#### GET `/api/wallet/balance`
Retorna o saldo atual do usuário.

**Response:**
```json
{
  "balance": "1000.00",
  "formatted_balance": "1000.00"
}
```

### Transações

#### POST `/api/transactions/transfer`
Realiza transferência entre usuários.

**Body:**
```json
{
  "to_user_id": 2,
  "amount": 100.50,
  "description": "Payment for services"
}
```

#### POST `/api/transactions/deposit`
Realiza depósito na própria conta.

**Body:**
```json
{
  "amount": 500.00,
  "description": "Initial deposit"
}
```

#### POST `/api/transactions/{id}/reverse`
Reverte uma transação.

**Body:**
```json
{
  "description": "Refund requested"
}
```

#### GET `/api/transactions`
Lista todas as transações do usuário (paginated).

#### GET `/api/transactions/{id}`
Retorna detalhes de uma transação específica.

## Regras de Negócio

1. **Transferência:**
   - Valida saldo suficiente antes de transferir
   - Não permite transferência para si mesmo
   - Usa transações de banco para garantir atomicidade

2. **Depósito:**
   - Permite depósito mesmo com saldo negativo
   - O valor é somado ao saldo atual

3. **Reversão:**
   - Apenas transações completas podem ser revertidas
   - Apenas o remetente ou destinatário podem reverter
   - Cria nova transação de reversão

## Segurança

- Autenticação via Laravel Sanctum
- Validação de entrada em todos os endpoints
- Lock de registros durante transações
- Transações de banco para consistência
- Tratamento de erros customizado

