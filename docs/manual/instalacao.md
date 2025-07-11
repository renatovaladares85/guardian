# 🚀 Guia de Instalação - Guardian

Guia completo para instalação e configuração inicial do sistema Guardian.

## 📋 Pré-requisitos

### Requisitos do Sistema
- **SO**: Windows 10/11, Linux Ubuntu/Debian, macOS
- **RAM**: 4GB mínimo, 8GB recomendado
- **Armazenamento**: 10GB livres
- **Rede**: Conexão com internet para download de imagens

### Software Necessário
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS)
- Docker + Docker Compose (Linux)
- [Git](https://git-scm.com/)
- Editor de código (VS Code recomendado)

## 🔧 Instalação Passo a Passo

### 1. Preparação do Ambiente

#### Windows
```powershell
# Verificar se Docker está instalado
docker --version
docker-compose --version

# Verificar se Git está instalado  
git --version
```

#### Linux (Ubuntu/Debian)
```bash
# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Adicionar usuário ao grupo docker
sudo usermod -aG docker $USER
```

### 2. Download do Projeto

```bash
# Clone do repositório
git clone https://github.com/empresa/guardian.git
cd guardian

# Verificar estrutura do projeto
ls -la
```

### 3. Configuração Inicial

#### Criar arquivo .env
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar configurações (se necessário)
nano .env
```

#### Configurações principais no .env:
```env
APP_NAME=Guardian
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# Banco de dados
DB_CONNECTION=pgsql
DB_HOST=guardian_db
DB_DATABASE=guardian_db
DB_USERNAME=guardian_user
DB_PASSWORD=guardian_secure_pass

# Redis
REDIS_HOST=guardian_redis
REDIS_PASSWORD=guardian_redis_pass

# Sessions e Cache
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 4. Construção e Inicialização

#### Primeira execução
```bash
# Construir containers (primeira vez)
docker-compose up -d --build

# Aguardar inicialização (2-3 minutos)
docker-compose ps

# Verificar se todos containers estão "Up"
```

#### Executar migrations e seeds
```bash
# Aplicar estrutura do banco de dados
docker-compose exec guardian_app php artisan migrate:fresh --seed

# Verificar se foi executado com sucesso
# Deve mostrar: "Database seeded successfully with complex test data!"
```

### 5. Verificação da Instalação

#### Testar acesso à aplicação
```bash
# Via Nginx (recomendado)
curl -I http://localhost

# Via Apache (direto)
curl -I http://localhost:8000

# Endpoint de health check
curl http://localhost/health-check
```

#### Verificar containers
```bash
# Status de todos os containers
docker-compose ps

# Logs da aplicação
docker-compose logs guardian_app

# Logs do banco de dados
docker-compose logs guardian_db
```

## 🔐 Primeiros Passos

### Credenciais de Acesso
Após a instalação, você pode acessar com:

```
Super Administrador:
- Login: gcosta
- Senha: guardian123

Gerente de Projetos:
- Login: aferreira
- Senha: guardian123

Desenvolvedor:
- Login: lalmeida
- Senha: guardian123
```

### Primeiro Login
1. Acesse: http://localhost
2. Use uma das credenciais acima
3. Explore o dashboard com dados de teste
4. Configure usuários e projetos conforme necessário

## 🛠️ Configurações Avançadas

### Personalizar Portas
Edite o `docker-compose.yml` se as portas estiverem em uso:

```yaml
services:
  guardian_nginx:
    ports:
      - "8080:80"  # Muda porta HTTP para 8080
      
  guardian_app:
    ports:
      - "8001:80"  # Muda porta Apache para 8001
```

### Configurar SSL/HTTPS
Para produção, configure certificados SSL:

```bash
# Criar diretório para certificados
mkdir -p docker/ssl

# Copiar certificados SSL
cp seu-certificado.crt docker/ssl/
cp sua-chave-privada.key docker/ssl/

# Atualizar configuração do Nginx
# Editar: docker/nginx/conf.d/guardian.conf
```

### Backup Automático
O sistema já vem configurado com backup automático, mas você pode ajustar:

```bash
# Verificar backups
ls -la database/backups/

# Executar backup manual
docker-compose exec guardian_backup /backup.sh
```

## 🚨 Solução de Problemas

### Container não inicia
```bash
# Verificar logs
docker-compose logs guardian_app

# Reiniciar containers
docker-compose restart

# Rebuild completo
docker-compose down
docker-compose up -d --build
```

### Erro de conexão com banco
```bash
# Verificar status do PostgreSQL
docker-compose exec guardian_db pg_isready -U guardian_user

# Verificar logs do banco
docker-compose logs guardian_db

# Resetar dados (cuidado em produção!)
docker-compose down -v
docker-compose up -d --build
```

### Problemas de permissão (Linux)
```bash
# Ajustar permissões
sudo chown -R $USER:$USER .
sudo chmod -R 755 .

# Verificar se usuário está no grupo docker
groups $USER
```

### Porta em uso
```bash
# Verificar qual processo usa a porta
sudo netstat -tulpn | grep :80

# Parar processo conflitante ou mudar porta no docker-compose.yml
```

## 📊 Validação da Instalação

### Checklist de Verificação
- [ ] Todos os containers estão "Up" (`docker-compose ps`)
- [ ] Aplicação responde em http://localhost
- [ ] Health check retorna status "healthy"
- [ ] Login funciona com credenciais de teste
- [ ] Dashboard carrega com dados de exemplo
- [ ] Backup automático está configurado

### Testes Funcionais
```bash
# Teste de login
curl -X POST http://localhost/login \
  -d "email=gcosta" \
  -d "password=guardian123"

# Teste de API (se disponível)
curl http://localhost/api/health

# Teste de banco de dados
docker-compose exec guardian_app php artisan tinker
# No tinker: User::count()
```

## 🎯 Próximos Passos

Após a instalação bem-sucedida:

1. **Configure usuários reais** - Remova dados de teste em produção
2. **Personalize o sistema** - Logo, cores, configurações
3. **Configure backup em produção** - Para ambiente externo
4. **Configure monitoramento** - Logs e alertas
5. **Documente sua configuração** - Para sua equipe

Para mais detalhes, consulte:
- [Manual do Usuário](usuario.md)
- [Configuração Avançada](configuracao.md)
- [Documentação de Deploy](../deployment/docker.md)

---

**Instalação concluída!** 🎉 Seu sistema Guardian está pronto para uso.
