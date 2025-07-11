# 👤 Manual do Usuário - Guardian

Guia completo para uso das funcionalidades do sistema Guardian.

## 🏁 Primeiros Passos

### Como Acessar o Sistema
1. Abra seu navegador
2. Acesse: `http://localhost` (ou URL configurada)
3. Digite seu login e senha
4. Clique em "Entrar"

### Tipos de Login
O sistema aceita dois formatos de login:
- **Login único**: ex. `gcosta`, `aferreira`, `lalmeida`
- **E-mail**: ex. `gabriela@guardian.com`

> **Dica**: O login único é formado pela primeira letra do nome + último sobrenome

### Primeiro Acesso
Após o primeiro login:
1. Verifique suas informações no perfil
2. Explore o dashboard com dados de exemplo
3. Familiarize-se com o menu de navegação
4. Configure suas preferências

## 🎛️ Interface Principal

### Dashboard
O dashboard é sua página inicial e mostra:
- **Resumo de atividades** recentes
- **Estatísticas** importantes
- **Notificações** e alertas
- **Atalhos** para funções principais

### Menu de Navegação
- 🏠 **Dashboard**: Página inicial com resumos
- 👥 **Usuários**: Gerenciar contas e perfis
- 📊 **Projetos**: Gerenciar projetos e tarefas
- ⚙️ **Configurações**: Personalizar o sistema
- 🔐 **Segurança**: Logs e auditoria
- 📋 **Relatórios**: Relatórios e análises

## 👥 Gerenciamento de Usuários

### Perfis de Usuário
O sistema possui diferentes níveis de acesso:

#### Super Administrador
- Acesso total ao sistema
- Gerenciar todos os usuários
- Configurações globais
- Backup e manutenção

#### Administrador
- Gerenciar usuários da empresa
- Criar e gerenciar projetos
- Relatórios gerenciais
- Configurações limitadas

#### Gerente de Projetos
- Gerenciar projetos específicos
- Adicionar membros à equipe
- Relatórios de projeto
- Configurações de projeto

#### Desenvolvedor
- Acesso a projetos atribuídos
- Gerenciar tarefas pessoais
- Relatórios de atividade
- Configurações do perfil

#### Usuário Básico
- Visualizar projetos atribuídos
- Gerenciar tarefas pessoais
- Relatórios básicos
- Configurações básicas

### Como Criar um Usuário

#### Para Administradores:
1. Acesse **Usuários** → **Novo Usuário**
2. Preencha os dados obrigatórios:
   - **Nome completo**: ex. "Ana Silva Costa"
   - **E-mail**: ex. "ana@empresa.com"
   - **Perfil**: Selecione o nível apropriado
   - **Senha temporária**: Sistema gera automaticamente
3. Clique em "Salvar"
4. O sistema criará automaticamente o login único: `acosta`

#### Login Único - Como Funciona:
- **Ana Silva Costa** → Login: `acosta`
- **João Pedro Santos** → Login: `jsantos`
- **Maria Fernanda Oliveira** → Login: `moliveira`

> **Importante**: O sistema evita logins ofensivos automaticamente

### Editar Perfil de Usuário
1. Acesse **Usuários** → Encontre o usuário
2. Clique em "Editar"
3. Modifique os campos necessários
4. **Atenção**: O login único não pode ser alterado
5. Clique em "Salvar"

### Desativar/Ativar Usuário
- **Desativar**: Usuário não consegue mais fazer login
- **Ativar**: Restaura acesso ao sistema
- **Excluir**: Remove permanentemente (cuidado!)

## 📊 Gerenciamento de Projetos

### Criar Novo Projeto
1. Acesse **Projetos** → **Novo Projeto**
2. Preencha as informações:
   - **Nome do projeto**
   - **Descrição detalhada**
   - **Data de início/fim**
   - **Prioridade** (Baixa, Média, Alta, Crítica)
   - **Status** (Planejamento, Em Andamento, Concluído)
3. Atribua membros da equipe
4. Defina marcos e entregas
5. Clique em "Criar Projeto"

