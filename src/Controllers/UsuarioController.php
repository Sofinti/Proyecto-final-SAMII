<?php
// /src/Controllers/UsuarioController.php

class UsuarioController {

    // ========================================================
    // --- 1. VISTAS ESTÁTICAS (Secciones y Novedades) ---
    // ========================================================

    public function mostrarCalendario() { require __DIR__ . '/../Views/html/calendario.html'; }
    public function mostrarSocios() { require __DIR__ . '/../Views/html/secciones/socios.html'; }
    public function mostrarContabilidad() { require __DIR__ . '/../Views/html/secciones/contabilidad.html'; }
    public function mostrarLegal() { require __DIR__ . '/../Views/html/secciones/legal.html'; }
    public function mostrarReclamos() { require __DIR__ . '/../Views/html/secciones/reclamos.html'; }
    
    public function mostrarNovedad1() { require __DIR__ . '/../Views/html/novedades/novedad1.html'; }
    public function mostrarNovedad2() { require __DIR__ . '/../Views/html/novedades/novedad2.html'; }
    public function mostrarNovedad3() { require __DIR__ . '/../Views/html/novedades/novedad3.html'; }
    public function mostrarNovedad4() { require __DIR__ . '/../Views/html/novedades/novedad4.html'; }
    public function mostrarNovedad5() { require __DIR__ . '/../Views/html/novedades/novedad5.html'; }


    // ========================================================
    // --- 2. MÓDULO DE HORAS ---
    // ========================================================

    public function showMisHorasForm() {
        require __DIR__ . '/../Views/html/horasTrabajo.html';
    }

