# 🚀 Guia de Deploy - Guardian

Documentação completa para deploy do sistema Guardian em diferentes ambientes.

## 📋 Visão Geral

Este guia cobre todos os aspectos do deploy do Guardian, desde ambientes de desenvolvimento até produção em escala empresarial.

### Ambientes Suportados
- **Desenvolvimento**: Local com Docker
- **Homologação**: Servidor de testes
- **Produção**: Ambiente de produção
- **Cloud**: AWS, Azure, Google Cloud
- **On-Premise**: Servidores físicos

## 🐳 Deploy com Docker (Recomendado)

### Pré-requisitos
- Docker 20.10+
- Docker Compose 2.0+
- 4GB RAM mínimo
- 10GB armazenamento livre

### Deploy Rápido

#### 1. Clone e Configure
```bash
# Clone do repositório
git clone https://github.com/empresa/guardian.git
cd guardian

# Copie e configure variáveis
cp .env.example .env
nano .env
```

#### 2. Configure Docker Compose
```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  guardian_app:
    build:
      context: .
      dockerfile: Dockerfile.prod
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - ./storage:/var/www/html/storage
      - ./bootstrap/cache:/var/www/html/bootstrap/cache
    restart: always
    
  guardian_db:
    image: postgres:15-alpine
    environment:
      POSTGRES_DB: guardian_db
      POSTGRES_USER: guardian_user
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - guardian_db_data:/var/lib/postgresql/data
      - ./database/backups:/backups
    restart: always
    
  guardian_redis:
    image: redis:7-alpine
    command: redis-server --requirepass ${REDIS_PASSWORD}
    volumes:
      - guardian_redis_data:/data
    restart: always
    
  guardian_nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
      - ./docker/ssl:/etc/nginx/ssl
    depends_on:
      - guardian_app
    restart: always

volumes:
  guardian_db_data:
  guardian_redis_data:
```

#### 3. Deploy
```bash
# Build e inicialização
docker-compose -f docker-compose.prod.yml up -d --build

# Aplicar migrations
docker-compose exec guardian_app php artisan migrate:fresh --seed

# Otimizar para produção
docker-compose exec guardian_app php artisan config:cache
docker-compose exec guardian_app php artisan route:cache
docker-compose exec guardian_app php artisan view:cache
```

### Deploy com SSL/HTTPS

#### Certificado Let's Encrypt
```bash
# Instalar certbot
sudo apt update
sudo apt install certbot

# Obter certificado
sudo certbot certonly --standalone -d seudominio.com

# Copiar certificados
sudo cp /etc/letsencrypt/live/seudominio.com/fullchain.pem docker/ssl/
sudo cp /etc/letsencrypt/live/seudominio.com/privkey.pem docker/ssl/

# Configurar renovação automática
echo "0 2 * * * certbot renew --quiet && docker-compose restart guardian_nginx" | sudo crontab -
```

#### Configuração Nginx SSL
```nginx
# docker/nginx/conf.d/guardian-ssl.conf
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name seudominio.com www.seudominio.com;
    
    ssl_certificate /etc/nginx/ssl/fullchain.pem;
    ssl_certificate_key /etc/nginx/ssl/privkey.pem;
    
    # Configurações SSL modernas
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # Headers de segurança
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options DENY always;
    add_header X-Content-Type-Options nosniff always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    location / {
        proxy_pass http://guardian_app;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## ☁️ Deploy em Cloud

### AWS (Amazon Web Services)

#### Usando ECS (Elastic Container Service)

##### 1. Preparar Imagem Docker
```dockerfile
# Dockerfile.aws
FROM php:8.2-apache

# Instalar dependências
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql zip

# Configurar Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/
RUN a2enmod rewrite

# Copiar aplicação
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Otimizar Laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 80
```

##### 2. Build e Push para ECR
```bash
# Configurar AWS CLI
aws configure

# Criar repositório ECR
aws ecr create-repository --repository-name guardian

# Obter login
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 123456789.dkr.ecr.us-east-1.amazonaws.com

