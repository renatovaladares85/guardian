# ⚙️ Manual de Configuração - Guardian

Guia avançado para configuração e personalização do sistema Guardian.

## 🎯 Visão Geral

Este manual é destinado a administradores de sistema que precisam configurar, personalizar e manter o Guardian em funcionamento. Abrange desde configurações básicas até otimizações avançadas.

## 🔧 Configurações de Sistema

### Arquivo .env Principal

O arquivo `.env` contém todas as configurações essenciais:

```env
# Aplicação
APP_NAME=Guardian
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost
APP_TIMEZONE=America/Sao_Paulo

# Segurança
APP_KEY=base64:CHAVE_GERADA_AUTOMATICAMENTE

# Banco de Dados
DB_CONNECTION=pgsql
DB_HOST=guardian_db
DB_PORT=5432
DB_DATABASE=guardian_db
DB_USERNAME=guardian_user
DB_PASSWORD=guardian_secure_pass

# Redis (Cache/Session)
REDIS_HOST=guardian_redis
REDIS_PORT=6379
REDIS_PASSWORD=guardian_redis_pass

# E-mail (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=sistema@empresa.com
MAIL_PASSWORD=senha_do_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@empresa.com
MAIL_FROM_NAME="${APP_NAME}"

# Sessions e Cache
SESSION_DRIVER=redis
SESSION_LIFETIME=120
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Logs
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error
```

### Configurações de Performance

#### Cache Redis
```env
# Configuração otimizada para produção
REDIS_CLIENT=predis
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3

# Configurações de memória (redis.conf)
maxmemory 512mb
maxmemory-policy allkeys-lru
```

#### Base de Dados PostgreSQL
```sql
-- Configurações recomendadas postgresql.conf
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 64MB
max_connections = 100
```

## 🐳 Configuração Docker

### docker-compose.yml Personalizado

Para ambientes específicos, personalize o `docker-compose.yml`:

```yaml
version: '3.8'

services:
  guardian_app:
    build: 
      context: .
      dockerfile: Dockerfile.prod  # Para produção
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    restart: always
    
  guardian_nginx:
    image: nginx:alpine
    ports:
      - "443:443"  # HTTPS
      - "80:80"    # HTTP
    volumes:
      - ./docker/nginx/ssl:/etc/nginx/ssl
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    depends_on:
      - guardian_app
    restart: always
```

### Configuração SSL/HTTPS

#### 1. Obter Certificados SSL
```bash
# Let's Encrypt (gratuito)
sudo apt install certbot
sudo certbot certonly --standalone -d seudominio.com

# Ou usar certificado próprio
mkdir -p docker/ssl
cp certificado.crt docker/ssl/
cp chave-privada.key docker/ssl/
```

#### 2. Configurar Nginx para HTTPS
```nginx
# docker/nginx/conf.d/guardian-ssl.conf
server {
    listen 80;
    server_name seudominio.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name seudominio.com;
    
    ssl_certificate /etc/nginx/ssl/certificado.crt;
    ssl_certificate_key /etc/nginx/ssl/chave-privada.key;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;
    
    location / {
        proxy_pass http://guardian_app;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 📧 Configuração de E-mail

### Provedores Populares

#### Gmail/Google Workspace
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=sistema@empresa.com
MAIL_PASSWORD=senha_de_app  # Use senha de app, não a senha normal
MAIL_ENCRYPTION=tls
```

#### Outlook/Hotmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=sistema@outlook.com
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
```

#### Servidor SMTP Próprio
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.seudominio.com
MAIL_PORT=587
MAIL_USERNAME=sistema@seudominio.com
MAIL_PASSWORD=senha_smtp
MAIL_ENCRYPTION=tls
```

### Testar Configuração de E-mail
```bash
# Dentro do container da aplicação
docker-compose exec guardian_app php artisan tinker

# No Tinker, execute:
Mail::raw('Teste de configuração', function($msg) {
    $msg->to('teste@empresa.com')->subject('Guardian - Teste SMTP');
});
```

## 🔐 Configurações de Segurança

### Autenticação e Autorização

#### Configurar Two-Factor Authentication (2FA)
```bash
# Instalar pacote 2FA
composer require pragmarx/google2fa-laravel

# Publicar configurações
php artisan vendor:publish --provider="PragmaRX\Google2FALaravel\ServiceProvider"
```

