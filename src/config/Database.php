<?php
class Database
{
    public static function connect()
    {
        $servidor = getenv('MYSQL_HOST') ?: 'samantha-db';
        $port = getenv('MYSQL_PORT') ?: '3306';
        $db = getenv('MYSQL_DATABASE') ?: 'samantha_db';
        $user = getenv('MYSQL_USER') ?: 'samantha_user';
        $pwd = getenv('MYSQL_PASSWORD') ?: 'samantha_pwd';
        
        $dsn = "mysql:host={$servidor};port={$port};dbname={$db};charset=utf8mb4";
        
        try {
            $pdo = new PDO($dsn, $user, $pwd);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión a la Base de Datos: " . $e->getMessage());
        }
    }
}