# ⚙️ Guardian - Tutorial Modo Personalizado

**Configuração avançada para necessidades específicas**

## 📋 Visão Geral

O **Modo Personalizado** oferece **controle total** sobre a configuração do Guardian, permitindo escolher exatamente quais componentes ativar, que dados inserir e como otimizar o sistema para suas necessidades específicas.

## 🎯 O que você pode personalizar?

- ✅ **Dados iniciais:** Escolher o que criar
- ✅ **Configurações:** Ajustar cada parâmetro
- ✅ **Recursos:** Habilitar/desabilitar funcionalidades
- ✅ **Performance:** Otimizar para seu ambiente
- ✅ **Segurança:** Configurar níveis específicos
- ✅ **Integrações:** Configurar APIs e serviços

## 🔧 Pré-requisitos

### **Conhecimento Recomendado:**
- 🔹 **Laravel básico:** Artisan, migrations, configurações
- 🔹 **Docker:** Conceitos de containers e volumes
- 🔹 **PostgreSQL:** Estruturas de banco de dados
- 🔹 **Apache/Nginx:** Configurações de servidor web

### **Preparação do Ambiente:**
```bash
# 1. Verificar se está tudo funcionando
docker-compose ps

# 2. Fazer backup das configurações atuais (se existirem)
cp .env .env.backup

# 3. Verificar espaço em disco
docker system df
```

## 🚀 Como Configurar

### Opção 1: Via Interface Web (Recomendado)

1. **Acesse o assistente:** `http://localhost:8000`

2. **Selecione Modo Personalizado:** Clique no cartão "⚙️ Modo Personalizado"

3. **Configure cada seção:**
   - **👤 Usuário Administrador**
   - **🏢 Configurações da Empresa**
   - **🔒 Configurações de Segurança**
   - **📊 Dados Iniciais**
   - **⚡ Performance**
   - **🔌 Integrações**

4. **Revise e confirme:** Verifique todas as configurações

5. **Execute:** Clique em "Configurar Guardian Personalizado"

### Opção 2: Via Linha de Comando (Avançado)

```bash
# Modo interativo completo
docker-compose exec guardian_app php artisan guardian:setup --mode=custom

# Com arquivo de configuração
docker-compose exec guardian_app php artisan guardian:setup --config=custom-config.json
```

## 👤 Configuração do Administrador

### **Informações Básicas:**
```json
{
  "admin": {
    "name": "Seu nome completo",
    "email": "admin@suaempresa.com",
    "password": "SenhaSegura123!",
    "timezone": "America/Sao_Paulo",
    "language": "pt-BR",
    "avatar": "opcional.jpg"
  }
}
```

### **Configurações Avançadas:**
- **🔐 2FA:** Habilitar autenticação de dois fatores
- **🔔 Notificações:** Definir tipos de alertas
- **🎨 Interface:** Tema escuro/claro, layout preferido
- **📱 Mobile:** Configurações para acesso móvel

## 🏢 Configurações da Empresa

### **Informações Corporativas:**
```json
{
  "company": {
    "name": "Nome da Sua Empresa Ltda",
    "legal_name": "Razão Social Completa",
    "cnpj": "00.000.000/0001-00",
    "address": {
      "street": "Rua Principal, 123",
      "city": "São Paulo",
      "state": "SP",
      "zipcode": "01000-000",
      "country": "Brasil"
    },
    "contact": {
      "phone": "+55 11 9999-9999",
      "email": "contato@empresa.com",
      "website": "https://empresa.com"
    }
  }
}
```

### **Configurações Operacionais:**
- **🕐 Horário:** Dias úteis, fuso horário
- **💰 Financeiro:** Moeda padrão, formato de números
- **📅 Calendário:** Feriados, formato de datas
- **🌐 Localização:** Idioma, país, região

## 🔒 Configurações de Segurança

### **Níveis de Segurança:**

#### **🟢 Básico (Desenvolvimento/Interno)**
```json
{
  "security_level": "basic",
  "features": {
    "password_complexity": "medium",
    "session_timeout": "8_hours",
    "audit_level": "basic",
    "ssl_required": false,
    "two_factor": false
  }
}
```

#### **🟡 Médio (Pequenas Empresas)**
```json
{
  "security_level": "medium",
  "features": {
    "password_complexity": "high",
    "session_timeout": "4_hours",
    "audit_level": "detailed",
    "ssl_required": true,
    "two_factor": "optional"
  }
}
```

#### **🔴 Alto (Empresas/Dados Sensíveis)**
```json
{
  "security_level": "high",
  "features": {
    "password_complexity": "very_high",
    "session_timeout": "2_hours",
    "audit_level": "complete",
    "ssl_required": true,
    "two_factor": "required",
    "ip_whitelist": true,
    "failed_login_lockout": true
  }
}
```

