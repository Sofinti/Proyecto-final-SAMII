<?php
// /src/Controllers/AdminController.php

class AdminController {

    /**
     * Muestra el panel con la lista de usuarios pendientes
     */
    public function dashboard() {
        // 1. Verificar si es Administrador (Rol 1)
        // Si no está logueado o no es Rol 1, lo sacamos.
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['Rol'] != 1) {
            header('Location: /'); // Lo mandamos al inicio
            exit();
        }

        // 2. Conectar a la BD
        try {
            $pdo = Database::connect();

            // 3. Buscar usuarios pendientes (Activo = 0) y que NO sean admins (Id_TipoUsuario != 1)
            $sql = "SELECT * FROM Usuarios WHERE Activo = 0 AND Id_TipoUsuario != 1";
            $stmt = $pdo->query($sql);
            $pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Cargar la Vista (crearemos este archivo en el Paso 2)
            // Le pasamos la variable $pendientes para que la use el HTML
            require __DIR__ . '/../Views/html/admin/dashboard.php';

        } catch (Exception $e) {
            echo "Error al cargar el panel: " . $e->getMessage();
        }
    }

    /**
     * Recibe el ID del usuario y lo habilita
     */
    public function habilitarUsuario() {
        // Verificar permisos de nuevo
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['Rol'] != 1) {
            header('Location: /');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idUsuario = $_POST['id_usuario'];

            try {
                $pdo = Database::connect();
                
                // Actualizar a Activo = 1
                $sql = "UPDATE Usuarios SET Activo = 1 WHERE Id_Usuario = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$idUsuario]);

                // Volver al dashboard
                header('Location: /admin/dashboard');
                exit();

            } catch (Exception $e) {
                echo "Error al habilitar usuario: " . $e->getMessage();
            }
        }
    }
}
?>