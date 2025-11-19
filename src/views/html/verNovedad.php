<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samantha: Novedad</title>
    <link rel="stylesheet" href="/views/CSS/estilos.css">
    <link rel="shortcut icon" href="/img/logo-cooperativa.jpg" type="image/xicon">
    <style>
        /* Un poco de estilo extra para que la noticia se vea linda */
        .novedad-detalle { max-width: 800px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .novedad-img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 10px; margin-bottom: 20px; }
        .meta-info { color: gray; font-size: 0.9rem; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .contenido-full { font-size: 1.1rem; line-height: 1.6; color: #333; }
        .btn-volver { display: inline-block; margin-top: 30px; text-decoration: none; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <a id="logo" href="/"><img src="/img/logo-cooperativa.jpg" alt="Samantha"></a>
            <nav><ul><li><a href="/">⬅ Volver al Inicio</a></li></ul></nav>
        </div>
    </header>

    <main>
        <article class="novedad-detalle">
            
            <h1><?= htmlspecialchars($noticia['Titulo']) ?></h1>
            
            <div class="meta-info">
                Publicado el: <?= date('d/m/Y', strtotime($noticia['FechaPublicacion'])) ?>
            </div>

            <?php if (!empty($noticia['Imagen_url'])): ?>
                <?php 
                    // Limpieza de ruta por si acaso
                    $img = '/' . str_replace('src/', '', $noticia['Imagen_url']);
                ?>
                <img src="<?= $img ?>" alt="Imagen Noticia" class="novedad-img">
            <?php endif; ?>

            <div class="contenido-full">
                <?= nl2br(htmlspecialchars($noticia['Contenido'])) ?>
            </div>

            <a href="/" class="btn-volver">← Volver a Novedades</a>
        </article>
    </main>

    <footer>
        <div class="grupo-2">
            <small>© Todos los derechos están reservados a SAMII™.</small>
        </div>
    </footer>

</body>
</html>