# 🔌 API Documentation - Guardian

Documentação completa da API REST do sistema Guardian.

## 📋 Visão Geral

A API Guardian fornece acesso programático a todas as funcionalidades do sistema, permitindo integração com aplicações externas, desenvolvimento de apps mobile e automação de processos.

### Características da API
- **REST**: Arquitetura RESTful padrão
- **JSON**: Formato de dados exclusivo
- **Autenticação**: Token-based (Bearer Token)
- **Versionamento**: API versionada (`/api/v1/`)
- **Rate Limiting**: Controle de taxa de requisições
- **CORS**: Configurado para cross-origin

### Base URL
```
http://localhost/api/v1/
```

## 🔐 Autenticação

### Obter Token de Acesso

#### POST `/api/v1/auth/login`
Autentica usuário e retorna token de acesso.

**Request:**
```json
{
    "login": "gcosta",
    "password": "guardian123"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Gabriela Costa",
            "login": "gcosta",
            "email": "gabriela@guardian.com",
            "role": "super_admin",
            "is_active": true,
            "avatar": null,
            "created_at": "2024-11-25T10:00:00Z",
            "updated_at": "2024-11-25T15:30:00Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 3600
    },
    "message": "Login realizado com sucesso"
}
```

**Response (401 Unauthorized):**
```json
{
    "success": false,
    "message": "Credenciais inválidas",
    "errors": {
        "credentials": ["Login ou senha incorretos"]
    }
}
```

### Usar Token nas Requisições

Inclua o token no header `Authorization`:

```bash
curl -H "Authorization: Bearer SEU_TOKEN_AQUI" \
     -H "Content-Type: application/json" \
     http://localhost/api/v1/users
```

### Refresh Token

#### POST `/api/v1/auth/refresh`
Renova token de acesso antes de expirar.

**Headers:** `Authorization: Bearer TOKEN_ATUAL`

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

### Logout

#### POST `/api/v1/auth/logout`
Invalida token atual.

**Headers:** `Authorization: Bearer TOKEN`

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Logout realizado com sucesso"
}
```

## 👥 Usuários

### Listar Usuários

#### GET `/api/v1/users`
Retorna lista paginada de usuários.

**Parâmetros de Query:**
- `page` (int): Página atual (padrão: 1)
- `per_page` (int): Itens por página (padrão: 15, máx: 100)
- `search` (string): Buscar por nome, login ou email
- `role` (string): Filtrar por papel (admin, manager, developer, user)
- `is_active` (boolean): Filtrar por status ativo/inativo
- `sort` (string): Campo para ordenação (name, login, created_at)
- `order` (string): Direção (asc, desc)

**Exemplo:**
```bash
GET /api/v1/users?page=1&per_page=10&search=costa&role=admin&sort=name&order=asc
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "users": [
            {
                "id": 1,
                "name": "Gabriela Costa",
                "login": "gcosta",
                "email": "gabriela@guardian.com",
                "role": "super_admin",
                "is_active": true,
                "avatar": null,
                "last_login": "2024-11-25T15:30:00Z",
                "created_at": "2024-11-25T10:00:00Z",
                "updated_at": "2024-11-25T15:30:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 3,
            "per_page": 10,
            "total": 25,
            "from": 1,
            "to": 10
        }
    }
}
```

### Obter Usuário Específico

#### GET `/api/v1/users/{id}`
Retorna dados completos de um usuário.

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Gabriela Costa",
            "login": "gcosta",
            "email": "gabriela@guardian.com",
            "role": "super_admin",
            "is_active": true,
            "avatar": "https://localhost/storage/avatars/gcosta.jpg",
            "bio": "Super administradora do sistema",
            "phone": "(11) 99999-9999",
            "timezone": "America/Sao_Paulo",
            "language": "pt-BR",
            "last_login": "2024-11-25T15:30:00Z",
            "login_count": 245,
            "created_at": "2024-11-25T10:00:00Z",
            "updated_at": "2024-11-25T15:30:00Z",
            "projects_count": 8,
            "tasks_count": 23
        }
    }
}
```

