<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta - Samantha</title>
    <link rel="stylesheet" href="/views/CSS/estilos.css">
    <style>
        .perfil-container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-guardar { background: #007bff; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn-guardar:hover { background: #0056b3; }
        .aviso-pass { font-size: 0.85rem; color: #666; margin-top: 5px; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <a id="logo" href="/">⬅ Volver al Inicio</a>
        </div>
    </header>

    <main>
        <div class="perfil-container">
            <h2 style="text-align: center;">👤 Mi Perfil</h2>
            
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="alert alert-success">¡Datos actualizados correctamente!</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                <div class="alert alert-danger">Ocurrió un error al guardar.</div>
            <?php endif; ?>

            <form action="/actualizar-perfil" method="POST">
                
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['Nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellido:</label>
                    <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['Apellido']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="correo" value="<?= htmlspecialchars($usuario['Correo']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['Telefono'] ?? '') ?>" placeholder="Ej: 099123456">
                </div>

                <hr style="margin: 20px 0; border-top: 1px solid #eee;">

                <div class="form-group">
                    <label>Nueva Contraseña:</label>
                    <input type="password" name="password" placeholder="Dejar en blanco para NO cambiar">
                    <p class="aviso-pass">ℹ️ Solo escribí acá si querés cambiar tu clave actual.</p>
                </div>

                <button type="submit" class="btn-guardar">Guardar Cambios</button>
            </form>
        </div>
    </main>

</body>
</html>