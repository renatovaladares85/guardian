#!/bin/bash

# Guardian Health Check Script
# Monitora a saúde dos containers e envia alertas

set -e

echo "🔍 Iniciando monitoramento de saúde dos containers Guardian..."

# Configurações
LOG_FILE="/logs/healthcheck.log"
ALERT_EMAIL="admin@guardian.local"
CHECK_INTERVAL=60  # segundos

# Função para log
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Função para verificar saúde do container
check_container_health() {
    local container_name=$1
    local status=$(docker inspect --format='{{.State.Health.Status}}' "$container_name" 2>/dev/null || echo "not_found")
    
    case $status in
        "healthy")
            return 0
            ;;
        "unhealthy")
            log "❌ ALERTA: Container $container_name está UNHEALTHY"
            return 1
            ;;
        "starting")
            log "⏳ Container $container_name está iniciando..."
            return 0
            ;;
        "not_found")
            log "❌ ERRO: Container $container_name não encontrado"
            return 1
            ;;
        *)
            log "⚠️ AVISO: Status desconhecido para $container_name: $status"
            return 1
            ;;
    esac
}

# Função para verificar se container está rodando
check_container_running() {
    local container_name=$1
    local is_running=$(docker inspect --format='{{.State.Running}}' "$container_name" 2>/dev/null || echo "false")
    
    if [ "$is_running" = "true" ]; then
        return 0
    else
        log "❌ ALERTA: Container $container_name não está rodando"
        return 1
    fi
}

# Função para restart de container
restart_container() {
    local container_name=$1
    log "🔄 Tentando reiniciar container $container_name..."
    
    if docker restart "$container_name"; then
        log "✅ Container $container_name reiniciado com sucesso"
        sleep 30  # Aguardar inicialização
        return 0
    else
        log "❌ ERRO: Falha ao reiniciar container $container_name"
        return 1
    fi
}

# Lista de containers críticos para monitorar
CONTAINERS=(
    "guardian_app"
    "guardian_db" 
    "guardian_redis"
    "guardian_nginx"
)

# Criar arquivo de log se não existir
mkdir -p "/logs"
touch "$LOG_FILE"

log "🚀 Sistema de monitoramento iniciado"

# Loop principal de monitoramento
while true; do
    all_healthy=true
    
    for container in "${CONTAINERS[@]}"; do
        # Verificar se está rodando
        if ! check_container_running "$container"; then
            all_healthy=false
            restart_container "$container"
            continue
        fi
        
        # Verificar saúde (se tiver healthcheck)
        if ! check_container_health "$container"; then
            all_healthy=false
            
            # Tentar reiniciar container unhealthy
            if [ "$(docker inspect --format='{{.State.Health.Status}}' "$container")" = "unhealthy" ]; then
                restart_container "$container"
            fi
        fi
    done
    
    # Log de status geral
    if [ "$all_healthy" = true ]; then
        log "✅ Todos os containers estão saudáveis"
    else
        log "⚠️ Alguns containers apresentam problemas"
    fi
    
    # Verificar uso de recursos
    log "📊 Uso de recursos:"
    docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}" "${CONTAINERS[@]}" | tee -a "$LOG_FILE"
    
    # Aguardar próxima verificação
    sleep "$CHECK_INTERVAL"
done
