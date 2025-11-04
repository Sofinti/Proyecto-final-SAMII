#!/bin/bash

# -----------------------------------------------------------------------------
# 🛠️ Script de Provisionamiento Linux Multi-Sistema (provision.sh)
# -----------------------------------------------------------------------------
#
# Este script de Bash automatiza la configuración inicial de un entorno de
# desarrollo basado en Docker/Docker Compose para aplicaciones web (ej. Laravel)
# en distribuciones Linux como Debian, Ubuntu y CentOS/RHEL/Fedora.
#
# Su objetivo es estandarizar y asegurar la configuración de herramientas
# críticas como SSH, Docker y la clonación del repositorio de forma
# segura e idempotente (ejecutable varias veces sin causar efectos colaterales).
#
# -----------------------------------------------------------------------------
# ⚙️ Flujo de Ejecución y Configuración
# -----------------------------------------------------------------------------
#
# El script sigue un flujo de trabajo lógico y adaptable:
#
# 1. Pre-chequeos y Detección (check_sudo, detect_os):
#    Verifica 'sudo' y detecta la distribución de Linux usando /etc/os-release.
#
# 2. Gestión de la Configuración (load_or_prompt_config):
#    Carga/guarda la configuración en '.provision.conf' para persistencia
#    y define el ambiente (dev/test).
#
# 3. Provisionamiento Base (install_docker, setup_ssh, setup_github_ssh):
#    Instala Docker Engine + Compose, asegura el servidor SSH (deshabilita
#    login por password/root) y genera claves SSH para GitHub.
#
# 4. Despliegue del Proyecto (clone_repository, setup_project):
#    Clona o actualiza el repositorio de forma idempotente y levanta
#    los contenedores, ejecutando los comandos de setup (ej. composer).
#
# -----------------------------------------------------------------------------

# --- CONFIGURACIÓN INICIAL Y LOGGING ---

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Nombre del archivo de configuración del script
CONFIG_FILE=".provision.conf"

# Función para imprimir mensajes
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}" >&2
    exit 1
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARN: $1${NC}"
}

info() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')] INFO: $1${NC}"
}

# -----------------------------------------------------------------------------
# 1. PRE-CHEQUEOS Y DETECCIÓN
# -----------------------------------------------------------------------------
#
# Sudo y Seguridad: La función check_sudo verifica la existencia de sudo.
# Si no se encuentra en una distribución compatible (como Debian), el script
# proporciona la instrucción exacta (su -c 'apt-get install sudo -y')
# para instalarlo como root.
#
# Detección de OS: Utiliza /etc/os-release para identificar la distribución.
# Esto es vital en la instalación de Docker, ya que permite obtener el
# nombre en código de la versión (ej. $VERSION_CODENAME).
#
# -----------------------------------------------------------------------------

# Función para verificar si el usuario tiene privilegios de sudo
check_sudo() {
    if [ "$EUID" -ne 0 ]; then
        if ! command -v sudo &> /dev/null; then
            # Si sudo no está instalado y no somos root
            error "No eres root y el comando 'sudo' no está disponible. En Debian/Ubuntu, puedes instalarlo con: su -c 'apt-get update && apt-get install sudo -y'"
        fi
        
        if ! sudo -n true 2>/dev/null; then
            error "Este script requiere privilegios de sudo. Por favor ejecuta con sudo o como root. Si estás en Debian y no funciona, revisa si tu usuario está en el grupo 'sudo'."
        fi
    fi
}

# Función para detectar la distribución de Linux
detect_os() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS=$ID 
        OS_VERSION=$VERSION_ID
    else
        error "No se pudo detectar la distribución de Linux"
    fi
}

# -----------------------------------------------------------------------------
# 2. GESTIÓN DE LA CONFIGURACIÓN
# -----------------------------------------------------------------------------
#
# Persistencia: La función load_or_prompt_config intenta cargar las variables
# (REPO_URL, PROJECT_DIR, ENV_MODE) desde el archivo .provision.conf
# usando el comando source. Esto hace que las ejecuciones posteriores
# sean no interactivas.
#
# Ambientes: Define el ambiente de trabajo (dev o test) y, en consecuencia,
# el archivo de Docker Compose a utilizar (ej. docker-compose.dev.yml).
#
# -----------------------------------------------------------------------------

