# 🚀 Guardian - Tutorial Modo Produção

**Configuração para uso em ambiente real de produção**

## 📋 Visão Geral

O **Modo Produção** configura o Guardian com foco em **segurança, performance e estabilidade**, criando um sistema limpo e otimizado para uso empresarial real.

## 🎯 O que será configurado?

- ✅ **Sistema limpo** sem dados de exemplo
- ✅ **Usuário administrador** personalizado
- ✅ **Configurações de segurança** ativadas
- ✅ **Cache otimizado** para performance
- ✅ **Auditoria completa** habilitada
- ✅ **Logs de produção** configurados

## 🔒 Pré-requisitos de Segurança

Antes de configurar o modo produção, **certifique-se** de que:

### **Variáveis de Ambiente (.env)**
```env
# OBRIGATÓRIO: Ambiente de produção
APP_ENV=production
APP_DEBUG=false

# OBRIGATÓRIO: Chave de aplicação segura
APP_KEY=base64:SuaChaveSeguraAqui...

# OBRIGATÓRIO: Senha forte do banco
DB_PASSWORD=SenhaSuperSegura123!@#

# RECOMENDADO: URL da aplicação
APP_URL=https://guardian.suaempresa.com

# RECOMENDADO: Configurações de email
MAIL_MAILER=smtp
MAIL_HOST=smtp.suaempresa.com
MAIL_PORT=587
MAIL_USERNAME=guardian@suaempresa.com
MAIL_PASSWORD=SenhaEmailSegura
```

### **Validações Automáticas**
O assistente verificará:
- ❌ `APP_ENV` não pode ser "local"
- ❌ `APP_KEY` deve estar definida
- ❌ `DB_PASSWORD` não pode ser "password" ou vazia
- ❌ Configurações básicas de segurança

## 🚀 Como Configurar

### Opção 1: Via Interface Web (Recomendado)

1. **Prepare o ambiente:**
   ```bash
   # Edite o arquivo .env com configurações de produção
   notepad .env
   ```

2. **Inicie o sistema:**
   ```bash
   .\start-mvp.bat
   ```

3. **Acesse o assistente:** `http://localhost:8000` (ou sua URL)

4. **Selecione Modo Produção:** Clique no cartão "🚀 Modo Produção"

5. **Configure o administrador:**
   - **Nome:** Nome do administrador principal
   - **Email:** Email corporativo válido
   - **Senha:** Mínimo 8 caracteres (use senha forte!)

6. **Confirme:** Clique em "Configurar Guardian"

7. **Aguarde:** Sistema será configurado e otimizado

### Opção 2: Via Linha de Comando

```bash
# Configuração interativa
docker-compose exec guardian_app php artisan guardian:setup --mode=production

# Com parâmetros (avançado)
docker-compose exec guardian_app php artisan guardian:setup-production \
  --name="Admin Principal" \
  --email="admin@empresa.com" \
  --password="SenhaSegura123!"
```

## 👤 Criação do Usuário Administrador

### **Informações Obrigatórias:**
- **Nome Completo:** Ex: "Maria Silva Santos"
- **Email Corporativo:** Ex: "admin@suaempresa.com"
- **Senha Forte:** Mínimo 8 caracteres, use:
  - Letras maiúsculas e minúsculas
  - Números
  - Símbolos especiais
  - Ex: `Admin@2025!Seg`

### **Permissões do Admin:**
- ✅ **Criar/editar/excluir** todos os projetos
- ✅ **Gerenciar usuários** e permissões
- ✅ **Acessar configurações** do sistema
- ✅ **Visualizar auditoria** completa
- ✅ **Configurar integrações** externas

## ⚙️ Configurações Aplicadas

### **1. Otimizações de Performance**
```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimização do autoloader
composer install --optimize-autoloader --no-dev
```

### **2. Configurações de Segurança**
```json
{
  "mode": "production",
  "features": {
    "demo_data": false,
    "security_enhanced": true,
    "audit_complete": true,
    "ssl_enforced": true,
    "session_secure": true
  },
  "setup_date": "2025-07-11T10:30:00Z",
  "version": "1.0-MVP"
}
```

### **3. Headers de Segurança (Apache)**
```apache
# Já configurado no container
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

## 🔐 Recursos de Segurança Ativados

### **Autenticação Robusta**
- ✅ **Hash seguro** de senhas (bcrypt)
- ✅ **Validação de email** obrigatória
- ✅ **Tentativas de login** limitadas
- ✅ **Sessões seguras** com HTTPS
- ✅ **Logout automático** por inatividade

### **Controle de Acesso**
- ✅ **RBAC** (Role-Based Access Control)
- ✅ **Permissões granulares** por projeto
- ✅ **Auditoria** de todas as ações
- ✅ **Logs de acesso** detalhados

### **Proteção de Dados**
- ✅ **Criptografia** de dados sensíveis
- ✅ **Backup automático** configurável
- ✅ **Validação** de entrada em todos os formulários
- ✅ **Proteção CSRF** ativada

## 📊 Monitoramento e Logs

### **Logs Disponíveis:**
```bash
# Logs da aplicação
docker-compose logs guardian_app

# Logs específicos do Laravel
docker-compose exec guardian_app tail -f storage/logs/laravel.log