### Criar Usuário

#### POST `/api/v1/users`
Cria novo usuário no sistema.

**Request:**
```json
{
    "name": "João Pedro Santos",
    "email": "joao@empresa.com",
    "role": "developer",
    "is_active": true,
    "password": "senha123",
    "bio": "Desenvolvedor sênior",
    "phone": "(11) 88888-8888"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 21,
            "name": "João Pedro Santos",
            "login": "jsantos",
            "email": "joao@empresa.com",
            "role": "developer",
            "is_active": true,
            "created_at": "2024-11-25T16:00:00Z"
        }
    },
    "message": "Usuário criado com sucesso. Login gerado: jsantos"
}
```

**Response (422 Validation Error):**
```json
{
    "success": false,
    "message": "Dados inválidos",
    "errors": {
        "email": ["Este email já está sendo usado"],
        "name": ["O nome é obrigatório"],
        "password": ["A senha deve ter pelo menos 8 caracteres"]
    }
}
```

### Atualizar Usuário

#### PUT `/api/v1/users/{id}`
Atualiza dados de um usuário existente.

**Request:**
```json
{
    "name": "João Pedro Silva Santos",
    "bio": "Desenvolvedor sênior especialista em Laravel",
    "phone": "(11) 77777-7777",
    "is_active": true
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 21,
            "name": "João Pedro Silva Santos",
            "login": "jsantos",
            "bio": "Desenvolvedor sênior especialista em Laravel",
            "phone": "(11) 77777-7777",
            "updated_at": "2024-11-25T16:15:00Z"
        }
    },
    "message": "Usuário atualizado com sucesso"
}
```

### Deletar Usuário

#### DELETE `/api/v1/users/{id}`
Remove usuário do sistema (soft delete).

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Usuário removido com sucesso"
}
```

## 📊 Projetos

### Listar Projetos

#### GET `/api/v1/projects`
Retorna lista paginada de projetos.

**Parâmetros de Query:**
- `page`, `per_page`: Paginação
- `search`: Buscar por nome ou descrição
- `status`: Filtrar por status (planning, active, paused, completed, cancelled)
- `priority`: Filtrar por prioridade (low, medium, high, critical)
- `owner_id`: Filtrar por responsável
- `sort`: Ordenar por (name, created_at, deadline, status)
- `order`: Direção (asc, desc)

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "projects": [
            {
                "id": 1,
                "name": "Sistema Guardian",
                "description": "Desenvolvimento do sistema de gestão completo",
                "status": "active",
                "priority": "high",
                "progress": 75,
                "start_date": "2024-11-01",
                "deadline": "2024-12-31",
                "owner": {
                    "id": 1,
                    "name": "Gabriela Costa",
                    "login": "gcosta"
                },
                "team_count": 5,
                "tasks_count": 23,
                "completed_tasks": 17,
                "created_at": "2024-11-01T09:00:00Z",
                "updated_at": "2024-11-25T16:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total": 12
        }
    }
}
```

### Obter Projeto Específico