# Función para cargar la configuración y solicitar valores faltantes al usuario
load_or_prompt_config() {
    info "Cargando configuración desde $CONFIG_FILE (si existe)..."
    if [ -f "$CONFIG_FILE" ]; then
        # Cargar variables del archivo. Se usa sed para sanitizar las líneas.
        # shellcheck disable=SC1090
        source <(grep -E '^[A-Z_]+=' "$CONFIG_FILE" | sed 's/^export //')
    else
        warn "Archivo de configuración $CONFIG_FILE no encontrado. Se solicitará la información."
    fi

    # 1. Definir el ambiente (dev/test)
    while [ -z "$ENV_MODE" ]; do
        read -r -p "Introduce el ambiente a construir (dev o test): [dev] " input_env
        ENV_MODE=${input_env:-dev}
        if [[ "$ENV_MODE" =~ ^(dev|test)$ ]]; then
            break
        else
            warn "Valor inválido. Por favor, introduce 'dev' o 'test'."
            ENV_MODE=""
        fi
    done
    DOCKER_COMPOSE_BASE="docker-compose.$ENV_MODE.yml"
    log "Ambiente seleccionado: $ENV_MODE (usando $DOCKER_COMPOSE_BASE)"
    
    # 2. Solicitar URL del repositorio
    while [ -z "$REPO_URL" ]; do
        read -r -p "Introduce la URL SSH de tu repositorio GitHub (ej: git@github.com:usuario/repo.git): " REPO_URL
        if [ -z "$REPO_URL" ]; then
            warn "La URL del repositorio es obligatoria."
        fi
    done
    
    # Intentar obtener el nombre del proyecto desde la URL
    REPO_NAME=$(basename "$REPO_URL" .git)
    DEFAULT_DIR="$HOME/$REPO_NAME"

    # 3. Definir el directorio de instalación
    while [ -z "$PROJECT_DIR" ]; do
        read -r -p "Introduce el directorio de instalación: [$DEFAULT_DIR] " input_dir
        PROJECT_DIR=${input_dir:-$DEFAULT_DIR}
        if [ -z "$PROJECT_DIR" ]; then
             warn "El directorio de proyecto es obligatorio."
        fi
    done
    
    # 4. Guardar la configuración para la próxima vez
    log "Guardando configuración en $CONFIG_FILE..."
    {
        echo "REPO_URL=\"$REPO_URL\""
        echo "PROJECT_DIR=\"$PROJECT_DIR\""
        echo "ENV_MODE=\"$ENV_MODE\""
    } > "$CONFIG_FILE"
}

# -----------------------------------------------------------------------------
# 3. PROVISIONAMIENTO BASE
# -----------------------------------------------------------------------------
#
# Instalación de Docker: Instala Docker Engine y el plugin de Docker Compose v2
# (docker compose). Luego añade al usuario actual al grupo 'docker'
# (sudo usermod -aG docker $USER), lo que permite ejecutar comandos de
# Docker sin sudo (requiere reiniciar sesión).
#
# Seguridad SSH y Respaldo (setup_ssh):
#   Respaldo Crítico: Antes de cualquier cambio, se crea una copia de
#   seguridad del archivo /etc/ssh/sshd_config con un timestamp.
#   Autenticación de Clave: Se fuerza el uso de pares de claves SSH al
#   deshabilitar el login por contraseña (PasswordAuthentication no) y
#   el login de root (PermitRootLogin no).
#
# Claves GitHub (setup_github_ssh):
#   Se genera un par de claves SSH (solo si no existe, garantizando la
#   idempotencia).
#
# -----------------------------------------------------------------------------

