<?php
// /src/Controllers/UsuarioController.php

class UsuarioController {

    // (Podemos agregar un __construct() con seguridad más adelante)

    public function mostrarCalendario() {
        // Carga la Vista de calendario
        require __DIR__ . '/../Views/html/calendario.html';
    }

    public function mostrarSocios() {
        // Carga la Vista de socios
        require __DIR__ . '/../Views/html/secciones/socios.html';
    }

    public function mostrarContabilidad() {
        // Carga la Vista de contabilidad
        require __DIR__ . '/../Views/html/secciones/contabilidad.html';
    }

    public function mostrarLegal() {
        // Carga la Vista de legal
        require __DIR__ . '/../Views/html/secciones/legal.html';
    }

    public function mostrarReclamos() {
        // Carga la Vista de reclamos
        require __DIR__ . '/../Views/html/secciones/reclamos.html';
    }

    public function mostrarNovedad1() {
        // Carga la Vista de novedad 1
        require __DIR__ . '/../Views/html/novedades/novedad1.html';
    }

    public function mostrarNovedad2() {
        // Carga la Vista de novedad 2
        require __DIR__ . '/../Views/html/novedades/novedad2.html';
    }

    public function mostrarNovedad3() {
        // Carga la Vista de novedad 3
        require __DIR__ . '/../Views/html/novedades/novedad3.html';
    }

    public function mostrarNovedad4() {
        // Carga la Vista de novedad 4
        require __DIR__ . '/../Views/html/novedades/novedad4.html';
    }

    public function mostrarNovedad5() {
        // Carga la Vista de novedad 5
        require __DIR__ . '/../Views/html/novedades/novedad5.html';
    }

    // (Acá crearías mostrarNovedad6, 7, etc.)

}
?>