#### Rate Limiting Personalizado
```php
// config/guardian-security.php
return [
    'rate_limits' => [
        'login' => '5,1',        // 5 tentativas por minuto
        'api' => '60,1',         // 60 requests por minuto
        'heavy_api' => '10,1',   // 10 requests pesados por minuto
    ],
    
    'blocked_ips' => [
        // IPs bloqueados permanentemente
    ],
    
    'allowed_ips' => [
        // IPs sempre permitidos (admin)
    ],
];
```

### Configuração de Backup Seguro

#### Script de Backup Avançado
```bash
#!/bin/bash
# /scripts/backup-advanced.sh

# Configurações
BACKUP_DIR="/backups"
DB_NAME="guardian_db"
DB_USER="guardian_user"
ENCRYPTION_PASSWORD="senha_super_segura"
RETENTION_DAYS=30
S3_BUCKET="guardian-backups"

# Criar backup com compressão e criptografia
pg_dump -h guardian_db -U $DB_USER $DB_NAME | \
  gzip | \
  gpg --cipher-algo AES256 --compress-algo 1 --symmetric --batch --yes --passphrase="$ENCRYPTION_PASSWORD" > \
  "$BACKUP_DIR/guardian_$(date +%Y%m%d_%H%M%S).sql.gz.gpg"

# Upload para S3 (opcional)
aws s3 cp "$BACKUP_DIR/" "s3://$S3_BUCKET/" --recursive --exclude "*" --include "*.gpg"

# Limpeza de backups antigos
find $BACKUP_DIR -name "*.gpg" -mtime +$RETENTION_DAYS -delete
```

## 📊 Monitoramento e Logs

### Configuração de Logs Avançada

#### config/logging.php
```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
        'ignore_exceptions' => false,
    ],

    'guardian_audit' => [
        'driver' => 'daily',
        'path' => storage_path('logs/audit.log'),
        'level' => 'info',
        'days' => 90,
    ],

    'guardian_security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'warning',
        'days' => 365,
    ],

    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Guardian',
        'emoji' => ':boom:',
        'level' => 'critical',
    ],
];
```

### Health Checks Customizados

#### resources/health-checks/custom.php
```php
<?php
// Verificações customizadas de saúde do sistema

return [
    'database_performance' => function() {
        $start = microtime(true);
        DB::select('SELECT 1');
        $duration = microtime(true) - $start;
        
        return $duration < 0.1 ? 'healthy' : 'slow';
    },
    
    'redis_memory' => function() {
        $redis = Redis::connection();
        $info = $redis->info('memory');
        $usage = $info['used_memory'] / $info['maxmemory'];
        
        return $usage < 0.8 ? 'healthy' : 'high_memory';
    },
    
    'disk_space' => function() {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        $usage = 1 - ($free / $total);
        
        return $usage < 0.9 ? 'healthy' : 'low_space';
    },
];
```

## 🎨 Personalização da Interface

### Temas e Cores

#### resources/sass/variables.scss
```scss
// Cores primárias da empresa
$primary-color: #1a73e8;
$secondary-color: #34a853;
$danger-color: #ea4335;
$warning-color: #fbbc04;

// Tema escuro
$dark-bg: #1a1a1a;
$dark-text: #e0e0e0;
$dark-card: #2d2d2d;

// Responsividade
$mobile-breakpoint: 768px;
$tablet-breakpoint: 1024px;
```

#### Logo e Branding
```bash
# Substituir logos
cp logo-empresa.png public/img/logo.png
cp favicon.ico public/favicon.ico
cp logo-pequeno.png public/img/logo-sm.png

# Compilar assets
npm run production
```

### Configuração de Idiomas

#### Adicionar Novo Idioma
```bash
# Criar arquivos de tradução
mkdir -p resources/lang/en
cp -r resources/lang/pt resources/lang/en

# Traduzir arquivo por arquivo
resources/lang/en/
├── auth.php
├── messages.php
├── validation.php
└── guardian.php
```

## 🔧 Otimizações de Performance

### Cache de Aplicação

#### Configurar Cache Inteligente
```php
// config/cache.php - Configuração avançada
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'prefix' => env('CACHE_PREFIX', 'guardian_cache'),
    ],
    
    'redis_tags' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'prefix' => 'guardian_tags',
        'tags' => true,
    ],
],

'guardian' => [
    'user_cache_ttl' => 3600,        // 1 hora
    'project_cache_ttl' => 1800,     // 30 minutos
    'report_cache_ttl' => 600,       // 10 minutos
],
```

### Otimização do Banco de Dados

