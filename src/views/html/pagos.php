<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Pagos - Samantha</title>
    <link rel="stylesheet" href="/src/Views/CSS/estilos.css">
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .pagos-container { max-width: 900px; margin: auto; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 10px; color: #333; }
        
        /* Tablas */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f1f1f1; }
        
        /* Estados */
        .estado-pendiente { color: orange; font-weight: bold; }
        .estado-aprobado { color: green; font-weight: bold; }
        .estado-rechazado { color: red; font-weight: bold; }

        /* Formularios */
        .form-pago { display: grid; gap: 15px; margin-top: 15px; }
        input, select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="/">Volver al Inicio</a>
        </div>
    </header>

    <main class="pagos-container">
        <h1>Gestión de Pagos</h1>

        <section class="card">
            <h2>🏠 Cuota Inicial</h2>
            
            <?php if ($pagoInicial): ?>
                <p>Estado de tu Cuota Inicial: 
                    <span class="estado-<?= strtolower($pagoInicial['Estado']) ?>">
                        <?= $pagoInicial['Estado'] ?>
                    </span>
                </p>
                <p>Monto: $<?= $pagoInicial['Monto'] ?></p>
                <p>Fecha: <?= $pagoInicial['Fecha'] ?></p>
                <?php if($pagoInicial['Comprobante_url']): ?>
                    <a href="<?= $pagoInicial['Comprobante_url'] ?>" target="_blank">Ver Comprobante</a>
                <?php endif; ?>

            <?php else: ?>
                <p>Aún no has registrado tu cuota inicial.</p>
                <form action="/pagar-inicial" method="POST" enctype="multipart/form-data" class="form-pago">
                    <label>Monto a Pagar:</label>
                    <input type="number" name="monto" required placeholder="Ej: 50000">
                    
                    <label>Subir Comprobante (Foto/PDF):</label>
                    <input type="file" name="comprobante" required accept="image/*,.pdf">
                    
                    <button type="submit">Registrar Pago Inicial</button>
                </form>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2>📅 Pagar Mes Actual</h2>
            <form action="/pagar-mensual" method="POST" enctype="multipart/form-data" class="form-pago" style="grid-template-columns: 1fr 1fr 1fr; align-items: end;">
                <div>
                    <label>Mes:</label>
                    <select name="mes">
                        <?php for($i=1; $i<=12; $i++) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                </div>
                <div>
                    <label>Año:</label>
                    <input type="number" name="ano" value="<?= date('Y') ?>">
                </div>
                <div>
                    <label>Monto:</label>
                    <input type="number" name="monto" placeholder="$">
                </div>
                <div style="grid-column: span 3;">
                    <label>Comprobante:</label>
                    <input type="file" name="comprobante" required>
                </div>
                <button type="submit" style="grid-column: span 3;">Registrar Pago Mensual</button>
            </form>
        </section>

        <section class="card">
            <h2>📜 Historial de Gastos Comunes</h2>
            <?php if (empty($pagosMensuales)): ?>
                <p>No hay pagos registrados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Periodo</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagosMensuales as $pago): ?>
                        <tr>
                            <td><?= $pago['Mes'] ?>/<?= $pago['Ano'] ?></td>
                            <td>$<?= $pago['Monto'] ?></td>
                            <td>
                                <span class="estado-<?= strtolower($pago['Estado']) ?>">
                                    <?= $pago['Estado'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if($pago['Comprobante_url']): ?>
                                    <a href="<?= $pago['Comprobante_url'] ?>" target="_blank">Ver</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

    </main>
</body>
</html>