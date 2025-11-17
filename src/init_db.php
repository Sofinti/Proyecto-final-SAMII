<?php
// Script de inicialización de base de datos para Railway
require_once __DIR__ . '/config/Database.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Init DB</title>";
echo "<style>body{font-family:Arial;max-width:600px;margin:50px auto;padding:20px;}</style>";
echo "</head><body>";
echo "<h1>🔧 Inicializando Base de Datos...</h1>";

try {
    $db = (new Database())->connect();
    
    // Crear tabla users
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    echo "<p>✅ Tabla 'users' creada correctamente.</p>";
    
    // Insertar usuario admin
    $hash = password_hash('123456', PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT IGNORE INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hash, 'administrador']);
    
    echo "<p>✅ Usuario admin creado.</p>";
    echo "<p><strong>Usuario:</strong> admin</p>";
    echo "<p><strong>Contraseña:</strong> 123456</p>";
    echo "<hr>";
    echo "<h2>✅ Base de datos lista!</h2>";
    echo "<p><a href='/' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;'>Ir al Login</a></p>";
    echo "<p style='color:red;margin-top:30px;'><strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de usarlo por seguridad.</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
