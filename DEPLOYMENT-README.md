# Configuración de Deployment para Railway

## Archivos de configuración:

- `docker-compose.yml` - Para desarrollo local (Apache + MySQL)
- `docker-compose.railway.yml` - Para deployment avanzado con Nginx + PHP-FPM
- `nixpacks.toml` - Configuración de Railway
- `app/` - Estructura MVC mejorada

## Para correr localmente:
```bash
docker-compose up -d
```

## Para Railway:
Railway usa automáticamente `nixpacks.toml` y la carpeta `app/`