# Build e push
docker build -f Dockerfile.aws -t guardian .
docker tag guardian:latest 123456789.dkr.ecr.us-east-1.amazonaws.com/guardian:latest
docker push 123456789.dkr.ecr.us-east-1.amazonaws.com/guardian:latest
```

##### 3. Task Definition ECS
```json
{
  "family": "guardian-task",
  "networkMode": "awsvpc",
  "requiresCompatibilities": ["FARGATE"],
  "cpu": "512",
  "memory": "1024",
  "executionRoleArn": "arn:aws:iam::123456789:role/ecsTaskExecutionRole",
  "containerDefinitions": [
    {
      "name": "guardian-app",
      "image": "123456789.dkr.ecr.us-east-1.amazonaws.com/guardian:latest",
      "portMappings": [
        {
          "containerPort": 80,
          "protocol": "tcp"
        }
      ],
      "environment": [
        {
          "name": "APP_ENV",
          "value": "production"
        },
        {
          "name": "DB_HOST",
          "value": "guardian-rds.cluster-xyz.us-east-1.rds.amazonaws.com"
        }
      ],
      "logConfiguration": {
        "logDriver": "awslogs",
        "options": {
          "awslogs-group": "/ecs/guardian",
          "awslogs-region": "us-east-1",
          "awslogs-stream-prefix": "ecs"
        }
      }
    }
  ]
}
```

#### Usando RDS para Banco de Dados
```bash
# Criar subnet group
aws rds create-db-subnet-group \
  --db-subnet-group-name guardian-subnet-group \
  --db-subnet-group-description "Guardian DB Subnet Group" \
  --subnet-ids subnet-12345 subnet-67890

# Criar instância RDS PostgreSQL
aws rds create-db-instance \
  --db-instance-identifier guardian-db \
  --db-instance-class db.t3.micro \
  --engine postgres \
  --engine-version 15.3 \
  --master-username guardian_user \
  --master-user-password SuaSenhaSegura123 \
  --allocated-storage 20 \
  --db-subnet-group-name guardian-subnet-group \
  --vpc-security-group-ids sg-guardian-db
```

### Google Cloud Platform

#### Usando Cloud Run
```bash
# Configurar gcloud
gcloud auth login
gcloud config set project seu-projeto-guardian

# Build e deploy
gcloud builds submit --tag gcr.io/seu-projeto-guardian/guardian

# Deploy no Cloud Run
gcloud run deploy guardian \
  --image gcr.io/seu-projeto-guardian/guardian \
  --platform managed \
  --region us-central1 \
  --allow-unauthenticated \
  --memory 1Gi \
  --cpu 1 \
  --max-instances 10
```

#### Cloud SQL PostgreSQL
```bash
# Criar instância Cloud SQL
gcloud sql instances create guardian-db \
  --database-version POSTGRES_15 \
  --tier db-f1-micro \
  --region us-central1

# Criar banco de dados
gcloud sql databases create guardian_db --instance guardian-db

# Criar usuário
gcloud sql users create guardian_user \
  --instance guardian-db \
  --password SuaSenhaSegura123
```

### Azure

#### Usando Azure Container Instances
```bash
# Login no Azure
az login

# Criar resource group
az group create --name guardian-rg --location eastus

# Criar container registry
az acr create --resource-group guardian-rg --name guardianregistry --sku Basic

# Build e push
az acr build --registry guardianregistry --image guardian .

# Deploy container
az container create \
  --resource-group guardian-rg \
  --name guardian-app \
  --image guardianregistry.azurecr.io/guardian \
  --cpu 1 \
  --memory 1 \
  --ports 80 \
  --environment-variables APP_ENV=production
```

## 🖥️ Deploy On-Premise

### Servidor Ubuntu/Debian

#### 1. Preparação do Servidor
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install -y \
  nginx \
  postgresql \
  redis-server \
  php8.2 \
  php8.2-fpm \
  php8.2-cli \
  php8.2-common \
  php8.2-curl \
  php8.2-gd \
  php8.2-mbstring \
  php8.2-mysql \
  php8.2-pgsql \
  php8.2-redis \
  php8.2-xml \
  php8.2-zip \
  composer \
  git \
  unzip

# Configurar firewall
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable
```

#### 2. Configuração do Banco de Dados
```bash
# PostgreSQL
sudo -u postgres psql

CREATE DATABASE guardian_db;
CREATE USER guardian_user WITH PASSWORD 'senha_segura';
GRANT ALL PRIVILEGES ON DATABASE guardian_db TO guardian_user;
\q

# Redis
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Configurar senha Redis
echo "requirepass senha_redis" | sudo tee -a /etc/redis/redis.conf
sudo systemctl restart redis-server
```

