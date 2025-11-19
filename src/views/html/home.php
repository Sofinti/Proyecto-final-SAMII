<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samantha: Inicio</title>

    <link rel="stylesheet" href="/views/CSS/estilos.css">
    <link rel="shortcut icon" href="/img/logo-cooperativa.jpg" type="image/xicon">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>

<body>

    <header>
        <div class="container">
            <a id="logo" href="/"><img src="/img/logo-cooperativa.jpg" alt="Samantha"></a>

            <form id="search-form" method="get" action="/search">
                <div class="search-content">
                    <input type="text" id="search-input" name="q" placeholder="Buscar...">
                    <button type="submit">Buscar</button>
                </div>
            </form>

            <nav>
                <ul>
                    <li><a href="/mis-horas">⏱️ Horas</a></li>
                    <li><a href="/mis-pagos">💰 Pagos</a></li>
                    <li><a href="/calendario">Calendario</a></li>
                    
                    <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] == 1): ?>
                        <li><a href="/admin/dashboard" style="color: orange; font-weight: bold;">👑 Admin</a></li>
                    <?php endif; ?>
                    
                    <li><a href=""><img src="/img/Iconos/user.png" alt="Icono-usuario"></a>
                        <ul>
                            <li><a href="/mi-cuenta">Mi cuenta</a></li>
                            <li><a href="">Configuración</a></li>
                            <li><a href="/logout">Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section id="hero">
            <img src="/img/imgCooperativa.jpeg" alt="Imágen de la cooperativa">
        </section>

        <article class="info-content">
            <section class="section container">

                <h2>Información</h2>

                <div class="section-content">
                    <div class="section-1">
                        <div class="icono-section"><img src="/img/Iconos/Modo claro/socios.png" alt="icono-socios"></div>
                        <div class="section-txt">
                            <h4>Socios</h4>
                            <p>Aquí encontrarás lo relacionado con los socios.</p>
                            <a href="/socios" class="btn-1">Ver más ></a>
                        </div>
                    </div>

                    <div class="section-1">
                        <div class="icono-section"><img src="/img/Iconos/Modo claro/contabilidad.png" alt="icono-contabilidad"></div>
                        <div class="section-txt">
                            <h4>Contabilidad</h4>
                            <p>Aquí encontrarás todo lo que tenga que ver con finanzas y contabilidad.</p>
                            <a href="/contabilidad" class="btn-1">Ver más ></a>
                        </div>
                    </div>

                    <div class="section-1">
                        <div class="icono-section"><img src="/img/Iconos/Modo claro/legal.png" alt="icono-legal"></div>
                        <div class="section-txt">
                            <h4>Libros legales</h4>
                            <p>Aquí encontrarás todo lo que tenga que ver con documentos legales.</p>
                            <a href="/legal" class="btn-1">Ver más ></a>
                        </div>
                    </div>

                    <div class="section-1">
                        <div class="icono-section"><img src="/img/Iconos/Modo claro/reclamo.png" alt="icono-reclamos"></div>
                        <div class="section-txt">
                            <h4>Reclamos</h4>
                            <p>Aquí encontrarás todo lo que tenga que ver con lo necesario para realizar reclamos.</p>
                            <a href="/reclamos" class="btn-1">Ver más ></a>
                        </div>
                    </div>

                </div>
            </section>

            <section class="novedades container">

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Novedades Recientes</h2>

                    <?php if (isset($_SESSION['Rol']) && $_SESSION['Rol'] == 1): ?>
                        <a href="/admin/nueva-novedad" style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 0.9rem;">
                            + Nueva Noticia
                        </a>
                    <?php endif; ?>
                </div>

                <div class="novedades-content">

                    <?php if (empty($novedades)): ?>
                        <div class="novedad-1">
                            <div class="novedad-txt">
                                <h4>¡Bienvenido!</h4>
                                <p>Aún no hay noticias publicadas. Pronto verás novedades aquí.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($novedades as $nov): ?>
                            <div class="novedad-1">
                                <?php 
                                    // Lógica de imagen
                                    $img = !empty($nov['Imagen_url']) 
                                        ? '/' . str_replace('src/', '', $nov['Imagen_url']) 
                                        : '/img/Icono defaul de noticias.png'; 
                                ?>
                                
                                <img src="<?= $img ?>" alt="Noticia" style="max-width: 100%; height: auto;">
                                
                                <div class="novedad-txt">
                                    <h4><?= htmlspecialchars($nov['Titulo']) ?></h4>
                                    <small style="color: gray; font-size: 0.8em;">
                                        Publicado el: <?= date('d/m/Y', strtotime($nov['FechaPublicacion'])) ?>
                                    </small>
                                    
                                    <p>
                                        <?= nl2br(htmlspecialchars(substr($nov['Contenido'], 0, 150))) ?>...
                                    </p>

                                    <a href="/ver-novedad?id=<?= $nov['Id_Novedad'] ?>" class="btn-1" style="display: inline-block; margin-top: 10px;">
                                        Leer completa >
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </section>

        </article>
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