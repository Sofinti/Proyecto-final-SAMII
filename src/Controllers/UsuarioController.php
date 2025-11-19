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
            echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>¡Horas Registradas!</h1> 
                            <p style='color: #FBFBFF;'>Se han registrado <b style='color: #FBFBFF;'>" . number_format($horasDecimales, 2) . " horas</b>.</p>
                            <p style='color: #FBFBFF;'>Estado: <b style='color: #FBFBFF;'>Pendiente de Aprobación</b></p>
                            <a href='/mis-horas' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Cargar más</a>
                        </div>
                    </div>
                ";

        } catch (Exception $e) {
            echo "<div style='display: flex; justify-content: center; align-items: center; height: 100%;margin: 0; background: #585858cc'>
                        <div style='background: #003f7f;
                        background-position: center;
                        background-repeat: no-repeat;
                        background-size: cover;
                        justify-self: center;
                        padding: 30px;
                        padding-bottom: 50px;
                        width: auto;
                        height: auto;
                        max-width: 550px;
                        border-radius: 25px;'>
                            <h1 class='bad' style='color: #FBFBFF;'>Error al guardar:</h1>
                            <p style='color: #FBFBFF;'>" . $e->getMessage() . "</p>
                            <a href='/mis-horas' style='display: flex;
                            justify-self: center;
                            background: #FBFBFF;
                            font-size: 16px;
                            padding: 8px 12px;
                            border: 2px solid #003f7f;
                            border-radius: 20px;
                            cursor: pointer;
                            box-shadow: 0px 3px 1px -2px rgba(0, 0, 0, .2);
                            transition: .3s;
                            text-decoration: none;
                            color: #003f7f;'>Volver</a>
                        </div>
                    </div>
                ";
        }
    }

}
?>