    public function handleHorasSubmit() {
        try {
            if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
                header('Location: /login');
                exit();
            }

            $id_usuario = $_SESSION['user_id'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];
            $descripcion = $_POST['descripcion'] ?? '';

            // Cálculo de horas
            $inicio = new DateTime($hora_inicio);
            $fin = new DateTime($hora_fin);
            if ($fin < $inicio) { $fin->modify('+1 day'); }
            $diferencia = $inicio->diff($fin);
            $horasDecimales = $diferencia->h + ($diferencia->i / 60);

            $pdo = Database::connect();
            $sql = "INSERT INTO HorasLaborales (Id_Usuario, HorasTrabajadas, Motivo, Estado, FechaRegistro) VALUES (?, ?, ?, 'Pendiente', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_usuario, $horasDecimales, $descripcion]);

            echo "<h1 style='color: green;'>¡Horas Registradas!</h1>";
            echo "<p>Se han registrado <b>" . number_format($horasDecimales, 2) . " horas</b>.</p>";
            echo "<a href='/mis-horas'>Cargar más</a> | <a href='/'>Volver al Inicio</a>";

        } catch (Exception $e) {
            echo "<h1 style='color:red;'>Error al guardar:</h1><p>" . $e->getMessage() . "</p><a href='/mis-horas'>Volver</a>";
        }
    }


    // ========================================================
    // --- 3. MÓDULO DE PAGOS ---
    // ========================================================

    public function mostrarPagos() {
        if (!isset($_SESSION['is_logged_in'])) { header('Location: /login'); exit(); }
        
        $idUsuario = $_SESSION['user_id'];
        $pdo = Database::connect();

        $sqlInicial = "SELECT * FROM PagoInicial WHERE Id_Usuario = ? LIMIT 1";
        $stmt = $pdo->prepare($sqlInicial);
        $stmt->execute([$idUsuario]);
        $pagoInicial = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlMensual = "SELECT * FROM PagoMensual WHERE Id_Usuario = ? ORDER BY Ano DESC, Mes DESC";
        $stmt2 = $pdo->prepare($sqlMensual);
        $stmt2->execute([$idUsuario]);
        $pagosMensuales = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/html/pagos.php';
    }

    public function handlePagoInicial() {
        try {
            if (!isset($_SESSION['is_logged_in'])) { header('Location: /login'); exit(); }

            $idUsuario = $_SESSION['user_id'];
            $monto = $_POST['monto'];
            $rutaComprobante = '';
            
            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
                $nombreArchivo = 'inicial_' . $idUsuario . '_' . time() . '.jpg';
                // Ruta física (para guardar el archivo)
                $rutaDestino = __DIR__ . '/../uploads/' . $nombreArchivo;
                
                if (!file_exists(__DIR__ . '/../uploads')) {
                    mkdir(__DIR__ . '/../uploads', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaDestino)) {
                    // Ruta WEB (para guardar en la BD y ver en el navegador)
                    // SIN EL /src AL PRINCIPIO
                    $rutaComprobante = '/uploads/' . $nombreArchivo;
                }
            }

            $pdo = Database::connect();
            $sql = "INSERT INTO PagoInicial (Id_Usuario, Monto, Comprobante_url, Estado, Fecha) VALUES (?, ?, ?, 'Pendiente', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idUsuario, $monto, $rutaComprobante]);

            header('Location: /mis-pagos');
            exit();

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function handlePagoMensual() {
        try {
            if (!isset($_SESSION['is_logged_in'])) { header('Location: /login'); exit(); }

            $idUsuario = $_SESSION['user_id'];
            $mes = $_POST['mes'];
            $ano = $_POST['ano'];
            $monto = $_POST['monto'];
            $rutaComprobante = '';

            if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
                $nombreArchivo = 'mensual_' . $idUsuario . '_' . $mes . $ano . '_' . time() . '.jpg';
                
                // Ruta física
                $rutaDestino = __DIR__ . '/../uploads/' . $nombreArchivo;
                
                if (!file_exists(__DIR__ . '/../uploads')) {
                    mkdir(__DIR__ . '/../uploads', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaDestino)) {
                    // Ruta WEB (SIN EL /src)
                    $rutaComprobante = '/uploads/' . $nombreArchivo;
                }
            }

            $pdo = Database::connect();
            $sql = "INSERT INTO PagoMensual (Id_Usuario, Mes, Ano, Monto, Comprobante_url, Estado) VALUES (?, ?, ?, ?, ?, 'Pendiente')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idUsuario, $mes, $ano, $monto, $rutaComprobante]);

            header('Location: /mis-pagos');
            exit();

        } catch (Exception $e) {
            echo "Error al procesar pago mensual: " . $e->getMessage();
            echo "<br><a href='/mis-pagos'>Volver</a>";
        }
    }

    // ========================================================
    // --- VER DETALLE DE UNA NOVEDAD ---
    // ========================================================
    public function verDetalleNovedad() {
        // 1. Obtener el ID de la URL (ej: /ver-novedad?id=5)
        $id = $_GET['id'] ?? null;

        if (!$id) {
            // Si no hay ID, volvemos al inicio
            header('Location: /');
            exit();
        }

        // 2. Buscar en la Base de Datos
        $pdo = Database::connect();
        $sql = "SELECT * FROM Novedades WHERE Id_Novedad = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$noticia) {
            echo "<h1>Error 404: Noticia no encontrada</h1><a href='/'>Volver</a>";
            exit();
        }

        // 3. Cargar la vista (pasándole los datos)
        require __DIR__ . '/../Views/html/verNovedad.php';
    }

    // ========================================================
    // --- MI CUENTA (PERFIL) ---
    // ========================================================
    
    public function mostrarMiCuenta() {
        if (!isset($_SESSION['is_logged_in'])) { header('Location: /login'); exit(); }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE Id_Usuario = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        require __DIR__ . '/../Views/html/miCuenta.php';
    }

    public function actualizarPerfil() {
        if (!isset($_SESSION['is_logged_in'])) { header('Location: /login'); exit(); }

        $id = $_SESSION['user_id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $pass = $_POST['password'];

        try {
            $pdo = Database::connect();

            // Si escribió una contraseña nueva, la actualizamos
            if (!empty($pass)) {
                // Si usas hash en el futuro: $passHash = password_hash($pass, PASSWORD_DEFAULT);
                // Por ahora plano según tu setup actual:
                $sql = "UPDATE Usuarios SET Nombre=?, Apellido=?, Correo=?, Telefono=?, Contrasenia=? WHERE Id_Usuario=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $apellido, $correo, $telefono, $pass, $id]);
            } else {
                // Si NO cambia la contraseña, actualizamos solo datos
                $sql = "UPDATE Usuarios SET Nombre=?, Apellido=?, Correo=?, Telefono=? WHERE Id_Usuario=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $apellido, $correo, $telefono, $id]);
            }

            // Actualizamos la sesión por si cambió el nombre
            $_SESSION['Nombre'] = $nombre;

            header('Location: /mi-cuenta?status=success');
            exit();

        } catch (Exception $e) {
            header('Location: /mi-cuenta?status=error');
        }
    }
}
?>