### Gerenciar Equipe do Projeto
- **Adicionar membros**: Busque usuários e defina papéis
- **Definir responsabilidades**: Atribua tarefas específicas
- **Configurar permissões**: Quem pode ver/editar o quê
- **Comunicação**: Sistema de notificações integrado

### Status de Projetos
- 📅 **Planejamento**: Projeto sendo estruturado
- 🚀 **Em Andamento**: Desenvolvimento ativo
- ⏸️ **Pausado**: Temporariamente suspenso
- ✅ **Concluído**: Finalizado com sucesso
- ❌ **Cancelado**: Encerrado sem conclusão

## 📋 Sistema de Tarefas

### Criar Tarefas
1. Dentro de um projeto, clique em **Nova Tarefa**
2. Defina:
   - **Título** claro e objetivo
   - **Descrição** detalhada
   - **Responsável** da tarefa
   - **Prazo** de conclusão
   - **Prioridade** da tarefa
   - **Dependências** (se houver)

### Acompanhar Progresso
- **Lista de tarefas**: Visão geral de todas as tarefas
- **Quadro Kanban**: Visual por status (A Fazer, Fazendo, Feito)
- **Calendário**: Visualização por datas e prazos
- **Gráficos**: Relatórios de produtividade

### Status de Tarefas
- 📝 **A Fazer**: Tarefa criada, aguardando início
- 🔄 **Em Andamento**: Sendo executada
- 🔍 **Em Revisão**: Aguardando aprovação
- ✅ **Concluída**: Finalizada
- 🚫 **Bloqueada**: Impedimento identificado

## 📊 Relatórios e Análises

### Tipos de Relatórios

#### Relatórios de Projeto
- **Progresso geral**: % de conclusão
- **Timeline**: Marcos e entregas
- **Recursos**: Tempo e custos
- **Qualidade**: Bugs e revisões

#### Relatórios de Usuário
- **Produtividade**: Tarefas por período
- **Tempo gasto**: Horas por projeto
- **Performance**: Metas vs realizado
- **Atividades**: Log de ações

#### Relatórios Gerenciais
- **Dashboard executivo**: KPIs principais
- **Comparativo**: Projetos e equipes
- **Tendências**: Análise temporal
- **Alertas**: Riscos e problemas

### Como Gerar Relatórios
1. Acesse **Relatórios**
2. Escolha o tipo desejado
3. Defina filtros:
   - **Período**: Data inicial e final
   - **Projetos**: Específicos ou todos
   - **Usuários**: Filtrar por equipe
   - **Status**: Estados específicos
4. Clique em "Gerar Relatório"
5. Visualize online ou **exporte** (PDF, Excel)

## 🔐 Segurança e Auditoria

### Logs de Atividade
O sistema registra automaticamente:
- **Logins e logouts** de usuários
- **Criação/edição** de dados
- **Alterações** em projetos
- **Acessos** a informações sensíveis

### Como Consultar Logs
1. Acesse **Segurança** → **Logs de Auditoria**
2. Use filtros para encontrar eventos específicos:
   - **Data/hora** do evento
   - **Usuário** responsável
   - **Tipo** de ação
   - **Módulo** afetado
3. Visualize detalhes de cada evento

### Segurança da Conta
- **Alterar senha**: Regularmente (recomendado mensalmente)
- **Sessões ativas**: Monitore dispositivos conectados
- **Duas etapas**: Configure autenticação adicional (se disponível)
- **Notificações**: Alertas de segurança por e-mail

## ⚙️ Configurações Pessoais

### Perfil do Usuário
- **Informações básicas**: Nome, e-mail, telefone
- **Foto de perfil**: Upload de imagem pessoal
- **Fuso horário**: Configure para sua região
- **Idioma**: Português (padrão), outros disponíveis

### Preferências do Sistema
- **Tema**: Claro, escuro ou automático
- **Notificações**: E-mail, push, dentro do sistema
- **Dashboard**: Widgets e layout preferido
- **Relatórios**: Formatos padrão de exportação

### Notificações
Configure quando receber alertas:
- ✅ **Novas tarefas** atribuídas
- ✅ **Prazos próximos** (1-3 dias)
- ✅ **Alterações** em projetos
- ✅ **Comentários** e menções
- ❌ **Relatórios** semanais
- ❌ **Atualizações** do sistema

