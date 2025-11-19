<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="/src/Views/CSS/estilos.css">
    <style>
        /* Estilos simples para la tabla */
        body { padding: 20px; font-family: sans-serif; }
        .admin-container { max-width: 1000px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn-habilitar {
            background-color: #28a745; color: white; padding: 8px 12px;
            text-decoration: none; border: none; border-radius: 4px; cursor: pointer;
        }
        .btn-habilitar:hover { background-color: #218838; }
        .empty-msg { color: #666; font-style: italic; margin-top: 20px; }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <h1>Panel de Administración</h1>
            <nav>
                <a href="/">Ir al Inicio</a> | 
                <a href="/logout">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="admin-container">
        <h2>Solicitudes de Registro Pendientes</h2>

        <?php if (empty($pendientes)): ?>
            
            <p class="empty-msg">✅ No hay usuarios pendientes de aprobación.</p>

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

    </main>

</body>
</html>