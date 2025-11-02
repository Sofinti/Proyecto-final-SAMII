<?php
// Script para insertar/actualizar el usuario admin con hash correcto

require_once __DIR__ . '/../core/Database.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    // Generar el hash correcto de "123456"
    $correctHash = password_hash('123456', PASSWORD_BCRYPT);
    
    echo "Hash generado para '123456': $correctHash\n\n";
    
    // Verificar si el usuario admin existe
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $exists = $stmt->fetch();
    
    if ($exists) {
        // Actualizar
        $stmt = $db->prepare("UPDATE users SET password_hash = ?, role = ? WHERE username = ?");
        $stmt->execute([$correctHash, 'administrador', 'admin']);
        echo "✅ Usuario 'admin' ACTUALIZADO correctamente.\n";
    } else {
        // Insertar
        $stmt = $db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $correctHash, 'administrador']);
        echo "✅ Usuario 'admin' INSERTADO correctamente.\n";
    }
    
    // Verificar
    $stmt = $db->prepare("SELECT username, role FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\n📋 Usuario verificado:\n";
    echo "   Username: {$user['username']}\n";
    echo "   Role: {$user['role']}\n";
    echo "\n✅ Ahora puedes hacer login con: admin / 123456\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
