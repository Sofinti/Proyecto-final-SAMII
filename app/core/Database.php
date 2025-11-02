<?php
// app/core/Database.php
class Database {
    private $host;
    private $dbname;
    private $user;
    private $pass;
    private $port;

    public function __construct() {
        // Detectar si estamos en Railway o local
        $this->host = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'db';
        $this->port = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3306';
        $this->dbname = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'samantha_db';
        $this->user = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'samantha_user';
        $this->pass = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: 'TuClaveUser456';
    }

    public function connect() {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
        $pdo = null;
        try {
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión a la Base de Datos: " . $e->getMessage());
        }
    }
}
