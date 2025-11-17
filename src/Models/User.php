<?php
// /src/Models/User.php

class User {
    private $db; // Para guardar la conexión PDO

    public function __construct() {
        // Crea una instancia de la clase Database y se conecta
        $dbClass = new Database();
        $this->db = $dbClass->connect();
    }

    /**
     * Busca un usuario por su Email.
     * (Esto reemplaza el SQL inseguro de tu archivo viejo)
     */
    public function findByEmail($email) {
        try {
            // 1. Preparar la consulta (Evita Inyección SQL)
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            
            // 2. Ejecutar la consulta
            $stmt->execute([$email]);
            
            // 3. Obtener el resultado
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ?: false; // Devuelve el usuario o 'false' si no lo encuentra

        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     */
    public function create($email, $password_hash, $role) {
        try {
            // 1. Preparar la consulta
            $stmt = $this->db->prepare(
                "INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)"
            );
            
            // 2. Ejecutar
            return $stmt->execute([$email, $password_hash, $role]);

        } catch (PDOException $e) {
            // Manejar error (ej. email duplicado)
            throw new Exception("Error al insertar usuario: " . $e->getMessage());
        }
    }

    // (Aquí podés agregar más métodos: findById, update, delete, etc.)
}