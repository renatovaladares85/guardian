# 🏗️ Arquitetura do Sistema - Guardian

Documentação técnica completa da arquitetura do sistema Guardian.

## 📋 Visão Geral

O Guardian é um sistema de gestão empresarial construído com arquitetura moderna, escalável e orientada a microserviços. Utiliza tecnologias consolidadas para garantir performance, segurança e facilidade de manutenção.

### Características Arquiteturais
- **Padrão MVC**: Model-View-Controller com Laravel
- **Containerização**: Docker para isolamento e portabilidade
- **Cache Distribuído**: Redis para performance
- **Banco Relacional**: PostgreSQL para integridade de dados
- **API-First**: Separação entre backend e frontend
- **Monitoramento**: Health checks e logs centralizados

## 🛠️ Stack Tecnológica

### Backend
- **Framework**: Laravel 11.x
- **Linguagem**: PHP 8.2+
- **Servidor Web**: Apache/Nginx
- **API**: RESTful JSON

### Banco de Dados
- **Principal**: PostgreSQL 15
- **Cache/Session**: Redis 7
- **Migrações**: Laravel Migrations
- **Seeding**: Factory + Seeders

### Frontend
- **CSS Framework**: Bootstrap 5
- **JavaScript**: Vanilla JS + AJAX
- **Build Tool**: Laravel Mix/Vite
- **Templates**: Blade Templates

### Infraestrutura
- **Containerização**: Docker + Docker Compose
- **Proxy Reverso**: Nginx
- **Monitoramento**: Custom Health Checks
- **Backup**: Automated PostgreSQL dumps
- **Logs**: Laravel Log + Custom channels

## 🏗️ Arquitetura de Alto Nível

```
┌─────────────────────────────────────────────────────────────┐
│                     INTERNET                                │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│                 NGINX PROXY                                 │
│            (Load Balancer + SSL)                           │
└─────────────────────┬───────────────────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────────────────┐
│               GUARDIAN APP                                  │
│              (Laravel 11)                                  │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │ Controllers │ │   Models    │ │    Views    │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐          │
│  │ Middleware  │ │  Services   │ │   Routes    │          │
│  └─────────────┘ └─────────────┘ └─────────────┘          │
└─────────────┬───────────────┬───────────────────────────────┘
              │               │
    ┌─────────▼─────────┐   ┌─▼─────────────────┐
    │   PostgreSQL      │   │      Redis        │
    │   (Database)      │   │   (Cache/Session) │
    └───────────────────┘   └───────────────────┘
```

## 📦 Estrutura de Containers

### Container: guardian_app
```
Função: Aplicação Laravel principal
Base: php:8.2-apache
Portas: 80
Volumes: 
  - ./storage (persistente)
  - ./bootstrap/cache (cache de aplicação)
Dependências: guardian_db, guardian_redis
```

### Container: guardian_db  
```
Função: Banco de dados PostgreSQL
Base: postgres:15-alpine
Portas: 5432 (interna)
Volumes:
  - guardian_db_data (dados)
  - ./database/backups (backups)
Environment: 
  - POSTGRES_DB, POSTGRES_USER, POSTGRES_PASSWORD
```

### Container: guardian_redis
```
Função: Cache e sessões
Base: redis:7-alpine
Portas: 6379 (interna)
Volumes: 
  - guardian_redis_data (persistente)
Command: redis-server --requirepass senha
```

### Container: guardian_nginx
```
Função: Proxy reverso e load balancer
Base: nginx:alpine
Portas: 80, 443
Volumes:
  - ./docker/nginx/conf.d (configurações)
  - ./docker/ssl (certificados SSL)
Dependências: guardian_app
```

### Containers de Suporte

#### guardian_watchtower
```
Função: Auto-update de containers
Monitora: Novas versões das imagens
Schedule: A cada 24 horas
```

#### guardian_healthcheck
```
Função: Monitoramento de saúde
Verifica: App, DB, Redis, Nginx
Frequência: A cada 30 segundos
```

#### guardian_backup
```
Função: Backup automático
Schedule: A cada 6 horas
Retenção: 7 dias
Formato: PostgreSQL dump comprimido
```

## 🔄 Fluxo de Dados

### Requisição HTTP
```
1. Cliente → Nginx (SSL termination, rate limiting)
2. Nginx → Guardian App (proxy_pass)
3. Apache → Laravel Router
4. Router → Controller
5. Controller → Service/Model
6. Model → PostgreSQL/Redis
7. Resposta invertida
```

### Autenticação
```
1. POST /api/v1/auth/login
2. Validação credenciais (login/email + senha)
3. Geração JWT token
4. Armazenamento em Redis (sessão)
5. Retorno token para cliente
6. Requisições subsequentes com Authorization: Bearer
```

