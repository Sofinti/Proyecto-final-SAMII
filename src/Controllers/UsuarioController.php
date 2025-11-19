<?php
// /src/Controllers/UsuarioController.php

class UsuarioController {

    // --- Funciones de Vistas (Calendario, Socios, etc.) ---
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
    // --- MÓDULO DE HORAS (ADAPTADO A LA BASE NUEVA) ---
    // ========================================================

    public function showMisHorasForm() {
        require __DIR__ . '/../Views/html/horasTrabajo.html';
    }

    public function handleHorasSubmit() {
        try {
            // 1. Verificar Login
            if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
                header('Location: /login');
                exit();
            }

            // 2. Recoger datos
            $id_usuario = $_SESSION['user_id'];
            $fecha = $_POST['fecha']; // Aunque la tabla nueva usa FechaRegistro automática, sirve de referencia
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];
            $descripcion = $_POST['descripcion'] ?? '';

            // 3. CÁLCULO DE HORAS (Adaptación para la base de datos nueva)
            // La nueva BD pide "HorasTrabajadas" (cantidad), no inicio/fin.
            // Calculamos la diferencia:
            $inicio = new DateTime($hora_inicio);
            $fin = new DateTime($hora_fin);
            
            // Si el fin es menor al inicio (ej: trabajó de noche hasta el otro día), sumamos 1 día
            if ($fin < $inicio) {
                $fin->modify('+1 day');
            }

            $diferencia = $inicio->diff($fin);
            
            // Convertimos horas y minutos a un número decimal (ej: 1h 30m = 1.5)
            $horasDecimales = $diferencia->h + ($diferencia->i / 60);

            // 4. Conectar a la BD
            $pdo = Database::connect();

            // 5. Insertar en la TABLA NUEVA 'HorasLaborales'
            $sql = "INSERT INTO HorasLaborales (
                        Id_Usuario, 
                        HorasTrabajadas, 
                        Motivo, 
                        Estado, 
                        FechaRegistro
                    ) VALUES (?, ?, ?, 'Pendiente', NOW())";
            
            $stmt = $pdo->prepare($sql);
            
            // Guardamos: ID, Cantidad Calculada, Descripción
            $stmt->execute([$id_usuario, $horasDecimales, $descripcion]);

            // 6. Mensaje de Éxito
            echo "<h1 style='color: green;'>¡Horas Registradas!</h1>";
            echo "<p>Se han registrado <b>" . number_format($horasDecimales, 2) . " horas</b>.</p>";
            echo "<p>Estado: <b>Pendiente de Aprobación</b></p>";
            echo "<a href='/mis-horas'>Cargar más</a>";

        } catch (Exception $e) {
            echo "<h1 style='color:red;'>Error al guardar:</h1>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<a href='/mis-horas'>Volver</a>";
        }
    }

}
?>