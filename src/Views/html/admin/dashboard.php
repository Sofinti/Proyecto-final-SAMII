<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Samantha: Solicitudes de registro pendientes</title>
    <link rel="stylesheet" href="/Views/CSS/dashboard.css">
    <link rel="shortcut icon" href="/img/logo-cooperativa.jpg" type="image/xicon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

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

            <h2>Solicitudes de registro pendientes</h2>

            <section>
                <?php if (empty($pendientes)): ?>
                    <div class="msg">
                        <p class="empty-msg">✅ No hay usuarios pendientes de aprobación.</p>
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
                        <?php foreach ($pendientes as $usuario): ?>
                        <tr>
                            <td><?= $usuario['Id_Usuario'] ?></td>
                            <td><?= htmlspecialchars($usuario['Nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['Apellido']) ?></td>
                            <td><?= htmlspecialchars($usuario['Correo']) ?></td>
                            <td><?= htmlspecialchars($usuario['Cedula']) ?></td>
                            <td>
                                <form action="/admin/habilitar" method="POST">
                                    <input type="hidden" name="id_usuario" value="<?= $usuario['Id_Usuario'] ?>">
                                    <button type="submit" class="btn-habilitar">✅ Habilitar</button>
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