### Cache Strategy
```
Cache Levels:
1. Redis Application Cache (dados frequentes)
2. Laravel Config/Route Cache (arquivos de configuração)
3. Nginx Static Cache (assets estáticos)
4. PostgreSQL Query Cache (queries repetitivas)
```

## 🗄️ Modelagem de Dados

### Principais Entidades

#### Users (Usuários)
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    login VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    avatar VARCHAR(255),
    bio TEXT,
    phone VARCHAR(20),
    timezone VARCHAR(50) DEFAULT 'America/Sao_Paulo',
    language VARCHAR(10) DEFAULT 'pt-BR',
    last_login TIMESTAMP,
    login_count INTEGER DEFAULT 0,
    email_verified_at TIMESTAMP,
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP
);

-- Índices de performance
CREATE INDEX idx_users_login ON users(login);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_active_role ON users(is_active, role);
```

#### Projects (Projetos)
```sql
CREATE TABLE projects (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) NOT NULL,
    priority VARCHAR(50) NOT NULL,
    progress INTEGER DEFAULT 0,
    start_date DATE,
    deadline DATE,
    budget DECIMAL(15,2),
    spent DECIMAL(15,2) DEFAULT 0,
    owner_id INTEGER REFERENCES users(id),
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP
);

CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_projects_owner ON projects(owner_id);
CREATE INDEX idx_projects_deadline ON projects(deadline);
```

#### Tasks (Tarefas)
```sql
CREATE TABLE tasks (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) NOT NULL,
    priority VARCHAR(50) NOT NULL,
    progress INTEGER DEFAULT 0,
    due_date DATE,
    completed_at TIMESTAMP,
    project_id INTEGER REFERENCES projects(id),
    assigned_to INTEGER REFERENCES users(id),
    created_by INTEGER REFERENCES users(id),
    estimated_hours INTEGER,
    actual_hours INTEGER,
    parent_task_id INTEGER REFERENCES tasks(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP
);

CREATE INDEX idx_tasks_project_id ON tasks(project_id);
CREATE INDEX idx_tasks_assigned_to ON tasks(assigned_to);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_tasks_due_date ON tasks(due_date);
CREATE INDEX idx_tasks_project_status ON tasks(project_id, status);
```

#### Relacionamentos Muitos-para-Muitos

##### project_user (Equipe dos Projetos)
```sql
CREATE TABLE project_user (
    id SERIAL PRIMARY KEY,
    project_id INTEGER REFERENCES projects(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    role VARCHAR(50) NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(project_id, user_id)
);
```

##### task_dependencies (Dependências de Tarefas)
```sql
CREATE TABLE task_dependencies (
    id SERIAL PRIMARY KEY,
    task_id INTEGER REFERENCES tasks(id) ON DELETE CASCADE,
    depends_on_task_id INTEGER REFERENCES tasks(id) ON DELETE CASCADE,
    dependency_type VARCHAR(50) DEFAULT 'finish_to_start',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(task_id, depends_on_task_id)
);
```

### Auditoria e Logs

#### audit_logs (Logs de Auditoria)
```sql
CREATE TABLE audit_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100),
    record_id INTEGER,
    old_values JSONB,
    new_values JSONB,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);
```

## 🔐 Arquitetura de Segurança

### Autenticação e Autorização

#### JWT Token Flow
```
1. Login Request → Validate Credentials
2. Generate JWT Token (expire: 1h)
3. Store Token in Redis (key: user:id:token)
4. Return Token to Client
5. Client sends: Authorization: Bearer {token}
6. Middleware validates token
7. Load user context from cache/DB
```

#### Role-Based Access Control (RBAC)
```php
// Hierarquia de Papéis
'roles' => [
    'super_admin' => [
        'level' => 5,
        'permissions' => ['*'] // Todas as permissões
    ],
    'admin' => [
        'level' => 4,
        'permissions' => ['users.*', 'projects.*', 'reports.*']
    ],
    'manager' => [
        'level' => 3,
        'permissions' => ['projects.create', 'projects.edit', 'tasks.*', 'users.view']
    ],
    'developer' => [
        'level' => 2,
        'permissions' => ['tasks.edit', 'projects.view', 'profile.*']
    ],
    'user' => [
        'level' => 1,
        'permissions' => ['tasks.view', 'profile.*']
    ]
];
```

### Middleware de Segurança

#### Rate Limiting
```php
// Configuração de Rate Limiting
'rate_limits' => [
    'api' => '60,1',           // 60 requests/min
    'auth' => '5,1',           // 5 login attempts/min
    'heavy_operations' => '10,1', // 10 operations/min
],