#### GET `/api/v1/projects/{id}`
Retorna dados completos do projeto incluindo estatísticas.

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "project": {
            "id": 1,
            "name": "Sistema Guardian",
            "description": "Desenvolvimento do sistema de gestão completo",
            "status": "active",
            "priority": "high",
            "progress": 75,
            "start_date": "2024-11-01",
            "deadline": "2024-12-31",
            "budget": 150000.00,
            "spent": 112500.00,
            "owner": {
                "id": 1,
                "name": "Gabriela Costa",
                "login": "gcosta",
                "email": "gabriela@guardian.com"
            },
            "team": [
                {
                    "id": 2,
                    "name": "Ana Ferreira",
                    "login": "aferreira",
                    "role": "manager"
                },
                {
                    "id": 3,
                    "name": "Lucas Almeida",
                    "login": "lalmeida",
                    "role": "developer"
                }
            ],
            "statistics": {
                "tasks_total": 23,
                "tasks_completed": 17,
                "tasks_in_progress": 4,
                "tasks_pending": 2,
                "completion_rate": 73.91,
                "days_remaining": 36,
                "team_size": 5
            },
            "created_at": "2024-11-01T09:00:00Z",
            "updated_at": "2024-11-25T16:00:00Z"
        }
    }
}
```

### Criar Projeto

#### POST `/api/v1/projects`
Cria novo projeto.

**Request:**
```json
{
    "name": "App Mobile Guardian",
    "description": "Desenvolvimento do aplicativo mobile do Guardian",
    "status": "planning",
    "priority": "medium",
    "start_date": "2024-12-01",
    "deadline": "2025-03-31",
    "budget": 80000.00,
    "owner_id": 2,
    "team_ids": [2, 3, 4, 5]
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "project": {
            "id": 13,
            "name": "App Mobile Guardian",
            "status": "planning",
            "priority": "medium",
            "owner": {
                "id": 2,
                "name": "Ana Ferreira"
            },
            "created_at": "2024-11-25T16:30:00Z"
        }
    },
    "message": "Projeto criado com sucesso"
}
```

## 📋 Tarefas

### Listar Tarefas

#### GET `/api/v1/tasks`
Retorna lista paginada de tarefas.

**Parâmetros de Query:**
- `project_id`: Filtrar por projeto
- `assigned_to`: Filtrar por responsável
- `status`: Filtrar por status (pending, in_progress, review, completed, blocked)
- `priority`: Filtrar por prioridade
- `due_date_from`, `due_date_to`: Filtrar por prazo

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "tasks": [
            {
                "id": 1,
                "title": "Implementar sistema de login único",
                "description": "Desenvolver funcionalidade de login único baseado em nome",
                "status": "completed",
                "priority": "high",
                "progress": 100,
                "due_date": "2024-11-25",
                "completed_at": "2024-11-25T14:30:00Z",
                "project": {
                    "id": 1,
                    "name": "Sistema Guardian"
                },
                "assigned_to": {
                    "id": 3,
                    "name": "Lucas Almeida",
                    "login": "lalmeida"
                },
                "created_by": {
                    "id": 2,
                    "name": "Ana Ferreira",
                    "login": "aferreira"
                },
                "estimated_hours": 16,
                "actual_hours": 14,
                "created_at": "2024-11-20T09:00:00Z",
                "updated_at": "2024-11-25T14:30:00Z"
            }
        ]
    }
}
```

### Criar Tarefa

#### POST `/api/v1/tasks`
Cria nova tarefa.

**Request:**
```json
{
    "title": "Configurar monitoramento Docker",
    "description": "Implementar health checks e monitoramento de containers",
    "project_id": 1,
    "assigned_to": 4,
    "status": "pending",
    "priority": "medium",
    "due_date": "2024-12-05",
    "estimated_hours": 8,
    "dependencies": [2, 5]
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "task": {
            "id": 24,
            "title": "Configurar monitoramento Docker",
            "status": "pending",
            "priority": "medium",
            "due_date": "2024-12-05",
            "project": {
                "id": 1,
                "name": "Sistema Guardian"
            },
            "assigned_to": {
                "id": 4,
                "name": "Bruno Santos"
            },
            "created_at": "2024-11-25T17:00:00Z"
        }
    },
    "message": "Tarefa criada com sucesso"
}
```

### Atualizar Status da Tarefa

#### PATCH `/api/v1/tasks/{id}/status`
Atualiza apenas o status de uma tarefa.

**Request:**
```json
{
    "status": "in_progress",
    "comment": "Iniciando implementação dos health checks"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "task": {
            "id": 24,
            "status": "in_progress",
            "updated_at": "2024-11-25T17:15:00Z"
        }
    },
    "message": "Status da tarefa atualizado"
}
```

## 📊 Relatórios

### Dashboard Stats

