<?php
// src/Model/Usuario.php

class Usuario {
    private $conn;
    private $table = "usuarios";

    public $id;
    public $nombre;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los usuarios
    public function obtenerTodos() {
        $query = "SELECT id, nombre, email, fecha_creacion FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Buscar usuario por email (para login)
    public function buscarPorEmail($email) {
        $query = "SELECT id, nombre, email, password FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt;
    }

    // Crear nuevo usuario (registro)
    public function crear() {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, password) 
                  VALUES (:nombre, :email, :password)";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        // Vincular valores
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si existe un email
    public function emailExiste($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>
