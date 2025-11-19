<?php
// Opcional: activar display de errores mientras debuggeás (quitar en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Samantha: Solicitudes de registro pendientes</title>
    <link rel="stylesheet" href="/Views/CSS/dashboard.css">
    <link rel="shortcut icon" href="/img/logo-cooperativa.jpg" type="image/xicon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
</head>

<body>

    <header>
        <div class="container">
            <h1>Panel de Administración</h1>

            <nav>
                <ul>
                    <li><a href="/">Ir al Inicio</a></li>
                    <li><a href="/logout">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>

        <div class="cons-content">

            <!-- SECCIÓN: Solicitudes de registro pendientes -->
            <section>
                <h2>Solicitudes de registro pendientes</h2>

                <?php if (empty($pendientes) || !is_array($pendientes)): ?>
                    <div class="msg">
                        <p class="empty-msg">No hay usuarios pendientes de aprobación.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Correo</th>
                                <th>Cédula</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientes as $usuario): 
                                // Normalizar campos
                                $idU = isset($usuario['Id_Usuario']) ? htmlspecialchars($usuario['Id_Usuario']) : '';
                                $nom = isset($usuario['Nombre']) ? htmlspecialchars($usuario['Nombre']) : '';
                                $ape = isset($usuario['Apellido']) ? htmlspecialchars($usuario['Apellido']) : '';
                                $mail= isset($usuario['Correo']) ? htmlspecialchars($usuario['Correo']) : '';
                                $ced = isset($usuario['Cedula']) ? htmlspecialchars($usuario['Cedula']) : '';
                            ?>
                                <tr>
                                    <td><?= $idU ?></td>
                                    <td><?= $nom ?></td>
                                    <td><?= $ape ?></td>
                                    <td><?= $mail ?></td>
                                    <td><?= $ced ?></td>
                                    <td>
                                        <form action="/admin/habilitar" method="POST" style="margin:0;">
                                            <input type="hidden" name="id_usuario" value="<?= $idU ?>">
                                            <button type="submit" class="btn-habilitar">✅ Habilitar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <!-- SECCIÓN: Horas Laborales -->
            <section>
                <h2>Horas Laborales (Pendientes)</h2>

                <?php if (empty($pendientesHoras) || !is_array($pendientesHoras)): ?>
                    <div class="msg">
                        <p class="empty-msg">Todo al día. No hay horas para revisar.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Horas</th>
                                <th>Tarea/Motivo</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendientesHoras as $h):
                                $nombreH = isset($h['Nombre']) ? htmlspecialchars($h['Nombre']) : '';
                                $apellidoH = isset($h['Apellido']) ? htmlspecialchars($h['Apellido']) : '';
                                $horas = isset($h['HorasTrabajadas']) ? htmlspecialchars($h['HorasTrabajadas']) : '0';
                                $motivo = isset($h['Motivo']) ? htmlspecialchars($h['Motivo']) : '';
                                $idHoras = isset($h['Id_Horas']) ? htmlspecialchars($h['Id_Horas']) : '';
                            ?>
                                <tr>
                                    <td><?= $nombreH . ' ' . $apellidoH ?></td>
                                    <td><span class="badge"><?= $horas ?> hrs</span></td>
                                    <td><?= $motivo ?></td>
                                    <td>
                                        <form action="/admin/aprobar-horas" method="POST" style="display:flex; gap:6px; margin:0;">
                                            <input type="hidden" name="id_horas" value="<?= $idHoras ?>">
                                            <button type="submit" name="accion" value="Aprobado" class="btn btn-ok">✓</button>
                                            <button type="submit" name="accion" value="Rechazado" class="btn btn-no">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <!-- SECCIÓN: Pagos por Revisar -->
            <section>
                <h2>Pagos por Revisar</h2>

                <?php
                // Normalizar y unir arrays recibidos (evitar foreach sobre null)
                $todosPagos = [];

                if (!empty($pIniciales) && is_array($pIniciales)) {
                    foreach ($pIniciales as $p) {
                        $item = is_array($p) ? $p : [];
                        $item['Tipo'] = 'Inicial';
                        $item['ID_Real'] = isset($item['id_PagoInicial']) ? $item['id_PagoInicial'] : null;
                        $todosPagos[] = $item;
                    }
                }

                if (!empty($pMensuales) && is_array($pMensuales)) {
                    foreach ($pMensuales as $p) {
                        $item = is_array($p) ? $p : [];
                        $item['Tipo'] = 'Mensual';
                        $item['ID_Real'] = isset($item['Id_PagoMensual']) ? $item['Id_PagoMensual'] : null;
                        $todosPagos[] = $item;
                    }
                }
                ?>

                <?php if (empty($todosPagos)): ?>
                    <div class="msg">
                        <p class="empty-msg">La caja está al día.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Comprobante</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todosPagos as $p):
                                $nombreP = isset($p['Nombre']) ? htmlspecialchars($p['Nombre']) : '';
                                $apellidoP = isset($p['Apellido']) ? htmlspecialchars($p['Apellido']) : '';
                                $tipoP = isset($p['Tipo']) ? htmlspecialchars($p['Tipo']) : '';
                                $mesP = isset($p['Mes']) ? htmlspecialchars($p['Mes']) : '';
                                $anoP = isset($p['Ano']) ? htmlspecialchars($p['Ano']) : '';
                                $montoP = isset($p['Monto']) ? htmlspecialchars($p['Monto']) : '0.00';
                                $comprobP = isset($p['Comprobante_url']) ? htmlspecialchars($p['Comprobante_url']) : '';
                                $idRealP = isset($p['ID_Real']) ? htmlspecialchars($p['ID_Real']) : '';
                            ?>
                                <tr>
                                    <td><?= $nombreP . ' ' . $apellidoP ?></td>
                                    <td>
                                        <span class="badge"><?= $tipoP ?></span>
                                        <?= ($tipoP === 'Mensual' && $mesP !== '' && $anoP !== '') ? (' ' . $mesP . '/' . $anoP) : '' ?>
                                    </td>
                                    <td style="color: green; font-weight: bold;">$<?= $montoP ?></td>
                                    <td>
                                        <?php if (!empty($comprobP)): ?>
                                            <a href="<?= $comprobP ?>" target="_blank" class="btn-link">Ver Foto</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="/admin/gestionar-pago" method="POST" style="display:flex; gap:6px; margin:0;">
                                            <input type="hidden" name="id_pago" value="<?= $idRealP ?>">
                                            <input type="hidden" name="tipo" value="<?= $tipoP ?>">
                                            <button type="submit" name="accion" value="Aprobado" class="btn btn-ok">✓</button>
                                            <button type="submit" name="accion" value="Rechazado" class="btn btn-no">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

        </div>

    </main>

    <footer>
        <div class="container">
            <div class="col-3">
                <h5><a href="/socios">Socios</a></h5>
                <ul>
                    <li><a href="">Libro de socios</a></li>
                    <li><a href="">Nómina de socios</a></li>
                    <li><a href="">Control de recursos humanos</a></li>
                </ul>
            </div>

            <div class="col-4">
                <h5><a href="/contabilidad">Contabilidad</a></h5>
                <ul>
                    <li><a href="">Pago de servicios</a></li>
                    <li><a href="">Libro contable</a></li>
                    <li><a href="">Gastos comunes</a></li>
                    <li><a href="https://www.anv.gub.uy/solicitud-de-subsidios-para-cooperativas">¿Cómo gestionar un subsidio?</a></li>
                </ul>
            </div>

            <div class="col-2">
                <h5><a href="/legal">Legal</a></h5>
                <ul>
                    <li><a href="">Actas</a></li>
                    <li><a href="https://www.gub.uy/junta-transparencia-etica-publica/politicas-y-gestion/gestion-declaraciones-juradas#:~:text=Descripci%C3%B3n,archivo%20de%20las%20declaraciones%20juradas.">Declaraciones juradas</a></li>
                </ul>
            </div>

            <div class="col-2">
                <h5><a href="/reclamos">Reclamos</a></h5>
                <ul>
                    <li><a href="/Documentos/">Formulario de reclamo</a></li>
                    <li><a href="/Documentos/">Formulario de denuncia</a></li>
                </ul>
            </div>
        </div>

        <div class="grupo-2">
            <small>© Todos los derechos están reservados a SAMII™.</small>
            <img src="/img/Diseño sin título.png" alt="logo-empresa">
        </div>
    </footer>

</body>

</html>