#### 3. Deploy da Aplicação
```bash
# Criar diretório
sudo mkdir -p /var/www/guardian
cd /var/www/guardian

# Clone e configuração
sudo git clone https://github.com/empresa/guardian.git .
sudo cp .env.example .env
sudo nano .env

# Instalar dependências
sudo composer install --no-dev --optimize-autoloader

# Configurar permissões
sudo chown -R www-data:www-data /var/www/guardian
sudo chmod -R 755 /var/www/guardian
sudo chmod -R 775 /var/www/guardian/storage
sudo chmod -R 775 /var/www/guardian/bootstrap/cache

# Configurar Laravel
sudo -u www-data php artisan key:generate
sudo -u www-data php artisan migrate:fresh --seed
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

#### 4. Configuração Nginx
```nginx
# /etc/nginx/sites-available/guardian
server {
    listen 80;
    server_name seudominio.com www.seudominio.com;
    root /var/www/guardian/public;
    index index.php index.html;

    access_log /var/log/nginx/guardian_access.log;
    error_log /var/log/nginx/guardian_error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /\. {
        deny all;
    }
}
```

```bash
# Ativar site
sudo ln -s /etc/nginx/sites-available/guardian /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 5. Configuração PHP-FPM
```ini
# /etc/php/8.2/fpm/pool.d/guardian.conf
[guardian]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm-guardian.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10
pm.max_requests = 500

php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
php_admin_value[memory_limit] = 256M
```

### Servidor CentOS/RHEL

#### Adaptações para CentOS
```bash
# Usar yum/dnf ao invés de apt
sudo dnf update -y
sudo dnf install -y nginx postgresql-server redis php php-fpm

# Configurar SELinux
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_execmem 1

# Inicializar PostgreSQL
sudo postgresql-setup --initdb
sudo systemctl enable postgresql
sudo systemctl start postgresql
```

## 📊 Monitoramento em Produção

### Health Checks

#### Script de Monitoramento
```bash
#!/bin/bash
# /scripts/health-check.sh

APP_URL="https://seudominio.com"
EMAIL_ALERT="admin@empresa.com"

# Verificar aplicação
if ! curl -f -s "$APP_URL/health-check" > /dev/null; then
    echo "Guardian está fora do ar!" | mail -s "ALERT: Guardian Down" $EMAIL_ALERT
fi

# Verificar banco de dados
if ! docker-compose exec -T guardian_db pg_isready -U guardian_user; then
    echo "Banco de dados não responde!" | mail -s "ALERT: DB Down" $EMAIL_ALERT
fi

# Verificar Redis
if ! docker-compose exec -T guardian_redis redis-cli ping; then
    echo "Redis não responde!" | mail -s "ALERT: Redis Down" $EMAIL_ALERT
fi

# Verificar espaço em disco
DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 85 ]; then
    echo "Espaço em disco baixo: $DISK_USAGE%" | mail -s "ALERT: Low Disk Space" $EMAIL_ALERT
fi
```

#### Cron para Monitoramento
```bash
# Adicionar ao crontab
crontab -e

# Verificar a cada 5 minutos
*/5 * * * * /scripts/health-check.sh

# Backup diário às 2h
0 2 * * * /scripts/backup.sh

# Limpar logs antigos semanalmente
0 0 * * 0 find /var/log -name "*.log" -mtime +30 -delete
```

### Logs Centralizados

#### Configuração ELK Stack
```yaml
# docker-compose.monitoring.yml
version: '3.8'

services:
  elasticsearch:
    image: elasticsearch:8.5.0
    environment:
      - discovery.type=single-node
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    volumes:
      - elastic_data:/usr/share/elasticsearch/data
    
  logstash:
    image: logstash:8.5.0
    volumes:
      - ./logstash.conf:/usr/share/logstash/pipeline/logstash.conf
      - ./logs:/logs
      
  kibana:
    image: kibana:8.5.0
    ports:
      - "5601:5601"
    environment:
      - ELASTICSEARCH_HOSTS=http://elasticsearch:9200

volumes:
  elastic_data:
```

## 🔧 Automação de Deploy

### GitHub Actions

