<?php
// /src/Controllers/AdminController.php

class AdminController {

    // ================================================================
    // 1. MOSTRAR EL DASHBOARD (Centro de Control)
    // ================================================================
    public function dashboard() {
        // Verificar si es Admin (Rol 1)
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['Rol'] != 1) {
            header('Location: /'); 
            exit();
        }

        try {
            $pdo = Database::connect();

            // A. Usuarios Pendientes (Activo = 0)
            $sqlUsers = "SELECT * FROM Usuarios WHERE Activo = 0 AND Id_TipoUsuario != 1";
            $pendientesUser = $pdo->query($sqlUsers)->fetchAll(PDO::FETCH_ASSOC);

            // B. Horas Pendientes (Estado = 'Pendiente')
            // Traemos datos de la hora y el nombre del usuario
            $sqlHoras = "SELECT h.*, u.Nombre, u.Apellido 
                        FROM HorasLaborales h 
                        JOIN Usuarios u ON h.Id_Usuario = u.Id_Usuario 
                        WHERE h.Estado = 'Pendiente'";
            $pendientesHoras = $pdo->query($sqlHoras)->fetchAll(PDO::FETCH_ASSOC);

            // C. Pagos Pendientes (Iniciales y Mensuales)
            // 1. Pago Inicial
            $sqlPI = "SELECT p.*, u.Nombre, u.Apellido 
                    FROM PagoInicial p 
                    JOIN Usuarios u ON p.Id_Usuario = u.Id_Usuario 
                    WHERE p.Estado = 'Pendiente'";
            $pIniciales = $pdo->query($sqlPI)->fetchAll(PDO::FETCH_ASSOC);

            // 2. Pago Mensual
            $sqlPM = "SELECT p.*, u.Nombre, u.Apellido 
                    FROM PagoMensual p 
                    JOIN Usuarios u ON p.Id_Usuario = u.Id_Usuario 
                    WHERE p.Estado = 'Pendiente'";
            $pMensuales = $pdo->query($sqlPM)->fetchAll(PDO::FETCH_ASSOC);

            // Cargar la Vista (Pasándole todas las variables)
            // NOTA: Ajusté la ruta para que coincida con tu estructura: /Views/html/admin/
            require __DIR__ . '/../Views/html/admin/dashboard.php';

        } catch (Exception $e) {
            echo "Error al cargar el panel: " . $e->getMessage();
        }
    }

    // ================================================================
    // 2. ACCIONES (Botones del Dashboard)
    // ================================================================

    // Acción: Habilitar Usuario
    public function habilitarUsuario() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_usuario'] ?? 0;
            $this->actualizarEstado('Usuarios', 'Activo', 1, 'Id_Usuario', $id);
        }
    }

    // Acción: Aprobar/Rechazar Horas
    public function aprobarHoras() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_horas'] ?? 0;
            $accion = $_POST['accion'] ?? ''; // 'Aprobado' o 'Rechazado'
            $this->actualizarEstado('HorasLaborales', 'Estado', $accion, 'Id_Horas', $id);
        }
    }

    // Acción: Gestionar Pago (Inicial o Mensual)
    public function gestionarPago() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_pago'] ?? 0;
            $tipo = $_POST['tipo'] ?? '';     // 'Inicial' o 'Mensual'
            $accion = $_POST['accion'] ?? ''; // 'Aprobado' o 'Rechazado'
            
            // Determinar qué tabla actualizar
            $tabla = ($tipo === 'Inicial') ? 'PagoInicial' : 'PagoMensual';
            $columnaId = ($tipo === 'Inicial') ? 'id_PagoInicial' : 'Id_PagoMensual';

            $this->actualizarEstado($tabla, 'Estado', $accion, $columnaId, $id);
        }
    }

    // ================================================================
    // 3. FUNCIONES AUXILIARES (Para no repetir código)
    // ================================================================

    private function verificarAdmin() {
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['Rol'] != 1) {
            header('Location: /'); 
            exit();
        }
    }

    private function actualizarEstado($tabla, $columna, $valor, $columnaId, $id) {
        try {
            $pdo = Database::connect();
            // Actualizamos el estado en la base de datos
            $sql = "UPDATE $tabla SET $columna = ? WHERE $columnaId = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$valor, $id]);
            
            // Volver al dashboard
            header('Location: /admin/dashboard');
            exit();

        } catch (Exception $e) {
            echo "Error al actualizar: " . $e->getMessage();
        }
    }

    // ================================================================
    // 4. GESTIÓN DE NOVEDADES (NUEVO)
    // ================================================================

    public function mostrarCrearNovedad() {
        $this->verificarAdmin(); // Usamos tu función de seguridad existente
        require __DIR__ . '/../Views/html/admin/crearNovedad.html';
    }

    public function handleGuardarNovedad() {
        $this->verificarAdmin();

        try {
            $pdo = Database::connect();

            $titulo = $_POST['titulo'];
            $contenido = $_POST['contenido'];
            $rutaImagen = null; 

            // Lógica de subida de imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                // Nombre único: tiempo + nombre original
                $nombreArchivo = time() . '_' . $_FILES['imagen']['name'];
                
                // Rutas
                $rutaFisica = __DIR__ . '/../uploads/' . $nombreArchivo; // Donde se guarda en el disco
                $rutaWeb = 'src/uploads/' . $nombreArchivo; // Lo que se guarda en la BD (sin barra inicial)

                // Crear carpeta si no existe
                if (!file_exists(__DIR__ . '/../uploads')) {
                    mkdir(__DIR__ . '/../uploads', 0777, true);
                }

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaFisica)) {
                    $rutaImagen = $rutaWeb;
                }
            }

            // Insertar en la Base de Datos
            $sql = "INSERT INTO Novedades (Titulo, Contenido, Imagen_url, FechaPublicacion) VALUES (?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $contenido, $rutaImagen]);

            // Redirigir al inicio para ver la noticia publicada
            header('Location: /');
            exit();

        } catch (Exception $e) {
            echo "Error al guardar noticia: " . $e->getMessage();
        }
    }
}
?>