# Función para actualizar el sistema
update_system() {
    log "Actualizando el sistema..."
    case "$OS" in
        ubuntu|debian)
            sudo apt-get update -qq
            sudo apt-get upgrade -y -qq
            ;;
        centos|rhel|fedora)
            sudo yum update -y -q
            ;;
        *)
            error "Sistema operativo no soportado: $OS"
            ;;
    esac
}

# Función para instalar paquetes básicos
install_basic_tools() {
    log "Instalando herramientas básicas..."
    # Se añade 'make' y 'tree' y 'net-tools'
    case "$OS" in
        ubuntu|debian)
            sudo apt-get install -y -qq git curl wget rsync openssh-client openssh-server \
                software-properties-common apt-transport-https ca-certificates \
                gnupg-agent unzip make nano htop tree net-tools 
            ;;
        centos|rhel|fedora)
            sudo yum install -y -q git curl wget rsync openssh-clients openssh-server \
                unzip nano htop vim make tree net-tools
            ;;
        *)
            error "Sistema operativo no soportado para la instalación de herramientas básicas: $OS"
            ;;
    esac
}

# Función para instalar Docker y el plugin de Docker Compose (Corregido para Debian)
install_docker() {
    log "Instalando Docker Engine y el plugin de Docker Compose (v2)..."
    if command -v docker &> /dev/null && docker compose version &> /dev/null; then
        warn "Docker y Docker Compose (plugin) ya están instalados"
        return
    fi

    case "$OS" in
        ubuntu)
            sudo apt-get update -qq
            sudo apt-get install -y -qq ca-certificates curl gnupg lsb-release
            sudo install -m 0755 -d /etc/apt/keyrings
            curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
            sudo chmod a+r /etc/apt/keyrings/docker.gpg
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
            sudo apt-get update -qq
            sudo apt-get install -y -qq docker-ce docker-ce-cli containerd.io docker-compose-plugin
            ;;
        debian)
            sudo apt-get update -qq
            sudo apt-get install -y -qq ca-certificates curl gnupg lsb-release
            sudo install -m 0755 -d /etc/apt/keyrings
            curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
            sudo chmod a+r /etc/apt/keyrings/docker.gpg
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
            sudo apt-get update -qq
            sudo apt-get install -y -qq docker-ce docker-ce-cli containerd.io docker-compose-plugin
            ;;
        centos|rhel|fedora)
            if [ "$OS" == "fedora" ]; then
                sudo dnf -y -q install dnf-plugins-core
                sudo dnf config-manager --add-repo https://download.docker.com/linux/fedora/docker-ce.repo
                sudo dnf -y -q install docker-ce docker-ce-cli containerd.io
            else
                sudo yum install -y -q yum-utils
                sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
                sudo yum install -y -q docker-ce docker-ce-cli containerd.io
            fi
            ;;
        *)
            error "Sistema operativo no soportado para la instalación de Docker: $OS"
            ;;
    esac

    if [ -n "$USER" ] && ! id -nG "$USER" | grep -qw "docker"; then
        log "Agregando usuario '$USER' al grupo 'docker'. Necesitarás cerrar sesión y volver a entrar."
        sudo usermod -aG docker "$USER"
    fi
    
    sudo systemctl enable docker
    sudo systemctl start docker
    
    log "Docker instalado correctamente"
}

# Función para instalar Docker Compose Standalone (Solo para retrocompatibilidad con 'docker-compose')
install_docker_compose() {
    log "Instalando Docker Compose Standalone (v2) para compatibilidad con 'docker-compose'..."
    if command -v docker-compose &> /dev/null; then
        warn "El binario 'docker-compose' ya está instalado. Omitiendo instalación."
        return
    fi
    
    local COMPOSE_VERSION="v2.23.0"
    log "Descargando Docker Compose Standalone versión $COMPOSE_VERSION..."
    
    if ! sudo curl -L "https://github.com/docker/compose/releases/download/$COMPOSE_VERSION/docker-compose-$(uname -s)-$(uname -m)" \
        -o /usr/local/bin/docker-compose --silent; then
        error "Fallo al descargar Docker Compose Standalone v2."
    fi
    
    sudo chmod +x /usr/local/bin/docker-compose
    
    if [ ! -f /usr/bin/docker-compose ]; then
        log "Creando enlace simbólico: /usr/bin/docker-compose -> /usr/local/bin/docker-compose"
        sudo ln -sf /usr/local/bin/docker-compose /usr/bin/docker-compose
    fi
    
    log "Docker Compose Standalone (v2) instalado correctamente como 'docker-compose'"
    warn "NOTA: Se recomienda usar 'docker compose' (sin guion) que se instala como plugin."
}

