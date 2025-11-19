<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="/views/CSS/estilos.css">
    <style>
        body { background-color: #f4f4f4; }
        .dashboard-container { max-width: 1200px; margin: 40px auto; padding: 20px; }
        
        .card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h3 { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: #333; }
        
        .btn-action { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; color: white; font-size: 0.9rem; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #dc3545; }
        .empty-msg { color: #777; font-style: italic; text-align: center; padding: 20px; }

        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-number { font-size: 2rem; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <a id="logo" href="/">⬅ Volver al Sitio</a>
            <nav><ul><li>ADMINISTRADOR</li></ul></nav>
        </div>
    </header>

    <main class="dashboard-container">
        <h1>👑 Panel de Control</h1>
        
        <div class="stats">
            <div class="stat-box">
                <a href="/admin/nueva-novedad" style="text-decoration: none; color: inherit;">
                    <h3>📢 Noticias</h3>
                    <button class="btn-action btn-approve" style="width: 100%; margin-top: 10px;">+ Publicar Nueva</button>
                </a>
            </div>
            </div>

        <div class="card">
            <h3>👤 Usuarios Nuevos (Pendientes de Habilitar)</h3>
            <?php if (empty($pendientesUser)): ?>
                <div class="empty-msg">✅ No hay usuarios pendientes.</div>
            <?php else: ?>
                <table>
                    <thead><tr><th>Nombre</th><th>Cédula</th><th>Correo</th><th>Acción</th></tr></thead>
                    <tbody>
                        <?php foreach ($pendientesUser as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['Nombre'] . ' ' . $u['Apellido']) ?></td>
                            <td><?= htmlspecialchars($u['Cedula']) ?></td>
                            <td><?= htmlspecialchars($u['Correo']) ?></td>
                            <td>
                                <form action="/admin/habilitar" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_usuario" value="<?= $u['Id_Usuario'] ?>">
                                    <button type="submit" class="btn-action btn-approve">Habilitar Acceso</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>⏱️ Horas Reportadas (Pendientes de Aprobación)</h3>
            <?php if (empty($pendientesHoras)): ?>
                <div class="empty-msg">✅ Todo al día. No hay horas para revisar.</div>
            <?php else: ?>
                <table>
                    <thead><tr><th>Socio</th><th>Fecha</th><th>Horas</th><th>Motivo</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($pendientesHoras as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['Nombre'] . ' ' . $h['Apellido']) ?></td>
                            <td><?= date('d/m/Y', strtotime($h['FechaRegistro'])) ?></td>
                            <td><strong><?= $h['HorasTrabajadas'] ?> hs</strong></td>
                            <td><?= htmlspecialchars($h['Motivo']) ?></td>
                            <td>
                                <form action="/admin/aprobar-horas" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_horas" value="<?= $h['Id_Horas'] ?>">
                                    <button type="submit" name="accion" value="Aprobado" class="btn-action btn-approve">✓</button>
                                    <button type="submit" name="accion" value="Rechazado" class="btn-action btn-reject">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>💰 Pagos Reportados (Requieren Revisión)</h3>
            
            <?php if (empty($pIniciales) && empty($pMensuales)): ?>
                <div class="empty-msg">✅ No hay pagos pendientes.</div>
            <?php else: ?>
                <table>
                    <thead><tr><th>Socio</th><th>Tipo</th><th>Monto</th><th>Comprobante</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($pIniciales as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['Nombre'] . ' ' . $p['Apellido']) ?></td>
                            <td><span style="color: #d63384; font-weight: bold;">INICIAL</span></td>
                            <td>$<?= $p['Monto'] ?></td>
                            <td>
                                <?php if($p['Comprobante_url']): ?>
                                    <a href="<?= $p['Comprobante_url'] ?>" target="_blank">Ver Foto</a>
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td>
                                <form action="/admin/gestionar-pago" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pago" value="<?= $p['id_PagoInicial'] ?>">
                                    <input type="hidden" name="tipo" value="Inicial">
                                    <button type="submit" name="accion" value="Aprobado" class="btn-action btn-approve">✓</button>
                                    <button type="submit" name="accion" value="Rechazado" class="btn-action btn-reject">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php foreach ($pMensuales as $pm): ?>
                        <tr>
                            <td><?= htmlspecialchars($pm['Nombre'] . ' ' . $pm['Apellido']) ?></td>
                            <td>Mensual (<?= $pm['Mes'] ?>/<?= $pm['Ano'] ?>)</td>
                            <td>$<?= $pm['Monto'] ?></td>
                            <td>
                                <?php if($pm['Comprobante_url']): ?>
                                    <a href="<?= $pm['Comprobante_url'] ?>" target="_blank">Ver Foto</a>
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td>
                                <form action="/admin/gestionar-pago" method="POST" style="display:inline;">
                                    <input type="hidden" name="id_pago" value="<?= $pm['Id_PagoMensual'] ?>">
                                    <input type="hidden" name="tipo" value="Mensual">
                                    <button type="submit" name="accion" value="Aprobado" class="btn-action btn-approve">✓</button>
                                    <button type="submit" name="accion" value="Rechazado" class="btn-action btn-reject">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </main>
</body>
</html>