## 🔍 Busca e Filtros

### Busca Global
Use a barra de busca no topo para encontrar:
- **Projetos** por nome ou descrição
- **Usuários** por nome ou login
- **Tarefas** por título ou conteúdo
- **Documentos** anexados

### Filtros Avançados
Em listas, use filtros para refinar resultados:
- **Status**: Filtre por estado atual
- **Data**: Período específico
- **Responsável**: Usuário específico
- **Prioridade**: Nível de urgência
- **Tags**: Marcadores personalizados

### Salvamento de Filtros
- **Filtros frequentes**: Salve combinações usadas
- **Relatórios personalizados**: Crie visões específicas
- **Alertas automáticos**: Receba quando critérios forem atendidos

## 🛠️ Funcionalidades Avançadas

### Integração com E-mail
- **Notificações**: Receba atualizações por e-mail
- **Criar tarefas**: Via e-mail (se configurado)
- **Relatórios automáticos**: Envio programado
- **Lembretes**: Prazos e eventos importantes

### API e Integrações
Para desenvolvedores e integrações:
- **API REST**: Endpoints documentados
- **Webhooks**: Notificações automáticas
- **Exportação**: Dados em JSON/XML
- **Importação**: De sistemas externos

### Backup de Dados
- **Automático**: Backup diário do sistema
- **Exportação pessoal**: Seus dados em arquivo
- **Histórico**: Versões anteriores de documentos
- **Recuperação**: Restaurar itens excluídos

## 🚨 Solução de Problemas

### Problemas Comuns

#### Não consegue fazer login
1. Verifique se está usando o login correto (não o nome completo)
2. Teste com e-mail se não lembrar do login
3. Verifique se caps lock está desligado
4. Contate o administrador se continuar o problema

#### Sistema está lento
1. Verifique sua conexão com internet
2. Feche outras abas do navegador
3. Limpe cache do navegador
4. Tente em modo anônimo/privado

#### Não recebe notificações
1. Verifique configurações de notificação no perfil
2. Confirme se e-mail está correto
3. Verifique pasta de spam
4. Teste envio de notificação manual

#### Relatório não carrega
1. Tente reduzir período de tempo
2. Remova filtros muito específicos
3. Verifique se tem permissão para os dados
4. Contate suporte se persistir

### Contato com Suporte
- **Chat interno**: Ícone no canto inferior direito
- **E-mail**: suporte@guardian.com
- **Telefone**: (11) 99999-9999
- **Horário**: Segunda a sexta, 8h às 18h

## 📚 Recursos Adicionais

### Documentação Técnica
- [Guia de Instalação](instalacao.md)
- [Configuração Avançada](configuracao.md)
- [API Documentation](../api/endpoints.md)
- [Deploy Guide](../deployment/docker.md)

### Treinamento
- **Vídeo tutoriais**: Canal no YouTube
- **Webinars**: Sessões quinzenais
- **Documentação**: Sempre atualizada
- **Treinamento presencial**: Sob demanda

### Comunidade
- **Fórum de usuários**: Tire dúvidas
- **Grupo no WhatsApp**: Comunicação rápida
- **Newsletter**: Novidades mensais
- **Feedback**: Sua opinião é importante

---

## 🎯 Dicas de Produtividade

### Organize seu Trabalho
1. **Use prioridades** para focar no importante
2. **Defina prazos realistas** para não se sobrecarregar
3. **Revise tarefas diariamente** para manter controle
4. **Use comentários** para comunicar progresso

### Trabalhe em Equipe
1. **Mencione colegas** (@nome) em comentários
2. **Compartilhe documentos** dentro dos projetos
3. **Use status** para comunicar disponibilidade
4. **Participe das reuniões** de projeto

### Monitore Performance
1. **Acompanhe relatórios** semanalmente
2. **Compare metas** com realizações
3. **Identifique gargalos** nos processos
4. **Celebre conquistas** da equipe

---

**Dominou o Guardian!** 🎉 Você agora tem todas as ferramentas para ser produtivo no sistema.
