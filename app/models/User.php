<?php
// app/models/User.php

require_once __DIR__ . '/../core/Database.php';

class User {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    /**
     * Busca un usuario por su nombre de usuario.
     * @param string $username
     * @return array|false Datos del usuario o false si no se encuentra.
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            // MODIFICACIÓN CRÍTICA: Aseguramos que el resultado sea un ARRAY ASOCIATIVO
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Manejo de errores de consulta (no de conexión)
            error_log("Error al buscar usuario: " . $e->getMessage());
            return false;
        }
    }
}