### **Configurações Detalhadas:**

#### **Políticas de Senha:**
```json
{
  "password_policy": {
    "min_length": 8,
    "require_uppercase": true,
    "require_lowercase": true,
    "require_numbers": true,
    "require_symbols": true,
    "prevent_reuse": 5,
    "expire_days": 90
  }
}
```

#### **Configurações de Sessão:**
```json
{
  "session": {
    "lifetime": 240,          // minutos
    "idle_timeout": 30,       // minutos
    "secure_cookies": true,
    "same_site": "strict",
    "remember_me": false
  }
}
```

## 📊 Dados Iniciais

### **Estrutura Organizacional:**

#### **Departamentos:**
```json
{
  "departments": [
    {
      "name": "Tecnologia",
      "description": "Desenvolvimento e infraestrutura",
      "manager": "admin@empresa.com"
    },
    {
      "name": "Comercial",
      "description": "Vendas e relacionamento",
      "manager": "comercial@empresa.com"
    },
    {
      "name": "Financeiro",
      "description": "Contabilidade e finanças",
      "manager": "financeiro@empresa.com"
    }
  ]
}
```

#### **Cargos/Funções:**
```json
{
  "roles": [
    {
      "name": "Super Administrador",
      "permissions": ["*"],
      "description": "Acesso total ao sistema"
    },
    {
      "name": "Gerente de Projetos",
      "permissions": ["project.*", "task.*", "team.view"],
      "description": "Gestão completa de projetos"
    },
    {
      "name": "Desenvolvedor Senior",
      "permissions": ["task.*", "project.view", "report.view"],
      "description": "Execução e gestão de tarefas"
    },
    {
      "name": "Desenvolvedor Junior",
      "permissions": ["task.create", "task.edit", "task.view"],
      "description": "Execução de tarefas atribuídas"
    }
  ]
}
```

### **Usuários Iniciais:**
```json
{
  "users": [
    {
      "name": "Maria Silva",
      "email": "maria@empresa.com",
      "role": "Gerente de Projetos",
      "department": "Tecnologia",
      "active": true,
      "password": "SenhaSegura123!"
    },
    {
      "name": "João Santos",
      "email": "joao@empresa.com",
      "role": "Desenvolvedor Senior",
      "department": "Tecnologia",
      "active": true,
      "password": "SenhaSegura123!"
    }
  ]
}
```

### **Projetos Modelo:**
```json
{
  "projects": [
    {
      "name": "Implementação do Guardian",
      "description": "Setup e configuração inicial do sistema",
      "status": "active",
      "manager": "maria@empresa.com",
      "start_date": "2025-01-15",
      "end_date": "2025-02-15",
      "budget": 10000.00,
      "priority": "high"
    }
  ]
}
```

## ⚡ Configurações de Performance

### **Ambientes Disponíveis:**

#### **🔧 Desenvolvimento**
```json
{
  "environment": "development",
  "cache": {
    "config": false,
    "routes": false,
    "views": false,
    "opcache": false
  },
  "debug": {
    "enabled": true,
    "whoops": true,
    "query_log": true
  },
  "queue": "sync"
}
```

#### **🧪 Teste/Homologação**
```json
{
  "environment": "staging",
  "cache": {
    "config": true,
    "routes": true,
    "views": true,
    "opcache": true
  },
  "debug": {
    "enabled": false,
    "whoops": false,
    "query_log": false
  },
  "queue": "redis"
}
```

#### **🚀 Produção**
```json
{
  "environment": "production",
  "cache": {
    "config": true,
    "routes": true,
    "views": true,
    "opcache": true,
    "redis": true
  },
  "debug": {
    "enabled": false,
    "whoops": false,
    "query_log": false
  },
  "queue": "redis",
  "optimization": "maximum"
}
```

### **Configurações de Cache:**
```json
{
  "cache": {
    "default": "redis",
    "stores": {
      "redis": {
        "driver": "redis",
        "connection": "cache",
        "ttl": 3600
      },
      "file": {
        "driver": "file",
        "path": "storage/framework/cache"
      }
    },
    "prefix": "guardian_cache"
  }
}
```

### **Configurações de Fila:**
```json
{
  "queue": {
    "default": "redis",
    "connections": {
      "redis": {
        "driver": "redis",
        "connection": "default",
        "queue": "guardian_queue",
        "retry_after": 90,
        "block_for": null
      }
    }
  }
}
```

## 🔌 Configurações de Integrações

### **Email (SMTP):**
```json
{
  "mail": {
    "mailer": "smtp",
    "host": "smtp.empresa.com",
    "port": 587,
    "username": "guardian@empresa.com",
    "password": "SenhaEmailSegura",
    "encryption": "tls",
    "from": {
      "address": "guardian@empresa.com",
      "name": "Sistema Guardian"
    },
    "templates": {
      "welcome": "emails.welcome",
      "password_reset": "emails.password-reset",
      "task_assigned": "emails.task-assigned"
    }
  }
}
```

