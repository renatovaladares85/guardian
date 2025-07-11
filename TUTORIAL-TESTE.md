# 🧪 Guardian - Tutorial Modo Teste

**Configuração para demonstração e avaliação do sistema**

## 📋 Visão Geral

O **Modo Teste** configura o Guardian com dados de exemplo completos, permitindo avaliar todas as funcionalidades do sistema imediatamente após a instalação.

## 🎯 O que será configurado?

- ✅ **5 usuários** com diferentes funções e permissões
- ✅ **2 projetos** de exemplo com dados realistas
- ✅ **5 tarefas** em diferentes estágios de desenvolvimento
- ✅ **Marcos (milestones)** configurados
- ✅ **Relacionamentos** entre usuários, projetos e tarefas
- ✅ **Dados históricos** simulando uso real

## 🚀 Como Configurar

### Opção 1: Via Interface Web (Recomendado)

1. **Inicie o sistema:**
   ```bash
   .\start-mvp.bat
   ```

2. **Acesse o assistente:** O navegador abrirá automaticamente em `http://localhost:8000`

3. **Selecione Modo Teste:** Clique no cartão "🧪 Modo Teste"

4. **Confirme a configuração:** Clique em "Configurar Guardian"

5. **Aguarde:** O sistema será configurado automaticamente

6. **Acesse:** Após a conclusão, faça login com qualquer usuário de teste

### Opção 2: Via Linha de Comando

```bash
# Dentro do container
docker-compose exec guardian_app php artisan guardian:setup --mode=test

# Ou usando o comando direto
docker-compose exec guardian_app php artisan guardian:setup-mvp --force
```

## 👥 Usuários de Teste Criados

| Email | Senha | Função | Departamento | Acesso |
|-------|-------|--------|--------------|--------|
| `admin@guardian.local` | `guardian123` | **Super Admin** | Administração | Total ao sistema |
| `joao@guardian.local` | `guardian123` | **Gerente de Projetos** | TI | Criar/gerenciar projetos |
| `maria@guardian.local` | `guardian123` | **Líder de Equipe** | Desenvolvimento | Gerenciar equipes |
| `pedro@guardian.local` | `guardian123` | **Desenvolvedor** | Desenvolvimento | Executar tarefas |
| `ana@guardian.local` | `guardian123` | **Desenvolvedora** | Desenvolvimento | Executar tarefas |

## 📊 Projetos de Exemplo

### 1. **Sistema Guardian MVP**
- **Status:** Ativo (em desenvolvimento)
- **Prioridade:** Alta
- **Orçamento:** R$ 50.000,00
- **Período:** 30 dias atrás → 60 dias à frente
- **Equipe:** João (gerente), Maria (líder), Pedro e Ana (desenvolvedores)

### 2. **Portal do Cliente**
- **Status:** Planejamento
- **Prioridade:** Média
- **Orçamento:** R$ 30.000,00
- **Período:** 15 dias à frente → 90 dias à frente
- **Equipe:** João (gerente), Maria (líder), Pedro (desenvolvedor)

## 📝 Tarefas de Exemplo

### Tarefas do Projeto Guardian MVP:

1. **✅ Configurar autenticação de usuários** (Concluída)
   - Responsável: Pedro
   - 8h estimadas / 6.5h realizadas
   - Vencimento: 5 dias atrás

2. **🔄 Criar CRUD de projetos** (Em andamento)
   - Responsável: Ana
   - 12h estimadas / 8h realizadas
   - Vencimento: em 3 dias

3. **📋 Implementar sistema de tarefas** (A fazer)
   - Responsável: Pedro
   - 16h estimadas
   - Vencimento: em 7 dias

4. **📊 Desenvolver dashboard inicial** (Backlog)
   - Responsável: Ana
   - 20h estimadas
   - Vencimento: em 14 dias

5. **🧪 Configurar testes automatizados** (A fazer)
   - Responsável: Maria
   - 8h estimadas
   - Vencimento: em 10 dias

## 🎯 Cenários de Teste Sugeridos

