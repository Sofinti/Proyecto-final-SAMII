<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SAMII</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="registro-container">
        <h2>Crear Cuenta</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="/registro">
            <div class="form-group">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required minlength="6">
                <small>Mínimo 6 caracteres</small>
            </div>
            
            <button type="submit">Registrarse</button>
        </form>

        <p>¿Ya tienes cuenta? <a href="/login">Inicia sesión aquí</a></p>
    </div>
</body>
</html>