### **Notificações:**
```json
{
  "notifications": {
    "channels": {
      "slack": {
        "enabled": true,
        "webhook": "https://hooks.slack.com/services/...",
        "channel": "#guardian-alerts"
      },
      "discord": {
        "enabled": false,
        "webhook": "https://discord.com/api/webhooks/..."
      },
      "telegram": {
        "enabled": false,
        "bot_token": "BOT_TOKEN",
        "chat_id": "CHAT_ID"
      }
    },
    "events": [
      "user.created",
      "project.created",
      "task.completed",
      "deadline.approaching"
    ]
  }
}
```

### **APIs Externas:**
```json
{
  "integrations": {
    "github": {
      "enabled": true,
      "token": "github_pat_...",
      "organization": "sua-org",
      "auto_sync": true
    },
    "jira": {
      "enabled": false,
      "url": "https://empresa.atlassian.net",
      "username": "user@empresa.com",
      "token": "API_TOKEN"
    },
    "trello": {
      "enabled": false,
      "key": "TRELLO_KEY",
      "token": "TRELLO_TOKEN"
    }
  }
}
```

## 📁 Estrutura de Arquivos Personalizada

### **Uploads e Armazenamento:**
```json
{
  "storage": {
    "disk": "local",
    "max_file_size": "50MB",
    "allowed_types": [
      "pdf", "doc", "docx", "xls", "xlsx",
      "jpg", "jpeg", "png", "gif",
      "zip", "rar", "7z"
    ],
    "virus_scan": true,
    "auto_backup": true,
    "retention_days": 365
  }
}
```

### **Estrutura de Pastas:**
```
storage/app/
├── public/
│   ├── uploads/
│   │   ├── avatars/
│   │   ├── documents/
│   │   ├── projects/
│   │   └── temp/
│   ├── exports/
│   └── reports/
├── private/
│   ├── backups/
│   ├── logs/
│   └── cache/
└── temp/
```

## 🎨 Personalização da Interface

### **Temas Disponíveis:**
```json
{
  "themes": {
    "default": "guardian-light",
    "available": [
      {
        "name": "guardian-light",
        "description": "Tema claro padrão",
        "primary_color": "#007bff",
        "secondary_color": "#6c757d"
      },
      {
        "name": "guardian-dark",
        "description": "Tema escuro",
        "primary_color": "#0d6efd",
        "secondary_color": "#495057"
      },
      {
        "name": "corporate-blue",
        "description": "Azul corporativo",
        "primary_color": "#003d7a",
        "secondary_color": "#5a6c7d"
      }
    ]
  }
}
```

### **Personalização da Logo:**
```json
{
  "branding": {
    "logo": {
      "main": "storage/app/public/logo.png",
      "favicon": "storage/app/public/favicon.ico",
      "email": "storage/app/public/logo-email.png"
    },
    "colors": {
      "primary": "#007bff",
      "secondary": "#6c757d",
      "success": "#28a745",
      "warning": "#ffc107",
      "danger": "#dc3545"
    }
  }
}
```

## 🔄 Processo de Configuração Detalhado

### **Etapa 1: Verificação do Ambiente**
```bash
# O sistema verificará automaticamente:
- ✅ Docker funcionando
- ✅ Containers ativos
- ✅ Conexão com banco
- ✅ Permissões de arquivo
- ✅ Configurações .env
```

### **Etapa 2: Configuração da Base**
```bash
# Configurações aplicadas:
- 🔧 Variáveis de ambiente
- 🗄️ Estrutura do banco
- 📁 Diretórios de armazenamento
- 🔒 Configurações de segurança
```

### **Etapa 3: Criação dos Dados**
```bash
# Dados inseridos conforme configuração:
- 👤 Usuário administrador
- 🏢 Informações da empresa
- 👥 Usuários iniciais (se configurados)
- 📊 Projetos modelo (se configurados)
```

### **Etapa 4: Otimização**
```bash
# Aplicação das otimizações:
- ⚡ Cache conforme ambiente
- 🔗 Configuração de filas
- 📈 Indexação do banco
- 🗜️ Compressão de assets
```

### **Etapa 5: Validação**
```bash
# Testes finais:
- 🔐 Login do administrador
- 📊 Carregamento do dashboard
- 🔗 Conectividade de serviços
- ✅ Configurações aplicadas
```

## 🛠️ Comandos Úteis Pós-Configuração

### **Gerenciamento de Cache:**
```bash
# Limpar todos os caches
docker-compose exec guardian_app php artisan cache:clear

# Recriar caches otimizados
docker-compose exec guardian_app php artisan optimize

# Cache específico
docker-compose exec guardian_app php artisan config:cache
docker-compose exec guardian_app php artisan route:cache
docker-compose exec guardian_app php artisan view:cache
```