#### Índices Recomendados
```sql
-- Índices para melhor performance
CREATE INDEX idx_users_login ON users(login);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_tasks_assigned_to ON tasks(assigned_to);
CREATE INDEX idx_tasks_project_id ON tasks(project_id);
CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);

-- Índice composto para consultas frequentes
CREATE INDEX idx_tasks_project_status ON tasks(project_id, status);
CREATE INDEX idx_users_active_role ON users(is_active, role);
```

## 🚀 Deploy em Produção

### Configuração de Produção

#### Dockerfile.prod
```dockerfile
FROM php:8.2-apache

# Instalar extensões necessárias para produção
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql zip opcache

# Configurar OPcache para produção
COPY docker/opcache.ini /usr/local/etc/php/conf.d/

# Otimizações Apache
COPY docker/apache/apache2.conf /etc/apache2/
COPY docker/apache/000-default.conf /etc/apache2/sites-available/

# Copiar aplicação
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Otimizar aplicação Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache
```

#### docker/opcache.ini
```ini
; Configuração OPcache para produção
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.validate_timestamps=0
```

### Load Balancer

#### nginx/load-balancer.conf
```nginx
upstream guardian_backend {
    least_conn;
    server guardian_app_1:80 max_fails=3 fail_timeout=30s;
    server guardian_app_2:80 max_fails=3 fail_timeout=30s;
    server guardian_app_3:80 max_fails=3 fail_timeout=30s;
}

server {
    listen 80;
    server_name guardian.empresa.com;
    
    location / {
        proxy_pass http://guardian_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        
        # Health check
        proxy_next_upstream error timeout invalid_header http_500 http_502 http_503;
    }
    
    # Health check endpoint
    location /health {
        access_log off;
        proxy_pass http://guardian_backend/health-check;
    }
}
```

## 🔍 Troubleshooting Avançado

### Problemas de Performance

#### Identificar Gargalos
```bash
# Monitorar recursos do container
docker stats guardian_app guardian_db guardian_redis

# Analisar queries lentas PostgreSQL
docker-compose exec guardian_db psql -U guardian_user -d guardian_db -c "
SELECT query, calls, total_time, mean_time
FROM pg_stat_statements
ORDER BY total_time DESC
LIMIT 10;"

# Verificar cache Redis
docker-compose exec guardian_redis redis-cli info stats
```

#### Otimizar Queries Lentas
```php
// Usar Query Builder com índices
DB::table('tasks')
    ->where('project_id', $projectId)
    ->where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(50)
    ->get();

// Cache em consultas pesadas
Cache::tags(['projects', "project:{$id}"])
    ->remember("project_stats:{$id}", 1800, function() use ($id) {
        return Project::with(['tasks', 'users'])->find($id);
    });
```

### Problemas de Conectividade

#### Diagnóstico de Rede
```bash
# Testar conectividade entre containers
docker-compose exec guardian_app ping guardian_db
docker-compose exec guardian_app ping guardian_redis

# Verificar portas abertas
docker-compose exec guardian_app netstat -tlnp

# Testar conexão com banco
docker-compose exec guardian_app php artisan tinker
# No Tinker: DB::select('SELECT version()');
```

### Recuperação de Desastres

#### Procedimento de Restore
```bash
#!/bin/bash
# Restaurar backup criptografado

BACKUP_FILE="guardian_20241125_143000.sql.gz.gpg"
ENCRYPTION_PASSWORD="senha_super_segura"

# Descriptografar e restaurar
gpg --batch --yes --passphrase="$ENCRYPTION_PASSWORD" --decrypt $BACKUP_FILE | \
  gunzip | \
  docker-compose exec -T guardian_db psql -U guardian_user guardian_db

echo "Backup restaurado com sucesso!"
```

## 📋 Checklist de Configuração

### Pré-produção
- [ ] Variáveis de ambiente configuradas
- [ ] SSL/HTTPS funcionando
- [ ] E-mail SMTP testado
- [ ] Backup automático configurado
- [ ] Logs centralizados
- [ ] Health checks ativos
- [ ] Performance otimizada
- [ ] Segurança revisada

### Pós-deploy
- [ ] Smoke tests executados
- [ ] Monitoramento ativo
- [ ] Alertas configurados
- [ ] Documentação atualizada
- [ ] Equipe treinada
- [ ] Suporte configurado

---

**Sistema configurado!** 🎉 Seu Guardian está otimizado e pronto para produção.
