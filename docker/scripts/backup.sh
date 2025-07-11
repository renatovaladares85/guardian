#!/bin/bash

# Guardian Database Backup Script
# Executa backup automático do PostgreSQL

set -e

echo "🔄 Iniciando backup do banco de dados Guardian..."

# Configurações
BACKUP_DIR="/backups"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="guardian_backup_${DATE}.sql"
RETENTION_DAYS=7

# Criar diretório de backup se não existir
mkdir -p "$BACKUP_DIR"

# Executar backup
echo "📦 Criando backup: $BACKUP_FILE"
pg_dump -h "$POSTGRES_HOST" -U "$POSTGRES_USER" -d "$POSTGRES_DB" > "$BACKUP_DIR/$BACKUP_FILE"

# Comprimir backup
echo "🗜️ Comprimindo backup..."
gzip "$BACKUP_DIR/$BACKUP_FILE"

# Limpar backups antigos
echo "🧹 Limpando backups antigos (mais de $RETENTION_DAYS dias)..."
find "$BACKUP_DIR" -name "guardian_backup_*.sql.gz" -type f -mtime +$RETENTION_DAYS -delete

# Listar backups disponíveis
echo "📋 Backups disponíveis:"
ls -lah "$BACKUP_DIR"/guardian_backup_*.sql.gz

echo "✅ Backup concluído com sucesso!"

# Agendar próximo backup (executa a cada 6 horas)
sleep 21600
exec "$0"