# -----------------------------------------------------------------------------
# 🔎 ENFOQUE ACADÉMICO: EXPRESIONES REGULARES Y SEGURIDAD (en setup_ssh)
# -----------------------------------------------------------------------------
#
# El script utiliza el comando 'sed' (Stream Editor) con expresiones regulares
# para modificar archivos de configuración de manera precisa y robusta.
#
# Ejemplo en setup_ssh:
# sudo sed -i -E 's/^\s*#?PermitRootLogin.*/PermitRootLogin no/' "$SSH_CONFIG"
#
# Desglose del RegEx:
#   -E: Habilita expresiones regulares extendidas.
#   s/.../.../: Comando de sustitución.
#
#   Patrón de Búsqueda: ^\s*#?PermitRootLogin.*
#     ^            # Ancla al inicio de la línea.
#     \s* # Busca cero o más espacios en blanco (indentación).
#     #?           # Busca cero o una '#' (para encontrar líneas comentadas).
#     PermitRootLogin # Busca el texto literal.
#     .* # Busca cualquier carácter, cero o más veces (el resto de la línea).
#
#   Patrón de Reemplazo: PermitRootLogin no
#     Reemplaza toda la línea encontrada por esta configuración exacta.
#
# -----------------------------------------------------------------------------

# Función para configurar SSH
setup_ssh() {
    local SSH_CONFIG="/etc/ssh/sshd_config"
    local SSH_CONFIG_BACKUP="${SSH_CONFIG}.bak.$(date +%Y%m%d%H%M%S)"

    log "Configurando SSH..."
    
    # 1. Respaldar archivo de configuración
    if [ -f "$SSH_CONFIG" ]; then
        log "Respaldando $SSH_CONFIG a $SSH_CONFIG_BACKUP"
        sudo cp "$SSH_CONFIG" "$SSH_CONFIG_BACKUP"
    else
        warn "El archivo de configuración SSH ($SSH_CONFIG) no existe. Saltando respaldo."
    fi

    # 2. Modificar archivo
    # Asegurarse de que el directorio .ssh existe
    mkdir -p ~/.ssh
    chmod 700 ~/.ssh
    
    # Deshabilitar PermitRootLogin (usando el RegEx explicado arriba)
    sudo sed -i -E 's/^\s*#?PermitRootLogin.*/PermitRootLogin no/' "$SSH_CONFIG"
    if ! grep -q '^PermitRootLogin' "$SSH_CONFIG"; then
        echo "PermitRootLogin no" | sudo tee -a "$SSH_CONFIG" > /dev/null
    fi

    # Deshabilitar PasswordAuthentication (usando el mismo RegEx)
    sudo sed -i -E 's/^\s*#?PasswordAuthentication.*/PasswordAuthentication no/' "$SSH_CONFIG"
    if ! grep -q '^PasswordAuthentication' "$SSH_CONFIG"; then
        echo "PasswordAuthentication no" | sudo tee -a "$SSH_CONFIG" > /dev/null
    fi
    
    # 3. Reiniciar servicio SSH
    log "Reiniciando servicio SSH..."
    sudo systemctl restart ssh || sudo service ssh restart 
    
    log "SSH configurado correctamente. El respaldo está en $SSH_CONFIG_BACKUP"
}