// Implementação
Route::middleware(['throttle:api'])->group(function () {
    Route::apiResource('users', UserController::class);
});
```

#### Input Validation
```php
// Request Validation Classes
class CreateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,manager,developer,user',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        ];
    }
}
```

### Headers de Segurança
```nginx
# Nginx Security Headers
add_header X-Frame-Options "DENY" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

## 📊 Performance e Escalabilidade

### Cache Strategy

#### Níveis de Cache
```
1. Application Level (Redis)
   - User sessions
   - Frequently accessed data
   - API responses
   - Database query results

2. HTTP Level (Nginx)
   - Static assets (CSS, JS, images)
   - API responses with cache headers
   - Gzip compression

3. Database Level (PostgreSQL)
   - Query plan caching
   - Connection pooling
   - Index optimization
```

#### Cache Implementation
```php
// Service Layer Caching
class ProjectService
{
    public function getProjectStats($projectId)
    {
        return Cache::tags(['projects', "project:{$projectId}"])
            ->remember("project_stats:{$projectId}", 1800, function () use ($projectId) {
                return $this->calculateProjectStats($projectId);
            });
    }
    
    public function invalidateProjectCache($projectId)
    {
        Cache::tags(["project:{$projectId}"])->flush();
    }
}
```

### Database Optimization

#### Indexing Strategy
```sql
-- Índices para queries mais frequentes
CREATE INDEX CONCURRENTLY idx_tasks_project_status_assigned 
ON tasks(project_id, status, assigned_to);

CREATE INDEX CONCURRENTLY idx_audit_logs_user_date 
ON audit_logs(user_id, created_at DESC);

CREATE INDEX CONCURRENTLY idx_users_active_role_created 
ON users(is_active, role, created_at DESC) 
WHERE is_active = true;
```

#### Query Optimization
```php
// Eager Loading para evitar N+1
$projects = Project::with(['owner', 'team', 'tasks.assignee'])
    ->where('status', 'active')
    ->orderBy('deadline')
    ->paginate(15);

// Query específica com joins
$projectStats = DB::table('projects as p')
    ->leftJoin('tasks as t', 'p.id', '=', 't.project_id')
    ->select([
        'p.id',
        'p.name',
        DB::raw('COUNT(t.id) as total_tasks'),
        DB::raw('COUNT(CASE WHEN t.status = "completed" THEN 1 END) as completed_tasks'),
        DB::raw('ROUND(AVG(t.progress), 2) as avg_progress')
    ])
    ->groupBy('p.id', 'p.name')
    ->get();
```

### Horizontal Scaling

#### Load Balancing
```nginx
# nginx.conf - Load Balancer
upstream guardian_backend {
    least_conn;
    server guardian_app_1:80 max_fails=3 fail_timeout=30s;
    server guardian_app_2:80 max_fails=3 fail_timeout=30s;
    server guardian_app_3:80 max_fails=3 fail_timeout=30s;
    
    # Health check
    keepalive 32;
}

server {
    location / {
        proxy_pass http://guardian_backend;
        proxy_next_upstream error timeout invalid_header http_500 http_502 http_503;
    }
}
```

#### Session Affinity
```php
// Redis Session Configuration
'session' => [
    'driver' => 'redis',
    'connection' => 'session',
    'table' => 'sessions',
    'store' => 'redis',
    'lottery' => [2, 100],
    'cookie' => 'guardian_session',
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null),
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'lax',
],
```

## 🔍 Monitoramento e Observabilidade

### Health Checks

#### Application Health
```php
// Health Check Endpoint
Route::get('/health-check', function () {
    $health = [
        'status' => 'healthy',
        'timestamp' => now(),
        'checks' => []
    ];

    // Database Check
    try {
        DB::select('SELECT 1');
        $health['checks']['database'] = 'healthy';
    } catch (Exception $e) {
        $health['checks']['database'] = 'unhealthy';
        $health['status'] = 'unhealthy';
    }

    // Redis Check
    try {
        Redis::ping();
        $health['checks']['redis'] = 'healthy';
    } catch (Exception $e) {
        $health['checks']['redis'] = 'unhealthy';
        $health['status'] = 'unhealthy';
    }

    // Disk Space Check
    $freeSpace = disk_free_space('/');
    $totalSpace = disk_total_space('/');
    $usagePercent = (1 - $freeSpace / $totalSpace) * 100;
    
    $health['checks']['disk_space'] = $usagePercent < 90 ? 'healthy' : 'warning';
    
    return response()->json($health);
});
```

#### Container Health Checks
```yaml
# docker-compose.yml health checks
services:
  guardian_app:
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost/health-check"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
      
  guardian_db:
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U guardian_user"]
      interval: 30s
      timeout: 5s
      retries: 3
      
  guardian_redis:
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 30s
      timeout: 3s
      retries: 3
```

