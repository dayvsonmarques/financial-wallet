# 🚂 Deploy no Railway.app

Guia completo para fazer deploy da Carteira Financeira no Railway.

## 📋 Pré-requisitos

- Conta no [Railway.app](https://railway.app)
- Repositório no GitHub com código atualizado
- Git configurado localmente

## 🚀 Passo a Passo

### 1. Preparar o Repositório

```bash
# Certifique-se de que todas as mudanças estão commitadas
git status

# Faça push para o GitHub
git push origin main
```

### 2. Criar Projeto no Railway

1. **Login:**
   - Acesse [railway.app](https://railway.app)
   - Clique em "Login with GitHub"
   - Autorize o Railway a acessar seus repositórios

2. **Novo Projeto:**
   - Clique em "New Project"
   - Selecione "Deploy from GitHub repo"
   - Escolha o repositório `financial-wallet`
   - Railway detectará automaticamente o `railway.json` e `Dockerfile.production`

### 3. Adicionar Banco de Dados MySQL

1. No dashboard do projeto, clique em **"+ New"**
2. Selecione **"Database"** → **"Add MySQL"**
3. Railway criará automaticamente o banco de dados

4. **⚠️ IMPORTANTE - Conectar MySQL ao serviço da aplicação:**
   - Clique no **serviço da aplicação** (não no MySQL)
   - Vá em **"Variables"**
   - Procure por **"Reference Variables"** ou **"Service Variables"**
   - Clique em **"+ New Variable"** → **"Add Reference"**
   - Selecione o serviço **MySQL**
   - Selecione TODAS estas variáveis:
     - `MYSQLHOST`
     - `MYSQLPORT`
     - `MYSQLDATABASE`
     - `MYSQLUSER`
     - `MYSQLPASSWORD`

**Como saber se funcionou:**
- As variáveis aparecerão na lista com ícone de "link" 🔗
- Valores começam com `${{MySQL.MYSQL...}}`

**Se não aparecerem automaticamente:**
1. Clique no MySQL no dashboard
2. Vá em **"Connect"** ou **"Variables"**
3. Copie os valores manualmente para o serviço da app

### 4. Configurar Variáveis de Ambiente

**⚠️ ANTES DE COMEÇAR:** Confirme que as variáveis MySQL foram conectadas (passo 3)

1. Clique no serviço da aplicação (não no banco)
2. Vá em **"Variables"**
3. Verifique se estas variáveis já aparecem (com ícone 🔗):
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
   
   **Se NÃO aparecerem:** Volte ao passo 3 e adicione as referências!

4. Clique em **"RAW Editor"**
5. Cole as seguintes variáveis:

```env
APP_NAME=Carteira Financeira
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

5. **⚠️ OBRIGATÓRIO - Gerar APP_KEY:**
   ```bash
   # Execute localmente:
   php artisan key:generate --show
   # OU no Docker:
   docker compose exec app php artisan key:generate --show
   ```
   
   Copie o resultado (exemplo: `base64:xxxxxxxxxxx`) e adicione como variável:
   ```
   APP_KEY=base64:xxxxxxxxxxx
   ```
   
   **IMPORTANTE:** Sem o APP_KEY a aplicação **NÃO FUNCIONARÁ**!

6. **✅ Checklist final de variáveis:**
   
   Confirme que estas variáveis estão presentes:
   - ✅ `APP_KEY` (gerado por você)
   - ✅ `APP_ENV=production`
   - ✅ `MYSQLHOST` (referência ao MySQL) 🔗
   - ✅ `MYSQLPORT` (referência ao MySQL) 🔗
   - ✅ `MYSQLDATABASE` (referência ao MySQL) 🔗
   - ✅ `MYSQLUSER` (referência ao MySQL) 🔗
   - ✅ `MYSQLPASSWORD` (referência ao MySQL) 🔗

### 5. Configurar Domínio e APP_URL

**Railway gera o domínio automaticamente no primeiro deploy!**

1. **Após o deploy inicial:**
   - Vá em **"Settings"** → **"Networking"**
   - Railway já terá gerado uma URL pública
   - Exemplo: `financial-wallet-production-abc123.up.railway.app`

2. **⚠️ OBRIGATÓRIO - Configurar APP_URL:**
   
   **Opção 1 (Recomendado):** Usar variável automática do Railway
   ```env
   APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
   ```
   ✅ Atualiza automaticamente se o domínio mudar
   
   **Opção 2:** Copiar manualmente
   ```env
   APP_URL=https://sua-url-gerada.up.railway.app
   ```
   ⚠️ Precisa atualizar se regenerar o domínio
   
3. **Adicionar a variável:**
   - Volte em **"Variables"**
   - Cole a variável `APP_URL` escolhida acima
   - **Importante:** Use HTTPS (não HTTP)

### 6. Aguardar Deploy

1. Railway iniciará o build automaticamente
2. Acompanhe o progresso em **"Deployments"**
3. Verifique os logs para garantir que tudo ocorreu bem

**Logs esperados:**
```
🚀 Starting Carteira Financeira...
📊 Database Configuration:
  Host: mysql.railway.internal
  Port: 3306
  Database: railway
✅ MySQL is ready!
📦 Running migrations...
⚡ Caching configuration...
✅ Application is ready!
🌐 Listening on port 8080
```

**Se o healthcheck falhar:**
- Verifique se **APP_KEY** está configurado
- Verifique se variáveis **MYSQL** existem
- Veja logs completos em **"Deployments"** → **"View Logs"**
🚀 Starting Carteira Financeira...
⏳ Waiting for database...
✅ Database is ready!
📦 Running migrations...
🌱 Running seeders...
⚡ Caching configuration...
🔗 Creating storage link...
✅ Application is ready!
🌐 Listening on port 8080
```

### 7. Testar a Aplicação

1. **Acessar URL:**
   - Clique na URL gerada pelo Railway
   - Você deve ver a página de login

2. **Health Check:**
   - Acesse: `https://sua-url.railway.app/api/health`
   - Deve retornar: `{"status":"healthy"}`

3. **Login com usuários de teste:**
   - Email: `admin@exemplo.com`
   - Senha: `password`
   - Saldo: R$ 10.000,00

## 🔧 Configurações Avançadas

### Desabilitar Seeders em Produção

Por padrão, os seeders são executados apenas quando `APP_ENV != production`.

Para produção verdadeira:
```env
APP_ENV=production  # Seeders não serão executados
```

### Criar Usuário Admin Manualmente

Se não quiser usar seeders:

1. No Railway, vá em **"Settings"** do serviço
2. Execute um **"One-off Command"**:
   ```bash
   php artisan tinker
   ```

3. Cole o código:
   ```php
   \App\Models\User::create([
       'name' => 'Admin',
       'email' => 'seu-email@exemplo.com',
       'password' => bcrypt('sua-senha-forte'),
       'balance' => 10000.00
   ]);
   ```

### Executar Migrações Manualmente

```bash
# No Railway CLI ou One-off Command:
php artisan migrate --force
```

### Ver Logs em Tempo Real

1. No Railway, vá em **"Logs"**
2. Filtre por tipo: Application, Build, Deploy
3. Use a busca para encontrar erros específicos

## 💰 Custos Estimados

Railway oferece:
- **$5/mês de crédito grátis** no plano trial
- **$5/mês** para o plano Hobby (pós-trial)

**Uso estimado:**
- Web Service (512MB RAM): ~$2-3/mês
- MySQL Database (256MB): ~$2-3/mês
- **Total:** ~$4-6/mês ✅ (dentro do crédito grátis!)

## 🔒 Segurança em Produção

✅ **Configurações já aplicadas:**
- `APP_DEBUG=false` - Oculta erros sensíveis
- `APP_ENV=production` - Modo otimizado
- SSL/HTTPS automático - Railway fornece
- Rate limiting configurado
- CSRF protection ativo
- Senhas com bcrypt

## ⚠️ Troubleshooting

### Erro: "No application encryption key"
```bash
# Gere uma nova key:
php artisan key:generate --show

# Adicione em Variables no Railway:
APP_KEY=base64:resultado-aqui
```

### Erro: "Database file at path [database.sqlite] does not exist"

**Causa:** Aplicação tentando usar SQLite ao invés de MySQL.

**Solução:**
1. Verifique se o serviço MySQL está conectado ao projeto
2. No Railway Dashboard, vá em **"Variables"**
3. Confirme que estas variáveis existem (injetadas automaticamente):
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
4. Se não existirem, adicione o MySQL novamente: **"+ New"** → **"Database"** → **"Add MySQL"**
5. Redesploy a aplicação: **"Deployments"** → **"Redeploy"**

**Verificar logs:**
```
✅ MySQL ready! Host: mysql.railway.internal
✅ Connected to MySQL successfully
```

### Erro: "Connection refused" ou "Database timeout"
- Verifique se o serviço MySQL está rodando
- As variáveis `MYSQL*` são injetadas automaticamente
- Aguarde 1-2 minutos após criar o banco
- Verifique logs do MySQL: clique no serviço MySQL → **"Logs"**

### Erro: "Permission denied" nos logs
- Já configurado no `Dockerfile.production`
- Permissões aplicadas durante build

### Build muito lento
- Normal na primeira vez (instala todas as dependências)
- Próximos builds usam cache e são mais rápidos

### Aplicação não responde
1. Verifique logs: **"Deployments"** → último deploy → **"View Logs"**
2. Verifique se a porta 8080 está exposta
3. Verifique Health Check: `/api/health`

## 📊 Monitoramento

Railway oferece:
- ✅ **Logs em tempo real**
- ✅ **Métricas de CPU, RAM, Network**
- ✅ **Alertas personalizáveis**
- ✅ **Deploy history completo**

Acesse em: **Dashboard** → **Seu Serviço** → **Metrics**

## 🔄 Deploy Automático

Railway faz deploy automático quando você faz push para a branch principal:

```bash
git add .
git commit -m "feat: nova funcionalidade"
git push origin main
# Railway detecta e faz deploy automaticamente! 🚀
```

## 📚 Recursos Adicionais

- [Documentação Railway](https://docs.railway.app/)
- [Railway Discord](https://discord.gg/railway)
- [Status do Railway](https://status.railway.app/)

## ✅ Checklist Final

- [ ] Repositório no GitHub atualizado
- [ ] Projeto criado no Railway
- [ ] MySQL adicionado
- [ ] Variáveis de ambiente configuradas
- [ ] APP_KEY gerado e adicionado
- [ ] APP_URL configurado com domínio Railway
- [ ] Deploy concluído com sucesso
- [ ] Health check retornando "healthy"
- [ ] Login funcionando
- [ ] Transações testadas

---

**🎉 Parabéns! Sua aplicação está no ar!**

Para suporte, abra uma issue no repositório ou contate o time do Railway.