# Función para configurar el acceso SSH a GitHub
setup_github_ssh() {
    log "Configurando acceso SSH a GitHub..."
    
    local SSH_KEY_PATH="$HOME/.ssh/id_rsa"
    local SSH_PUB_KEY_PATH="$HOME/.ssh/id_rsa.pub"

    # Verificar para evitar doble generación de claves (Idempotencia)
    if [ -f "$SSH_KEY_PATH" ]; then
        warn "La clave SSH estándar ('$SSH_KEY_PATH') ya existe. No se generará una nueva."
    else
        info "Generando nueva clave SSH en '$SSH_KEY_PATH'..."
        mkdir -p ~/.ssh
        ssh-keygen -t rsa -b 4096 -C "devops@provision-script" -N "" -f "$SSH_KEY_PATH"
        log "Clave SSH generada."
    fi
    
    # Mostrar la clave pública para GitHub
    log "Por favor agrega la siguiente clave pública a tu cuenta de GitHub:"
    echo -e "${YELLOW}"
    if [ -f "$SSH_PUB_KEY_PATH" ]; then
        cat "$SSH_PUB_KEY_PATH"
    else
        error "No se encontró la clave pública en '$SSH_PUB_KEY_PATH'."
    fi
    echo -e "${NC}"
    
    read -r -p "Presiona Enter después de haber agregado la clave a GitHub..."
    
    # Probar la conexión a GitHub
    # Se usa '|| true' para que el script no falle si la prueba SSH
    # devuelve un código de salida no-cero (lo cual hace).
    log "Probando conexión SSH con GitHub..."
    ssh -T git@github.com || true
    log "Prueba de conexión a GitHub finalizada."
}

# -----------------------------------------------------------------------------
# 4. DESPLIEGUE DEL PROYECTO
# -----------------------------------------------------------------------------
#
# Clonación Idempotente: Si el directorio ya existe, clone_repository
# intenta actualizarlo con 'git pull origin main || git pull origin master',
# gestionando las ramas principales comunes.
#
# Orquestación Docker: La función setup_project utiliza la bandera -T en
# 'docker compose exec' (ej. composer install). Esta bandera evita la
# asignación de TTY (terminal), una buena práctica al ejecutar comandos
# dentro de contenedores de forma no interactiva (scripting).
#
# -----------------------------------------------------------------------------

# Función para clonar el repositorio
clone_repository() {
    local repo_url=$1
    local target_dir=$2
    
    log "Clonando repositorio: $repo_url en $target_dir"
    
    if [ -d "$target_dir" ]; then
        warn "El directorio '$target_dir' ya existe. Intentando actualizar en lugar de clonar..."
        if [ ! -d "$target_dir/.git" ]; then
            error "El directorio existe pero no es un repositorio git. Borra '$target_dir' o cambia el directorio de instalación para continuar."
        fi
        
        # Asegurar permisos para operar dentro del directorio
        sudo chown -R "$USER":"$USER" "$target_dir"
        cd "$target_dir"
        git pull origin main || git pull origin master 
    else
        log "Creando directorio '$target_dir' y clonando repositorio..."
        # Crear la ruta completa y asegurar permisos
        sudo mkdir -p "$target_dir"
        sudo chown "$USER":"$USER" "$target_dir"
        git clone "$repo_url" "$target_dir"
        cd "$target_dir"
    fi
    
    log "Configurando permisos iniciales del repositorio..."
    find . -type d -exec chmod 755 {} \;
    find . -type f -exec chmod 644 {} \;
    
    log "Repositorio clonado/actualizado en: $target_dir"
}

