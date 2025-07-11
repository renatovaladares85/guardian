## 🚀 Guardian MVP - Guia de Teste

### Como Iniciar o Sistema

#### Opção 1: Script Automático (Recomendado)
```bash
# No Windows
.\start-mvp.bat

# No Linux/Mac
chmod +x start-mvp.sh
./start-mvp.sh
```

#### Opção 2: Comandos Manuais
```bash
# Iniciar containers
docker-compose up -d

# Aguardar 15 segundos para o banco ficar pronto
# Configurar aplicação
docker-compose exec guardian_app php artisan guardian:setup-mvp
```

---

### 🌐 Acessos do Sistema

- **Aplicação Principal:** http://localhost:8000
- **MailHog (Email Testing):** http://localhost:8025
- **Banco PostgreSQL:** localhost:5432 (guardian_db)
- **Redis:** localhost:6379

---

### 👥 Usuários de Teste

| Email | Senha | Função | Descrição |
|-------|-------|--------|-----------|
| `admin@guardian.local` | `guardian123` | Super Admin | Acesso total ao sistema |
| `joao@guardian.local` | `guardian123` | Gerente de Projetos | Pode criar/gerenciar projetos |
| `maria@guardian.local` | `guardian123` | Líder de Equipe | Gerencia equipes e tarefas |
| `pedro@guardian.local` | `guardian123` | Desenvolvedor | Membro da equipe |
| `ana@guardian.local` | `guardian123` | Desenvolvedora | Membro da equipe |

---

### 🎯 Funcionalidades do MVP Para Testar

#### 1. **Autenticação**
- [x] Login/Logout
- [x] Controle de acesso por função
- [x] Registro de novos usuários

#### 2. **Dashboard**
- [x] Visão geral de estatísticas
- [x] Projetos recentes
- [x] Minhas tarefas
- [x] Tarefas vencidas

#### 3. **Gerenciamento de Projetos**
- [x] Listar projetos
- [x] Criar novo projeto
- [x] Visualizar detalhes do projeto
- [x] Editar projeto
- [x] Adicionar membros
- [x] Excluir projeto

#### 4. **Gerenciamento de Tarefas**
- [x] Listar tarefas
- [x] Criar nova tarefa
- [x] Atribuir tarefa a usuários
- [x] Alterar status da tarefa
- [x] Editar tarefa
- [x] Filtros por status, projeto, usuário

---

### 🧪 Roteiro de Teste Sugerido

#### 1. **Teste de Login**
1. Acesse http://localhost:8000
2. Faça login com `admin@guardian.local` / `guardian123`
3. Verifique se o dashboard carrega corretamente

#### 2. **Teste de Projetos**
1. Vá para "Projetos" no menu
2. Observe o projeto "Sistema Guardian MVP" já criado
3. Clique para visualizar detalhes
4. Crie um novo projeto
5. Adicione membros ao projeto

#### 3. **Teste de Tarefas**
1. Vá para "Tarefas" no menu
2. Observe as tarefas já criadas
3. Crie uma nova tarefa
4. Atribua a tarefa a um usuário
5. Altere o status da tarefa

#### 4. **Teste com Diferentes Usuários**
1. Faça logout
2. Faça login com `pedro@guardian.local`
3. Observe que só vê projetos onde é membro
4. Teste criar/editar tarefas

#### 5. **Teste de Permissões**
1. Teste com usuário comum tentando acessar funções administrativas
2. Verifique se as restrições funcionam

---

### 📊 Dados de Exemplo Inclusos

O sistema já vem com:
- **5 usuários** com diferentes funções
- **2 projetos** de exemplo
- **5 tarefas** com diferentes status e prioridades
- **Membros** atribuídos aos projetos
- **Marcos** (milestones) configurados

---

### 🛠️ Comandos Úteis

```bash
# Ver logs da aplicação
docker-compose logs -f guardian_app

# Acessar container da aplicação
docker-compose exec guardian_app bash

# Resetar dados (limpar e recriar)
docker-compose exec guardian_app php artisan guardian:setup-mvp --force

# Parar o sistema
docker-compose down

# Parar e remover volumes (reset completo)
docker-compose down -v
```

---

### ✅ Checklist de Validação

- [ ] Sistema inicia sem erros
- [ ] Login funciona com usuários de teste
- [ ] Dashboard carrega com estatísticas
- [ ] Pode listar projetos e tarefas
- [ ] Pode criar novos projetos
- [ ] Pode criar novas tarefas
- [ ] Filtros funcionam
- [ ] Permissões por função funcionam
- [ ] Interface é responsiva

---

### 🚨 Solução de Problemas

#### Erro de Conexão com Banco
```bash
# Verificar status dos containers
docker-compose ps

# Reiniciar banco de dados
docker-compose restart guardian_db

# Aguardar e tentar novamente
sleep 15
docker-compose exec guardian_app php artisan guardian:setup-mvp
```

#### Erro de Permissões
```bash
# Corrigir permissões
docker-compose exec guardian_app chown -R www-data:www-data /var/www/html
docker-compose exec guardian_app chmod -R 755 /var/www/html
```

#### Cache de Configuração
```bash
# Limpar todos os caches
docker-compose exec guardian_app php artisan config:clear
docker-compose exec guardian_app php artisan route:clear
docker-compose exec guardian_app php artisan view:clear
```
