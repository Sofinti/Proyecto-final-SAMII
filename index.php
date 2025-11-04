<?php

// SIMULACION DE ROUTER

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/':
        require __DIR__ . '/src/Vista/html/index.html';

        break;

        // Ya tienen el router basico, y el docker-compose.yml

    default:
        http_response_code(404);
        break;
}