#### GET `/api/v1/dashboard/stats`
Retorna estatísticas para dashboard.

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "overview": {
            "total_projects": 12,
            "active_projects": 8,
            "total_tasks": 145,
            "completed_tasks": 98,
            "total_users": 25,
            "active_users": 23
        },
        "projects_by_status": {
            "planning": 2,
            "active": 8,
            "paused": 1,
            "completed": 1,
            "cancelled": 0
        },
        "tasks_by_priority": {
            "low": 25,
            "medium": 67,
            "high": 42,
            "critical": 11
        },
        "productivity": {
            "completion_rate": 67.58,
            "average_task_time": 12.5,
            "overdue_tasks": 8,
            "this_week_completed": 15
        },
        "recent_activity": [
            {
                "id": 1,
                "action": "task_completed",
                "description": "Lucas Almeida completou 'Implementar sistema de login único'",
                "user": {
                    "name": "Lucas Almeida",
                    "login": "lalmeida"
                },
                "timestamp": "2024-11-25T14:30:00Z"
            }
        ]
    }
}
```

### Relatório de Projetos

#### GET `/api/v1/reports/projects`
Relatório detalhado de projetos.

**Parâmetros de Query:**
- `date_from`, `date_to`: Período do relatório
- `status`: Filtrar por status
- `owner_id`: Filtrar por responsável
- `export`: Formato de exportação (json, csv, pdf)

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "summary": {
            "total_projects": 8,
            "total_budget": 650000.00,
            "total_spent": 425000.00,
            "average_completion": 73.5,
            "on_time_projects": 6,
            "delayed_projects": 2
        },
        "projects": [
            {
                "id": 1,
                "name": "Sistema Guardian",
                "completion": 75,
                "budget": 150000.00,
                "spent": 112500.00,
                "days_remaining": 36,
                "status": "active",
                "team_size": 5,
                "tasks_completed": 17,
                "tasks_total": 23
            }
        ]
    }
}
```

## 🔍 Busca

### Busca Global

#### GET `/api/v1/search`
Busca em todas as entidades do sistema.

**Parâmetros de Query:**
- `q` (string, obrigatório): Termo de busca
- `type`: Tipo de entidade (users, projects, tasks, all)
- `limit`: Número máximo de resultados por tipo

**Exemplo:**
```bash
GET /api/v1/search?q=guardian&type=all&limit=5
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "users": [
            {
                "id": 1,
                "name": "Gabriela Costa",
                "login": "gcosta",
                "type": "user",
                "match": "Email contains 'guardian'"
            }
        ],
        "projects": [
            {
                "id": 1,
                "name": "Sistema Guardian",
                "description": "Desenvolvimento do sistema...",
                "type": "project",
                "match": "Name contains 'guardian'"
            }
        ],
        "tasks": [
            {
                "id": 15,
                "title": "Configurar backup Guardian",
                "project_name": "Sistema Guardian",
                "type": "task",
                "match": "Title contains 'guardian'"
            }
        ],
        "total_results": 8
    }
}
```

## 📁 Upload de Arquivos

### Upload de Avatar

#### POST `/api/v1/users/{id}/avatar`
Faz upload de avatar do usuário.

**Request:** `multipart/form-data`
- `avatar`: Arquivo de imagem (jpg, png, gif, max: 2MB)

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "avatar_url": "https://localhost/storage/avatars/gcosta_1732557600.jpg"
    },
    "message": "Avatar atualizado com sucesso"
}
```

### Upload de Anexos

#### POST `/api/v1/tasks/{id}/attachments`
Adiciona anexos a uma tarefa.

**Request:** `multipart/form-data`
- `files[]`: Array de arquivos (max: 10MB cada)
- `description`: Descrição opcional dos arquivos

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "attachments": [
            {
                "id": 1,
                "filename": "wireframe.pdf",
                "original_name": "Wireframe - Sistema Guardian.pdf",
                "size": 2048576,
                "mime_type": "application/pdf",
                "url": "https://localhost/storage/attachments/wireframe_1732557600.pdf",
                "uploaded_by": {
                    "id": 3,
                    "name": "Lucas Almeida"
                },
                "uploaded_at": "2024-11-25T17:30:00Z"
            }
        ]
    },
    "message": "Arquivos enviados com sucesso"
}
```

