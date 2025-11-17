<?php
// src/seed.php
// Script COMPLETO para CREAR TABLA e INSERTAR/ACTUALIZAR admin.

require_once __DIR__ . '/config/Database.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    // -----------------------------------------------------------------
    // PASO 1: Crear la tabla de usuarios (SI NO EXISTE)
    // -----------------------------------------------------------------
    // Esta estructura coincide con nuestro AuthController y User Model
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    
    echo "✅ Tabla 'users' verificada/creada correctamente.\n<br>\n";

    // -----------------------------------------------------------------
    // PASO 2: Insertar o Actualizar el usuario Administrador
    // -----------------------------------------------------------------
    
    $adminEmail = 'admin@samantha.com';
    $adminPwd = '123456';
    $adminRole = 'Administrador';
    
    // Generar el hash
    $correctHash = password_hash($adminPwd, PASSWORD_BCRYPT);
    
    echo "Hash generado para '$adminPwd': $correctHash\n<br>\n";
    
    // Verificar si el usuario admin (por email) existe
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        // Actualizar
        $stmt = $db->prepare("UPDATE users SET password_hash = ?, role = ? WHERE email = ?");
        $stmt->execute([$correctHash, $adminRole, $adminEmail]);
        echo "✅ Usuario '$adminEmail' ACTUALIZADO correctamente.\n<br>\n";
    } else {
        // Insertar
        $stmt = $db->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute([$adminEmail, $correctHash, $adminRole]);
        echo "✅ Usuario '$adminEmail' INSERTADO correctamente.\n<br>\n";
    }
    
    echo "\n<hr>\n";
    echo "<h2>✅ ¡Seed completado!</h2>\n";
    echo "<p>Ahora puedes iniciar sesión con:</p>\n";
    echo "<p><strong>Email:</strong> $adminEmail</p>\n";
    echo "<p><strong>Contraseña:</strong> $adminPwd</p>\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