# Logs de auditoria
docker-compose exec guardian_app php artisan log:audit
```

### **Métricas Monitoradas:**
- **Performance:** Tempo de resposta, uso de memória
- **Segurança:** Tentativas de login, ações de usuários
- **Uso:** Projetos criados, tarefas executadas
- **Erros:** Exceções, falhas de sistema

## 🗂️ Estrutura Inicial Criada

### **Tabelas do Banco:**
- `users` - Apenas o administrador criado
- `projects` - Vazia, pronta para novos projetos
- `tasks` - Vazia, pronta para novas tarefas
- `audit_logs` - Ativa, registrando todas as ações
- `system_settings` - Configurações de produção

### **Configurações Padrão:**
```json
{
  "company_name": "Sua Empresa",
  "timezone": "America/Sao_Paulo",
  "language": "pt-BR",
  "date_format": "d/m/Y",
  "currency": "BRL",
  "max_file_size": "10MB",
  "session_lifetime": "480"
}
```

## 🎯 Primeiros Passos Pós-Configuração

### **1. Login Inicial (Crítico)**
```
1. Acesse: http://localhost:8000 (ou sua URL)
2. Faça login com as credenciais criadas
3. Verifique se o dashboard carrega corretamente
4. Confirme que você está no ambiente de produção
```

### **2. Configuração da Empresa (Recomendado)**
```
1. Vá para: Configurações > Empresa
2. Configure: Nome da empresa, logo, informações
3. Defina: Timezone, formato de data, moeda
4. Salve as configurações
```

### **3. Criação da Estrutura Inicial**
```
1. Criar departamentos da empresa
2. Cadastrar primeiros usuários
3. Definir estrutura de projetos
4. Configurar fluxos de trabalho
```

## 👥 Gerenciamento de Usuários

### **Adicionando Novos Usuários:**
1. **Menu:** Usuários > Novo Usuário
2. **Dados obrigatórios:**
   - Nome completo
   - Email corporativo
   - Função (role)
   - Departamento
   - Cargo
3. **Configurações:**
   - Status ativo/inativo
   - Permissões específicas
   - Projetos de acesso

### **Funções Disponíveis:**
- **Super Admin:** Acesso total (apenas você inicialmente)
- **Admin:** Gerenciamento geral sem configurações críticas
- **Gerente de Projetos:** Criar e gerenciar projetos
- **Líder de Equipe:** Gerenciar equipes específicas
- **Membro da Equipe:** Executar tarefas atribuídas
- **Observador:** Apenas visualização

## 🔧 Configurações Avançadas

### **1. Configuração de Email (SMTP)**
```env
# .env - Configurações de produção
MAIL_MAILER=smtp
MAIL_HOST=smtp.empresa.com
MAIL_PORT=587
MAIL_USERNAME=guardian@empresa.com
MAIL_PASSWORD=SenhaSeguraEmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=guardian@empresa.com
MAIL_FROM_NAME="Sistema Guardian"
```

### **2. Configuração de SSL/HTTPS**
```bash
# Para Apache (produção)
# Edite: docker/apache/apache.conf
<VirtualHost *:443>
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
</VirtualHost>
```

### **3. Backup Automático**
```bash
# Criar script de backup
#!/bin/bash
# backup-guardian.sh

# Backup do banco
docker-compose exec guardian_db pg_dump -U falcon_user guardian_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup dos arquivos
tar -czf files_backup_$(date +%Y%m%d_%H%M%S).tar.gz ./storage/app/public
```

## ⚠️ Checklist de Segurança

### **Obrigatório antes de usar:**
- [ ] **Senha forte** para administrador
- [ ] **Senhas de banco** alteradas
- [ ] **APP_KEY** gerada e segura
- [ ] **APP_DEBUG=false** configurado
- [ ] **HTTPS** configurado (se aplicável)
- [ ] **Firewall** configurado
- [ ] **Backup** configurado

### **Recomendado:**
- [ ] **2FA** habilitado para admin
- [ ] **Monitoramento** configurado
- [ ] **Logs** sendo coletados
- [ ] **Atualizações** programadas
- [ ] **Política de senhas** definida

## 🚨 Solução de Problemas

### **Erro: "Configuração inválida"**
```bash
# Verificar variáveis de ambiente
docker-compose exec guardian_app php artisan config:show

# Limpar cache
docker-compose exec guardian_app php artisan config:clear
```

### **Erro: "Não é possível conectar ao banco"**
```bash
# Verificar conexão
docker-compose exec guardian_app php artisan migrate:status

# Reiniciar banco
docker-compose restart guardian_db
```

### **Erro: "Página não carrega"**
```bash
# Verificar logs
docker-compose logs guardian_app

# Verificar permissões
docker-compose exec guardian_app chown -R www-data:www-data storage
```

## 📈 Monitoramento Contínuo

### **Métricas Importantes:**
- **Uptime:** Disponibilidade do sistema
- **Performance:** Tempo de resposta < 2s
- **Segurança:** Tentativas de acesso inválidas
- **Uso:** Número de usuários ativos

### **Alertas Configurados:**
- ⚠️ **Alto uso de CPU/memória**
- ⚠️ **Múltiplas tentativas de login falhadas**
- ⚠️ **Erros de aplicação**
- ⚠️ **Espaço em disco baixo**

## 📞 Próximos Passos

1. **📚 Treinamento:** Capacite a equipe no uso do sistema
2. **📊 Processos:** Defina fluxos de trabalho da empresa
3. **🔧 Integrações:** Configure integrações com outras ferramentas
4. **📈 Otimização:** Monitore e otimize conforme o uso
5. **🔄 Backup:** Implemente rotina de backup regular

---

**⚠️ Importante:** O modo produção é para uso real. Certifique-se de ter configurado adequadamente a segurança e backup antes de usar com dados importantes!