### 1. **Teste de Diferentes Funções**
```
Admin (admin@guardian.local):
→ Acesso total ao sistema
→ Pode gerenciar todos os usuários
→ Visualiza todos os projetos

Gerente (joao@guardian.local):
→ Pode criar e gerenciar projetos
→ Atribui tarefas à equipe
→ Visualiza relatórios

Líder (maria@guardian.local):
→ Gerencia equipe em projetos específicos
→ Distribui e acompanha tarefas
→ Relatórios da equipe

Desenvolvedor (pedro@guardian.local):
→ Visualiza projetos onde é membro
→ Atualiza status de suas tarefas
→ Registra tempo trabalhado
```

### 2. **Fluxo Completo de Projeto**
1. **Login como Gerente** (joão)
2. **Criar novo projeto**
3. **Adicionar membros da equipe**
4. **Definir marcos do projeto**
5. **Criar tarefas iniciais**
6. **Atribuir tarefas aos desenvolvedores**

### 3. **Teste de Execução de Tarefas**
1. **Login como Desenvolvedor** (pedro)
2. **Visualizar tarefas atribuídas**
3. **Alterar status para "Em andamento"**
4. **Adicionar comentários**
5. **Registrar tempo trabalhado**
6. **Marcar como concluída**

## 📈 Dados Gerados Automaticamente

### **Dashboard Statistics:**
- **2 projetos** criados
- **1 projeto ativo**, 1 em planejamento
- **5 tarefas** distribuídas
- **1 tarefa concluída**, 2 em andamento, 2 pendentes
- **Progresso automático** calculado

### **Relacionamentos:**
- Usuários conectados aos projetos como membros
- Tarefas atribuídas aos usuários apropriados
- Marcos vinculados às tarefas
- Histórico de datas realista

### **Funcionalidades Demonstradas:**
- Sistema de permissões por função
- Cálculo automático de progresso
- Alertas de tarefas vencidas
- Filtros e busca funcionais
- Interface responsiva

## 🔧 Configurações Aplicadas

### **Arquivo de Configuração Criado:**
```json
{
  "mode": "test",
  "features": {
    "demo_data": true,
    "security_relaxed": true,
    "audit_minimal": true
  },
  "setup_date": "2025-07-11T10:30:00Z",
  "version": "1.0-MVP"
}
```

### **Recursos Ativados:**
- ✅ Dados de demonstração completos
- ✅ Configurações de segurança relaxadas (para teste)
- ✅ Auditoria básica ativada
- ✅ Cache otimizado para desenvolvimento
- ✅ Logs detalhados para debug

## 🎓 Roteiro de Aprendizado

### **Iniciante (15 minutos):**
1. Faça login como admin
2. Explore o dashboard
3. Visualize os projetos existentes
4. Veja as tarefas em andamento

### **Intermediário (30 minutos):**
1. Teste diferentes usuários
2. Crie um novo projeto
3. Adicione tarefas ao projeto
4. Teste filtros e busca

### **Avançado (60 minutos):**
1. Configure equipe completa
2. Simule fluxo de desenvolvimento
3. Teste todas as permissões
4. Explore relatórios

## ⚠️ Limitações do Modo Teste

- **Não recomendado para produção**
- **Senhas simples** (guardian123)
- **Configurações de segurança relaxadas**
- **Dados fictícios** (não usar para projetos reais)
- **SSL/HTTPS não configurado**

## 🔄 Como Migrar para Produção

1. **Backup dos dados importantes** (se houver customizações)
2. **Execute reset completo:**
   ```bash
   docker-compose down -v
   .\start-mvp.bat
   ```
3. **Escolha "Modo Produção"** no assistente
4. **Configure usuário administrador real**
5. **Ajuste configurações de segurança**

## 📞 Próximos Passos

Após explorar o modo teste:

1. **📖 Consulte:** `TUTORIAL-PRODUCAO.md` para uso real
2. **🔧 Explore:** `TUTORIAL-PERSONALIZADO.md` para configurações avançadas
3. **📚 Leia:** `COMPONENTES.md` para entender a arquitetura
4. **🚀 Implemente:** Seus próprios projetos no sistema

---

**💡 Dica:** Use o modo teste para demonstrações, treinamento de equipe e avaliação do sistema antes de partir para a implementação em produção!