## 🚨 Códigos de Status HTTP

### Success (2xx)
- `200 OK`: Requisição processada com sucesso
- `201 Created`: Recurso criado com sucesso
- `204 No Content`: Operação bem-sucedida sem conteúdo

### Client Error (4xx)
- `400 Bad Request`: Dados da requisição inválidos
- `401 Unauthorized`: Token inválido ou expirado
- `403 Forbidden`: Sem permissão para o recurso
- `404 Not Found`: Recurso não encontrado
- `422 Unprocessable Entity`: Erro de validação
- `429 Too Many Requests`: Rate limit excedido

### Server Error (5xx)
- `500 Internal Server Error`: Erro interno do servidor
- `503 Service Unavailable`: Serviço temporariamente indisponível

## 📊 Rate Limiting

### Limites por Endpoint
- **Autenticação**: 5 tentativas por minuto
- **API Geral**: 60 requests por minuto por usuário
- **Upload**: 10 uploads por minuto
- **Relatórios**: 20 requests por minuto

### Headers de Rate Limit
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1732558200
```

## 🔧 Códigos de Exemplo

### JavaScript (Fetch)
```javascript
// Função helper para API
async function guardianAPI(endpoint, options = {}) {
    const token = localStorage.getItem('guardian_token');
    
    const config = {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...options.headers
        },
        ...options
    };
    
    const response = await fetch(`/api/v1${endpoint}`, config);
    const data = await response.json();
    
    if (!response.ok) {
        throw new Error(data.message || 'Erro na API');
    }
    
    return data;
}

// Usar a função
try {
    const users = await guardianAPI('/users?page=1&per_page=10');
    console.log(users.data.users);
} catch (error) {
    console.error('Erro:', error.message);
}
```

### PHP (cURL)
```php
<?php
class GuardianAPI {
    private $baseUrl;
    private $token;
    
    public function __construct($baseUrl, $token) {
        $this->baseUrl = rtrim($baseUrl, '/') . '/api/v1';
        $this->token = $token;
    }
    
    public function request($method, $endpoint, $data = null) {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ]
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $decoded = json_decode($response, true);
        
        if ($httpCode >= 400) {
            throw new Exception($decoded['message'] ?? 'Erro na API');
        }
        
        return $decoded;
    }
    
    public function getUsers($params = []) {
        $query = http_build_query($params);
        return $this->request('GET', '/users?' . $query);
    }
}

// Usar a classe
$api = new GuardianAPI('http://localhost', 'SEU_TOKEN');
$users = $api->getUsers(['page' => 1, 'per_page' => 10]);
```

### Python (Requests)
```python
import requests
import json

class GuardianAPI:
    def __init__(self, base_url, token):
        self.base_url = base_url.rstrip('/') + '/api/v1'
        self.headers = {
            'Content-Type': 'application/json',
            'Authorization': f'Bearer {token}'
        }
    
    def request(self, method, endpoint, data=None):
        url = self.base_url + endpoint
        
        response = requests.request(
            method=method,
            url=url,
            headers=self.headers,
            json=data if data else None
        )
        
        if response.status_code >= 400:
            error_data = response.json()
            raise Exception(error_data.get('message', 'Erro na API'))
        
        return response.json()
    
    def get_users(self, **params):
        query = '&'.join([f'{k}={v}' for k, v in params.items()])
        return self.request('GET', f'/users?{query}')

# Usar a classe
api = GuardianAPI('http://localhost', 'SEU_TOKEN')
users = api.get_users(page=1, per_page=10)
```

---

## 🎯 Próximos Passos

1. **Teste as APIs** com Postman ou Insomnia
2. **Implemente autenticação** em sua aplicação
3. **Configure webhooks** para notificações em tempo real
4. **Use paginação** para listas grandes
5. **Implemente cache** para melhor performance

Para mais detalhes sobre implementação, consulte:
- [Guia de Instalação](../manual/instalacao.md)
- [Manual de Configuração](../manual/configuracao.md)
- [Documentação de Deploy](../deployment/docker.md)

**API pronta para integração!** 🚀
