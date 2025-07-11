# 🛡️ Guardian - Sistema de Gestão Empresarial

Sistema completo de gestão empresarial com foco em produtividade, colaboração e controle de projetos. Arquitetura moderna com Docker, cache inteligente e sistema de login único inovador.

## 🚀 Quick Start

```bash
# Clone e execute
git clone https://github.com/empresa/guardian.git
cd guardian
docker-compose up -d --build

# Aplique dados de teste
docker-compose exec guardian_app php artisan migrate:fresh --seed

# Acesse: http://localhost
# Login: gcosta | Senha: guardian123
```

## 📚 Documentação Completa

### 📖 Manuais do Usuário
- **[🔧 Guia de Instalação](docs/manual/instalacao.md)** - Setup completo passo a passo
- **[👤 Manual do Usuário](docs/manual/usuario.md)** - Como usar todas as funcionalidades
- **[⚙️ Configuração Avançada](docs/manual/configuracao.md)** - Personalização e otimização

### 🏗️ Documentação Técnica
- **[🏗️ Arquitetura do Sistema](docs/architecture/sistema.md)** - Visão técnica completa

### 🚀 Deploy e Produção
- **[🐳 Guia de Deploy](docs/deployment/docker.md)** - Deploy completo em produção

### 🔌 API e Integração
- **[📋 API Endpoints](docs/api/endpoints.md)** - Documentação completa da API REST

## ✨ Principais Funcionalidades

### 🎯 Sistema de Login Único
Geração inteligente de login baseado em **primeira letra + sobrenome**:
```
Gabriela Costa → gcosta
Ana Silva Ferreira → aferreira  
Lucas Almeida → lalmeida
```
- ✅ Detecção automática de palavras ofensivas
- ✅ Resolução inteligente de conflitos
- ✅ Suporte a nomes compostos

### 🛠️ Gestão Completa
- **Projetos**: Criação, acompanhamento e relatórios
- **Tarefas**: Atribuição, dependências e progresso
- **Usuários**: Roles, permissões e auditoria
- **Dashboard**: Analytics e métricas em tempo real

### 🔐 Segurança Avançada
- **JWT Authentication**: Tokens seguros
- **Rate Limiting**: Proteção contra abuso
- **Auditoria**: Log completo de ações
- **Backup**: Automático a cada 6 horas

## 🐳 Infraestrutura Docker

### Containers em Produção
| Container | Função | Status |
|-----------|---------|---------|
| `guardian_app` | Aplicação Laravel | ✅ Persistente |
| `guardian_db` | PostgreSQL 15 | ✅ Backup automático |
| `guardian_redis` | Cache/Sessões | ✅ Persistente |
| `guardian_nginx` | Proxy + SSL | ✅ Load balancer |
| `guardian_watchtower` | Auto-update | ✅ Monitoramento |
| `guardian_healthcheck` | Health monitor | ✅ 24/7 |
| `guardian_backup` | Backup system | ✅ A cada 6h |

### Volumes Persistentes
```yaml
volumes:
  guardian_db_data:     # Dados PostgreSQL
  guardian_redis_data:  # Cache Redis  
  ./storage:           # Uploads e logs
  ./database/backups:  # Backups automáticos
```

## 🛠️ Stack Tecnológica

**Backend:**
- Laravel 11 + PHP 8.2
- PostgreSQL 15 + Redis 7

**Frontend:**  
- Bootstrap 5 + Vanilla JS
- Blade Templates + AJAX

**Infraestrutura:**
- Docker + Docker Compose
- Nginx + Apache
- Health Monitoring

## 🎭 Credenciais de Teste

| Role | Login | Senha | Acesso |
|------|-------|-------|---------|
| Super Admin | `gcosta` | `guardian123` | Total |
| Manager | `aferreira` | `guardian123` | Projetos |
| Developer | `lalmeida` | `guardian123` | Tarefas |
| User | `bsantos` | `guardian123` | Básico |

## 🔍 Monitoramento

### Health Check
```bash
# Verificar saúde geral
curl http://localhost/health-check

# Status dos containers  
docker-compose ps

# Logs em tempo real
docker-compose logs -f guardian_app
```

### Backup Automático
```bash
# Localização: ./database/backups/
# Frequência: A cada 6 horas
# Retenção: 7 dias

# Backup manual
docker-compose exec guardian_backup /backup.sh
```

## 🚨 Troubleshooting Rápido

**Container não inicia:**
```bash
docker-compose logs guardian_app
docker-compose restart
```

**Reset completo:**
```bash
docker-compose down -v
docker-compose up -d --build
```

**Erro de banco:**
```bash
docker-compose exec guardian_db pg_isready -U guardian_user
```

## 📊 Métricas e Performance

- **Response Time**: < 200ms média
- **Database**: Índices otimizados
- **Cache Hit Ratio**: > 85%
- **Uptime**: 99.9% target

### Logs Estruturados
- `storage/logs/laravel.log` - Aplicação
- `storage/logs/audit.log` - Auditoria  
- `storage/logs/security.log` - Segurança
- `storage/logs/performance.log` - Performance

## 🎯 Próximos Passos

1. **[Instale o sistema](docs/manual/instalacao.md)** seguindo o guia completo
2. **[Configure para produção](docs/manual/configuracao.md)** com SSL e monitoramento
3. **[Explore a API](docs/api/endpoints.md)** para integrações
4. **[Entenda a arquitetura](docs/architecture/sistema.md)** para customizações

## 🤝 Contribuição & Suporte

- **Issues**: [GitHub Issues](https://github.com/empresa/guardian/issues)
- **Email**: suporte@guardian.com  
- **Docs**: Sempre atualizadas neste repositório

## 📄 Licença e Direitos Autorais

### 🛡️ Propriedade Intelectual
**© 2025 Renato Valadares - Todos os direitos reservados**

- **Autor**: Renato Valadares (renatovaladares85@gmail.com)
- **Sistema**: Guardian - Sistema de Gestão Empresarial
- **Versão**: 1.0.0
- **Data**: Julho 2025

### 📋 Termos de Uso

Este software é propriedade exclusiva de **Renato Valadares** e está protegido por direitos autorais.

#### ✅ **Permitido:**
- Uso para fins comerciais e pessoais
- Instalação em múltiplos servidores
- Backup e arquivamento
- Configuração e personalização de interface

#### ❌ **Proibido:**
- **Modificação do código fonte**
- **Redistribuição ou revenda**
- **Engenharia reversa**
- **Criação de trabalhos derivados**
- **Remoção de créditos autorais**

#### 🔒 **Proteção e Segurança:**
- Código fonte criptografado em produção
- Validação de integridade automática
- Logs de auditoria completos
- Proteção contra acesso direto a arquivos

### 📞 **Contato e Suporte**
- **Email**: renatovaladares85@gmail.com
- **GitHub**: https://github.com/renatovaladares85
- **Projeto**: https://github.com/renatovaladares85/guardian

### ⚖️ **Aviso Legal**
O uso deste software implica na aceitação integral destes termos. 
Violações podem resultar em ações legais.

---

**Guardian** - Sistema proprietário desenvolvido por Renato Valadares 🛡️