### **Gerenciamento de Filas:**
```bash
# Iniciar worker de filas
docker-compose exec guardian_app php artisan queue:work

# Ver status das filas
docker-compose exec guardian_app php artisan queue:monitor

# Limpar filas com falha
docker-compose exec guardian_app php artisan queue:flush
```

### **Backup e Restauração:**
```bash
# Backup completo
docker-compose exec guardian_db pg_dump -U falcon_user guardian_db > backup.sql

# Backup apenas dados
docker-compose exec guardian_db pg_dump -U falcon_user --data-only guardian_db > data.sql

# Restaurar backup
docker-compose exec -i guardian_db psql -U falcon_user guardian_db < backup.sql
```

## 📊 Monitoramento Personalizado

### **Métricas Configuráveis:**
```json
{
  "monitoring": {
    "enabled": true,
    "metrics": {
      "performance": {
        "response_time": true,
        "memory_usage": true,
        "cpu_usage": true,
        "disk_usage": true
      },
      "business": {
        "active_users": true,
        "projects_created": true,
        "tasks_completed": true,
        "login_frequency": true
      },
      "security": {
        "failed_logins": true,
        "suspicious_activity": true,
        "permission_changes": true
      }
    },
    "alerts": {
      "email": ["admin@empresa.com"],
      "slack": "#alerts",
      "thresholds": {
        "cpu_usage": 80,
        "memory_usage": 85,
        "disk_usage": 90,
        "failed_logins": 5
      }
    }
  }
}
```

## 🚨 Solução de Problemas Avançados

### **Problemas de Performance:**
```bash
# Verificar uso de recursos
docker stats guardian_app guardian_db guardian_redis

# Analisar queries lentas
docker-compose exec guardian_db psql -U falcon_user -d guardian_db -c "
SELECT query, mean_time, calls 
FROM pg_stat_statements 
ORDER BY mean_time DESC 
LIMIT 10;"

# Verificar logs de performance
docker-compose exec guardian_app tail -f storage/logs/performance.log
```

### **Problemas de Conectividade:**
```bash
# Testar conexão com banco
docker-compose exec guardian_app php artisan tinker
>>> DB::connection()->getPdo();

# Testar conexão com Redis
docker-compose exec guardian_app php artisan tinker
>>> Redis::ping();

# Verificar network do Docker
docker network ls
docker network inspect guardian_network
```

### **Problemas de Permissões:**
```bash
# Corrigir permissões de arquivos
docker-compose exec guardian_app chown -R www-data:www-data storage bootstrap/cache

# Verificar permissões
docker-compose exec guardian_app ls -la storage/

# Limpar logs antigos
docker-compose exec guardian_app find storage/logs -name "*.log" -mtime +30 -delete
```

## 📚 Configurações Avançadas

### **SSL/TLS em Produção:**
```apache
# docker/apache/ssl.conf
<VirtualHost *:443>
    ServerName guardian.empresa.com
    DocumentRoot /var/www/html/public
    
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/guardian.crt
    SSLCertificateKeyFile /etc/ssl/private/guardian.key
    SSLCertificateChainFile /etc/ssl/certs/chain.crt
    
    # Configurações de segurança SSL
    SSLProtocol all -SSLv2 -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder on
    
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</VirtualHost>
```

### **Configuração de Proxy Reverso:**
```nginx
# nginx.conf para proxy reverso
upstream guardian {
    server guardian_app:80;
}

server {
    listen 443 ssl http2;
    server_name guardian.empresa.com;
    
    ssl_certificate /etc/ssl/certs/guardian.crt;
    ssl_certificate_key /etc/ssl/private/guardian.key;
    
    location / {
        proxy_pass http://guardian;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 📞 Próximos Passos

### **Configuração Inicial:**
1. **✅ Validar:** Testar todas as configurações
2. **👥 Usuarios:** Cadastrar equipe inicial
3. **📋 Processos:** Definir fluxos de trabalho
4. **🔗 Integrações:** Conectar ferramentas externas

### **Otimização Contínua:**
1. **📊 Monitorar:** Acompanhar métricas
2. **🔧 Ajustar:** Refinar configurações
3. **📈 Escalar:** Aumentar recursos conforme necessário
4. **🛡️ Segurança:** Revisar e atualizar políticas

### **Manutenção:**
1. **🔄 Backup:** Implementar rotina automatizada
2. **📱 Atualizações:** Planejar upgrades
3. **👨‍🎓 Treinamento:** Capacitar usuários
4. **📋 Documentação:** Manter documentação atualizada

---

**⚙️ Importante:** O modo personalizado oferece máximo controle, mas requer conhecimento técnico. Certifique-se de entender cada configuração antes de aplicar em produção!
