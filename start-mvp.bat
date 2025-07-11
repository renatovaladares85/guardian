@echo off
echo 🚀 Iniciando Guardian Project Management System...

REM Verificar se Docker está rodando
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker não está rodando. Por favor, inicie o Docker primeiro.
    pause
    exit /b 1
)

REM Construir e iniciar os containers
echo 📦 Iniciando containers Docker...
docker-compose up -d

REM Aguardar banco de dados ficar pronto
echo ⏳ Aguardando serviços ficarem prontos...
timeout /t 20 /nobreak

REM Instalar dependências se necessário
echo 📚 Verificando dependências PHP...
docker-compose exec guardian_app composer install --no-dev --optimize-autoloader

REM Verificar se já foi configurado
echo 🔍 Verificando estado do sistema...
docker-compose exec guardian_app php -r "
try {
    \$pdo = new PDO('pgsql:host=guardian_db;dbname=guardian_db', 'falcon_user', 'falcon_secure_pass');
    \$stmt = \$pdo->query('SELECT COUNT(*) FROM users');
    \$count = \$stmt->fetchColumn();
    if (\$count > 0) {
        echo 'CONFIGURED';
    } else {
        echo 'NEEDS_SETUP';
    }
} catch (Exception \$e) {
    echo 'NEEDS_SETUP';
}
" > temp_status.txt

set /p SETUP_STATUS=<temp_status.txt
del temp_status.txt

if "%SETUP_STATUS%"=="CONFIGURED" (
    echo.
    echo ✅ Guardian já está configurado e rodando!
    echo.
    echo 🌐 Aplicação: http://localhost:8000
    echo 📧 MailHog: http://localhost:8025
    echo.
    echo 🎯 Sistema pronto para uso!
) else (
    echo.
    echo 🛠️  Primeira execução detectada!
    echo.
    echo 🌐 Acesse: http://localhost:8000
    echo.
    echo 🎯 O Assistente de Configuração será exibido automaticamente.
    echo    Você poderá escolher entre:
    echo.
    echo    🧪 Modo Teste - MVP com dados de exemplo
    echo    � Modo Produção - Sistema limpo para uso real  
    echo    ⚙️  Modo Personalizado - Configuração avançada
    echo.
)

echo.
echo 📊 Informações do Sistema:
echo    �️  Banco: localhost:5432 (guardian_db)
echo    📊 Redis: localhost:6379
echo    📧 MailHog: http://localhost:8025
echo.
echo 🛠️  Comandos úteis:
echo    Para parar: docker-compose down
echo    Para logs: docker-compose logs -f guardian_app
echo    Para resetar: docker-compose down -v
echo    Para assistente CLI: docker-compose exec guardian_app php artisan guardian:setup
echo.

REM Abrir navegador automaticamente se for primeira vez
if "%SETUP_STATUS%"=="NEEDS_SETUP" (
    echo 🌐 Abrindo navegador...
    start http://localhost:8000
)

pause