### Logging Strategy

#### Structured Logging
```php
// config/logging.php
'channels' => [
    'guardian' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
    ],
    
    'audit' => [
        'driver' => 'daily',
        'path' => storage_path('logs/audit.log'),
        'level' => 'info',
        'days' => 90,
        'formatter' => \Monolog\Formatter\JsonFormatter::class,
    ],
    
    'performance' => [
        'driver' => 'daily',
        'path' => storage_path('logs/performance.log'),
        'level' => 'debug',
        'days' => 30,
    ],
];

// Usage
Log::channel('audit')->info('User logged in', [
    'user_id' => auth()->id(),
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

#### Metrics Collection
```php
// Custom Metrics Middleware
class MetricsMiddleware
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        
        Log::channel('performance')->debug('Request metrics', [
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'status' => $response->getStatusCode(),
            'duration' => $duration,
            'memory' => memory_get_peak_usage(true),
        ]);
        
        return $response;
    }
}
```

## 🚀 Deploy e CI/CD

### Build Process

#### Dockerfile.prod
```dockerfile
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/
RUN a2enmod rewrite

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80
```

### CI/CD Pipeline

#### GitHub Actions Workflow
```yaml
# .github/workflows/ci-cd.yml
name: Guardian CI/CD

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      postgres:
        image: postgres:15
        env:
          POSTGRES_PASSWORD: postgres
          POSTGRES_DB: guardian_test
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
          
      redis:
        image: redis:7
        options: >-
          --health-cmd "redis-cli ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.2
        extensions: mbstring, xml, ctype, iconv, intl, pdo_pgsql, redis
        
    - name: Copy .env
      run: cp .env.github .env
      
    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress
      
    - name: Generate key
      run: php artisan key:generate
      
    - name: Run Tests
      run: php artisan test --coverage
      
    - name: Run PHPStan
      run: ./vendor/bin/phpstan analyse
      
  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
    - name: Deploy to Production
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.PROD_HOST }}
        username: ${{ secrets.PROD_USER }}
        key: ${{ secrets.PROD_SSH_KEY }}
        script: |
          cd /var/www/guardian
          git pull origin main
          docker-compose -f docker-compose.prod.yml up -d --build
          docker-compose exec guardian_app php artisan migrate --force
          docker-compose exec guardian_app php artisan optimize
```

## 🔧 Manutenção e Evolução

### Database Migrations

#### Versionamento de Schema
```php
// Migration Example: Add user preferences
class AddUserPreferencesTable extends Migration
{
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->json('value');
            $table->timestamps();
            
            $table->unique(['user_id', 'key']);
            $table->index(['user_id', 'key']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('user_preferences');
    }
}
```

### Feature Flags

#### Implementação de Feature Toggles
```php
// Feature Flag Service
class FeatureFlag
{
    public static function isEnabled(string $feature, ?User $user = null): bool
    {
        $flags = Cache::remember('feature_flags', 300, function () {
            return config('features', []);
        });
        
        if (!isset($flags[$feature])) {
            return false;
        }
        
        $flag = $flags[$feature];
        
        // Global flag
        if (is_bool($flag)) {
            return $flag;
        }
        
        // User-specific flags
        if ($user && isset($flag['users'])) {
            return in_array($user->id, $flag['users']);
        }
        
        // Role-based flags
        if ($user && isset($flag['roles'])) {
            return in_array($user->role, $flag['roles']);
        }
        
        return $flag['enabled'] ?? false;
    }
}

// Usage
if (FeatureFlag::isEnabled('new_dashboard', auth()->user())) {
    return view('dashboard.v2');
}
```

### API Versioning

#### Estratégia de Versionamento
```php
// routes/api/v1.php
Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('projects', ProjectController::class);
});

// routes/api/v2.php
Route::prefix('v2')->group(function () {
    Route::apiResource('users', V2\UserController::class);
    Route::apiResource('projects', V2\ProjectController::class);
});

// Versioning Middleware
class ApiVersionMiddleware
{
    public function handle($request, Closure $next, $version)
    {
        config(['app.api_version' => $version]);
        
        return $next($request);
    }
}
```

---

## 📚 Conclusão

A arquitetura do Guardian foi projetada para ser:

- **Escalável**: Suporta crescimento horizontal e vertical
- **Maintível**: Código limpo e bem estruturado
- **Segura**: Múltiplas camadas de segurança
- **Performática**: Cache inteligente e otimizações
- **Observável**: Logs e métricas detalhadas
- **Resiliente**: Health checks e recovery automático

Esta documentação deve ser atualizada conforme a evolução do sistema, mantendo sempre a sincronia entre código e documentação.

**Arquitetura documentada!** 🏗️ Sistema robusto e bem estruturado.
