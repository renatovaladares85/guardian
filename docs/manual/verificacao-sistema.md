# 🔧 Guardian - Verificação e Ativação do Sistema

**Script para validar configuração e inicializar dados complexos**

## 📋 Checklist de Verificação

### **1. Verificar Estrutura do Projeto**
```bash
# Entrar no diretório do projeto
cd c:\dev\system-project

# Verificar se todas as dependências estão instaladas
composer install
npm install

# Verificar se o .env está configurado
php artisan config:clear
php artisan cache:clear
```

### **2. Verificar Banco de Dados**
```bash
# Testar conexão com PostgreSQL
php artisan tinker
# No tinker, executar:
DB::connection()->getPdo();
# Se retornar um objeto PDO, a conexão está OK
exit
```

### **3. Aplicar Migrações e Dados Complexos**
```bash
# Resetar banco e aplicar migrações
php artisan migrate:fresh --seed

# Verificar se os dados foram criados
php artisan tinker
# No tinker, executar:
User::count();        # Deve retornar 20
Project::count();     # Deve retornar 6  
Task::count();        # Deve retornar 25+
# exit
```

### **4. Verificar Redis (Session/Cache)**
```bash
# Testar conexão Redis
php artisan tinker
# No tinker, executar:
Redis::ping();        # Deve retornar "PONG"
Cache::put('teste', 'valor', 60);
Cache::get('teste');   # Deve retornar "valor"
# exit
```

### **5. Iniciar Serviços**
```bash
# Método 1: Docker (Recomendado)
docker-compose up -d

# Verificar se todos os containers estão rodando
docker-compose ps

# Método 2: Artisan (Desenvolvimento)
php artisan serve --host=0.0.0.0 --port=8000
# Em outro terminal:
npm run dev
```

## 🧪 **Testes de Funcionalidade**

### **1. Teste de Login e Segurança**
```bash
# Acessar: http://localhost:8000
# Credenciais de teste:

# Super Admin
Email: admin@guardian.local
Senha: guardian123

# Gerente Senior  
Email: ana.ferreira@guardian.local
Senha: guardian123

# Desenvolvedor Full Stack
Email: lucas.almeida@guardian.local
Senha: guardian123
```

### **2. Teste de Performance do Dashboard**
```bash
# Após login, verificar no Developer Tools:
# - Network tab: requests devem ser rápidos (< 500ms)
# - Console: não deve haver erros JavaScript
# - Database: verificar logs do Laravel para count de queries
```

### **3. Teste de Dados Complexos**
```bash
# No dashboard, verificar se aparecem:
# ✅ 6 projetos com orçamentos realistas
# ✅ 20+ tarefas distribuídas entre usuários  
# ✅ Progresso dos milestones
# ✅ Estatísticas de produtividade
```

## 🚨 **Solução de Problemas Comuns**

### **Erro: "Class 'Redis' not found"**
```bash
# Instalar extensão Redis para PHP
# Windows:
# 1. Baixar php_redis.dll compatível com sua versão do PHP
# 2. Copiar para pasta ext/ do PHP
# 3. Adicionar extension=redis no php.ini
# 4. Reiniciar servidor web

# Verificar
php -m | grep redis
```

### **Erro: "Connection refused PostgreSQL"**
```bash
# Verificar se PostgreSQL está rodando
# Windows:
services.msc
# Procurar por "postgresql" e iniciar o serviço

# Verificar credenciais no .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=guardian_db
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
```

### **Erro: "Permission denied artisan"**
```bash
# Windows PowerShell como administrador:
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser

# Verificar permissões de pasta
icacls "C:\dev\system-project" /grant Users:F
```

### **Sessões não persistem**
```bash
# Verificar configuração de sessão no .env
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Limpar cache de configuração
php artisan config:clear
php artisan session:table  # Se usando database sessions
```

## 📊 **Comandos de Diagnóstico**

### **1. Verificar Configuração Completa**
```bash
# Status geral do Laravel
php artisan about

# Verificar configurações específicas
php artisan config:show database
php artisan config:show session
php artisan config:show cache
```

### **2. Monitorar Performance**
```bash
# Habilitar query log
php artisan tinker
# No tinker:
DB::enableQueryLog();
# Fazer requests no sistema
DB::getQueryLog(); // Ver todas as queries executadas
```

### **3. Verificar Middlewares e Rotas**
```bash
# Listar todas as rotas
php artisan route:list

# Verificar middlewares registrados
php artisan route:list --columns=uri,middleware
```

## 🔍 **Indicadores de Sucesso**

### **✅ Sistema Funcionando Corretamente Quando:**

#### **Dashboard Admin:**
- Carrega em menos de 500ms
- Mostra estatísticas de 6 projetos
- Exibe gráficos de progresso atualizados
- Lista últimas atividades dos usuários

#### **Gestão de Projetos:**
- Gerentes veem apenas seus projetos
- Desenvolvedores veem apenas suas tarefas
- Filtros por status funcionam corretamente
- Atualizações de progresso são salvas

#### **Sistema de Segurança:**
- Login/logout funcionam sem erro
- Redirecionamentos baseados em role
- Verificações de permissão sem consultas extras ao DB
- Sessões persistem entre requests

#### **Performance:**
- Máximo 3-5 queries por página
- Cache de sessão funcionando
- Sem consultas N+1
- Tempo de resposta < 500ms

## 📈 **Estatísticas Esperadas**

### **Usuários por Departamento:**
- **Diretoria:** 1 CTO
- **Gestão:** 2 Gerentes
- **Desenvolvimento:** 11 profissionais (2 Tech Leads, 3 Seniors, 3 Plenos, 2 Juniors, 1 Estagiário)
- **QA:** 3 especialistas
- **Design:** 2 designers
- **DevOps:** 1 especialista
- **Produto:** 1 analista

### **Projetos por Status:**
- **Completos:** 1 projeto (Analytics Suite)
- **Em progresso:** 4 projetos (E-commerce, ERP, Banking, Cloud Migration)
- **Planejamento:** 1 projeto (IoT Platform)

### **Orçamentos por Projeto:**
- **Total:** R$ 2.280.000,00
- **Executado:** R$ 1.642.500,00 (72%)
- **Pendente:** R$ 637.500,00 (28%)

---

**🎯 Execute este checklist para garantir que o sistema está funcionando com dados complexos e performance otimizada!**