# Función para configurar el entorno del proyecto
setup_project() {
    local compose_file=$1
    local project_dir=$2
    
    log "Configurando el proyecto Laravel para ambiente $ENV_MODE (usando $compose_file)..."
    
    # 1. Copiar archivo de entorno si no existe
    if [ ! -f .env ]; then
        info "Creando archivo .env a partir de .env.example"
        cp .env.example .env
    fi
    
    # 2. Determinar comando Docker Compose (plugin vs standalone)
    DOCKER_COMPOSE_CMD="docker-compose"
    if command -v docker &> /dev/null && docker compose version &> /dev/null; then
        DOCKER_COMPOSE_CMD="docker compose"
        info "Usando el plugin 'docker compose' (v2)."
    else
        info "Usando el binario 'docker-compose' (standalone)."
    fi

    # 3. Construir contenedores Docker
    log "Construyendo contenedores Docker..."
    "$DOCKER_COMPOSE_CMD" -f "$compose_file" build
    
    # 4. Iniciar contenedores
    log "Iniciando contenedores..."
    "$DOCKER_COMPOSE_CMD" -f "$compose_file" up -d
    
    # 5. Instalar dependencias de Composer, Generar key, Migrar
    log "Instalando dependencias de Composer..."
    # Se usa -T para deshabilitar la pseudo-TTY, ideal para scripting
    "$DOCKER_COMPOSE_CMD" -f "$compose_file" exec -T app composer install --no-interaction
    
    log "Generando key de aplicación..."
    "$DOCKER_COMPOSE_CMD" -f "$compose_file" exec -T app php artisan key:generate
    
    log "Ejecutando migraciones de base de datos..."
    # Se añade --force para producción/pruebas sin confirmación
    "$DOCKER_COMPOSE_CMD" -f "$compose_file" exec -T app php artisan migrate --seed --force 
    
    # 6. Configurar permisos
    log "Configurando permisos de almacenamiento/cache..."
    "$DOCKER_COMPOSE_CMD" -f "$compose_file" exec -T app chmod -R 775 storage bootstrap/cache
    "$DOCKER_COMPOSE_CMD" -f "$compose_file" exec -T app chown -R www-data:www-data storage bootstrap/cache

    if [ -d "$project_dir/storage" ]; then
         log "Asegurando permisos del host para '$project_dir/storage' y '$project_dir/bootstrap/cache'..."
         sudo chmod -R 775 "$project_dir/storage" "$project_dir/bootstrap/cache"
    fi
    
    log "Proyecto configurado correctamente"
}

# -----------------------------------------------------------------------------
# FUNCIÓN PRINCIPAL (MAIN)
# -----------------------------------------------------------------------------

main() {
    log "Iniciando proceso de provisionamiento multi-sistema"
    
    # Verificar privilegios de sudo
    check_sudo
    
    # Detectar sistema operativo
    detect_os
    info "Sistema operativo detectado: $OS $OS_VERSION"
    
    # Cargar/solicitar variables de configuración
    load_or_prompt_config
    
    # --- PROVISIONAMIENTO BASE ---
    update_system
    install_basic_tools
    install_docker
    install_docker_compose
    setup_ssh
    setup_github_ssh
    
    # --- CONFIGURACIÓN DEL PROYECTO ---
    
    # Clonar repositorio
    clone_repository "$REPO_URL" "$PROJECT_DIR"
    
    # Configurar proyecto (ejecutar comandos de Laravel/Docker Compose)
    # cd "$PROJECT_DIR" ya se hace en clone_repository
    setup_project "$DOCKER_COMPOSE_BASE" "$PROJECT_DIR"
    
    # --- INFORMACIÓN FINAL ---
    log "Provisionamiento completado exitosamente!"
    info "Ambiente construido: $ENV_MODE"
    info "Directorio del proyecto: $PROJECT_DIR"
    
    # Redeterminar DOCKER_COMPOSE_CMD y DOCKER_COMPOSE_BASE para el resumen final
    DOCKER_COMPOSE_CMD="docker-compose"
    if command -v docker &> /dev/null && docker compose version &> /dev/null; then
        DOCKER_COMPOSE_CMD="docker compose"
    fi
    
    log "Comandos útiles (usando el comando $DOCKER_COMPOSE_CMD):"
    info "Logs: $DOCKER_COMPOSE_CMD -f $DOCKER_COMPOSE_BASE logs -f"
    info "Detener: $DOCKER_COMPOSE_CMD -f $DOCKER_COMPOSE_BASE down"
    info "Iniciar: $DOCKER_COMPOSE_CMD -f $DOCKER_COMPOSE_BASE up -d"
    
    warn "Recuerda que puede que necesites cerrar sesión y volver a iniciar para que los cambios en el grupo 'docker' surtan efecto."
}

# Ejecutar función principal
main "$@"
