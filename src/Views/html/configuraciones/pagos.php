<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samantha: Configuración</title>

    <link rel="stylesheet" href="/Views/CSS/configuraciones/pagos.css">
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
            <a id="logo" href="/Views/html/index.html"><img src="/img/logo-cooperativa.jpg" alt="Samantha"></a>

            <form id="search-form" method="get" action="/search">
                <div class="search-content">
                    <input type="text" id="search-input" name="q" placeholder="Buscar...">
                    <button type="submit">Buscar</button>
                </div>
            </form>

            <nav>
                <ul>
                    <li><a href="/Views/html/calendario.html">Calendario</a></li>
                    <li><a href=""><img src="/img/Iconos/user.png" alt="Icono-usuario"></a>
                        <ul>
                            <li><a href="/logout">Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="data container">

            <section class="titles">

                <ul>
                    <li><a href="/Views/html/configuraciones/miCuenta.html">Mi cuenta</a></li>
                    <li><a href="/Views/html/configuraciones/preferencias.html">Preferencias</a></li>
                    <li><a href="#" class="active">| Pagos e historial</a></li> 
                </ul>

            </section>

            <section class="info-content">

                <section class="card">
                    <h2>Cuota Inicial</h2>

                    <?php if ($pagoInicial): ?>
                        <p>Estado de tu Cuota Inicial:
                            <span class="estado-<?= strtolower($pagoInicial['Estado']) ?>">
                                <?= $pagoInicial['Estado'] ?>
                            </span>
                        </p>
                        <p>Monto: $
                            <?= $pagoInicial['Monto'] ?>
                        </p>
                        <p>Fecha:
                            <?= $pagoInicial['Fecha'] ?>
                        </p>
                        <?php if ($pagoInicial['Comprobante_url']): ?>
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
                    <h2>Pagar Mes Actual</h2>
                    <form action="/pagar-mensual" method="POST" enctype="multipart/form-data" class="form-pago"
                        style="grid-template-columns: 1fr 1fr 1fr; align-items: end;">
                        <div>
                            <label>Mes:</label>
                            <select name="mes">
                                <?php for ($i = 1; $i <= 12; $i++) echo "<option value='$i'>$i</option>"; ?>
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
                    <h2>Historial de Gastos Comunes</h2>
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
                                        <td>
                                            <?= $pago['Mes'] ?>/
                                            <?= $pago['Ano'] ?>
                                        </td>
                                        <td>$
                                            <?= $pago['Monto'] ?>
                                        </td>
                                        <td>
                                            <span class="estado-<?= strtolower($pago['Estado']) ?>">
                                                <?= $pago['Estado'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pago['Comprobante_url']): ?>
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

            </section>

        </div>

    </main>

    <footer>
        <div class="container">
            <div class="col-3">
                <h5><a href="/Views/html/secciones/socios.html">Socios</a></h5>
                <ul>
                    <li><a href="">Libro de socios</a></li>
                    <li><a href="">Nómina de socios</a></li>
                    <li><a href="">Control de recursos humanos</a></li>
                </ul>
            </div>

            <div class="col-4">
                <h5><a href="/Views/html/secciones/contabilidad.html">Contabilidad</a></h5>
                <ul>
                    <li><a href="">Pago de servicios</a></li>
                    <li><a href="">Libro contable</a></li>
                    <li><a href="">Gastos comunes</a></li>
                    <li><a href="https://www.anv.gub.uy/solicitud-de-subsidios-para-cooperativas">¿Cómo gestionar un
                            subsidio?</a></li>
                </ul>
            </div>

            <div class="col-2">
                <h5><a href="/Views/html/secciones/legal.html">Legal</a></h5>
                <ul>
                    <li><a href="actas.html">Actas</a></li>
                    <li><a
                            href="https://www.gub.uy/junta-transparencia-etica-publica/politicas-y-gestion/gestion-declaraciones-juradas#:~:text=Descripci%C3%B3n,archivo%20de%20las%20declaraciones%20juradas.">Declaraciones
                            juradas</a></li>
                </ul>
            </div>

            <div class="col-2">
                <h5><a href="/Views/html/secciones/reclamos.html">Reclamos</a></h5>
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