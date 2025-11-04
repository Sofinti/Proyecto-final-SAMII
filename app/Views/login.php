<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SAMII</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($_GET['registro']) && $_GET['registro'] == 'exitoso'): ?>
            <div class="success">¡Registro exitoso! Ya puedes iniciar sesión.</div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Entrar</button>
        </form>

        <p>¿No tienes cuenta? <a href="/registro">Regístrate aquí</a></p>
    </div>
</body>
</html>