#### .github/workflows/deploy.yml
```yaml
name: Deploy Guardian

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup Docker Buildx
      uses: docker/setup-buildx-action@v2
      
    - name: Login to Docker Hub
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_USERNAME }}
        password: ${{ secrets.DOCKER_PASSWORD }}
        
    - name: Build and Push
      uses: docker/build-push-action@v3
      with:
        context: .
        file: ./Dockerfile.prod
        push: true
        tags: empresa/guardian:latest
        
    - name: Deploy to Production
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.PROD_HOST }}
        username: ${{ secrets.PROD_USER }}
        key: ${{ secrets.PROD_SSH_KEY }}
        script: |
          cd /var/www/guardian
          docker-compose pull
          docker-compose up -d
          docker-compose exec guardian_app php artisan migrate --force
          docker-compose exec guardian_app php artisan config:cache
```

### Script de Deploy Automatizado

#### deploy.sh
```bash
#!/bin/bash
# Script de deploy automatizado

set -e

# Configurações
REPO_URL="https://github.com/empresa/guardian.git"
DEPLOY_DIR="/var/www/guardian"
BACKUP_DIR="/backups/guardian"

echo "🚀 Iniciando deploy do Guardian..."

# Backup atual
echo "📦 Criando backup..."
mkdir -p $BACKUP_DIR
tar -czf "$BACKUP_DIR/guardian_$(date +%Y%m%d_%H%M%S).tar.gz" -C /var/www guardian

# Atualizar código
echo "📥 Atualizando código..."
cd $DEPLOY_DIR
git pull origin main

# Atualizar dependências
echo "📚 Atualizando dependências..."
composer install --no-dev --optimize-autoloader

# Executar migrations
echo "🗄️ Aplicando migrations..."
php artisan migrate --force

# Limpar caches
echo "🧹 Limpando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar serviços
echo "🔄 Reiniciando serviços..."
if command -v docker-compose &> /dev/null; then
    docker-compose restart guardian_app
else
    sudo systemctl restart php8.2-fpm
    sudo systemctl restart nginx
fi

# Verificar health
echo "🏥 Verificando saúde do sistema..."
sleep 10
if curl -f -s "http://localhost/health-check" > /dev/null; then
    echo "✅ Deploy concluído com sucesso!"
else
    echo "❌ Erro no deploy - aplicação não responde"
    exit 1
fi
```

## 🚨 Troubleshooting

### Problemas Comuns

#### Aplicação não inicia
```bash
# Verificar logs
docker-compose logs guardian_app

# Verificar configurações
docker-compose exec guardian_app php artisan config:show

# Testar conexão com banco
docker-compose exec guardian_app php artisan tinker
# No Tinker: DB::select('SELECT 1');
```

#### Performance Lenta
```bash
# Verificar recursos
docker stats

# Otimizar Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar queries lentas
tail -f storage/logs/laravel.log | grep "slow"
```

#### Problemas de SSL
```bash
# Verificar certificado
openssl x509 -in docker/ssl/fullchain.pem -text -noout

# Testar SSL
curl -I https://seudominio.com

# Renovar Let's Encrypt
certbot renew --dry-run
```

### Recovery de Emergência

#### Rollback Rápido
```bash
# Voltar para versão anterior
git checkout HEAD~1
composer install --no-dev
php artisan migrate:rollback
php artisan config:cache

# Reiniciar serviços
docker-compose restart guardian_app
```

#### Restore de Backup
```bash
# Parar aplicação
docker-compose stop guardian_app

# Restaurar banco
docker-compose exec guardian_db psql -U guardian_user guardian_db < backup.sql

# Restaurar arquivos
tar -xzf guardian_backup.tar.gz -C /var/www/

# Reiniciar
docker-compose start guardian_app
```

---

## 📋 Checklist de Deploy

### Pré-Deploy
- [ ] Backup do ambiente atual
- [ ] Configurações de produção validadas
- [ ] SSL/TLS configurado
- [ ] Monitoramento ativo
- [ ] Plano de rollback definido

### Deploy
- [ ] Código atualizado
- [ ] Dependências instaladas
- [ ] Migrations aplicadas
- [ ] Caches limpos
- [ ] Serviços reiniciados

### Pós-Deploy
- [ ] Health checks passando
- [ ] Performance verificada
- [ ] Logs sem erros
- [ ] Backup pós-deploy
- [ ] Equipe notificada

**Deploy concluído!** 🎉 Seu Guardian está em produção!
