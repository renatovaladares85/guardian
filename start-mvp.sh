#!/bin/bash

echo "🚀 Iniciando Guardian MVP..."

# Verificar se Docker está rodando
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker não está rodando. Por favor, inicie o Docker primeiro."
    exit 1
fi

# Construir e iniciar os containers
echo "📦 Construindo containers Docker..."
docker-compose up -d --build

# Aguardar banco de dados ficar pronto
echo "⏳ Aguardando banco de dados ficar pronto..."
sleep 30

# Instalar dependências do Composer
echo "📚 Instalando dependências PHP..."
docker-compose exec guardian_app composer install

# Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
docker-compose exec guardian_app php artisan key:generate

# Executar migrations
echo "🗄️ Executando migrations..."
docker-compose exec guardian_app php artisan migrate --force

# Executar seeders
echo "🌱 Populando banco com dados de teste..."
docker-compose exec guardian_app php artisan db:seed --force

# Limpar e otimizar cache
echo "🧹 Otimizando aplicação..."
docker-compose exec guardian_app php artisan config:cache
docker-compose exec guardian_app php artisan route:cache
docker-compose exec guardian_app php artisan view:cache

echo ""
echo "✅ Guardian MVP está pronto!"
echo ""
echo "🌐 Acesse: http://localhost:8000"
echo "📧 MailHog: http://localhost:8025"
echo ""
echo "👥 Usuários de teste:"
echo "   admin@guardian.local (senha: guardian123) - Super Admin"
echo "   joao@guardian.local (senha: guardian123) - Gerente de Projetos"
echo "   maria@guardian.local (senha: guardian123) - Líder de Equipe"
echo "   pedro@guardian.local (senha: guardian123) - Desenvolvedor"
echo "   ana@guardian.local (senha: guardian123) - Desenvolvedora"
echo ""
echo "🎯 O sistema já tem projetos e tarefas de exemplo!"
