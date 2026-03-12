<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/modelos/BaseDatos.php';
require_once __DIR__ . '/../app/modelos/ServicioTiempo.php';
require_once __DIR__ . '/../app/dao/AccesoDatosTiempo.php';
require_once __DIR__ . '/../app/controladores/ControladorTiempo.php';

// Enrutador simple
$ruta   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

$controlador = new ControladorTiempo();

match (true) {
    $ruta === '/'                              => $controlador->inicio(),
    $ruta === '/buscar' && $metodo === 'POST'  => $controlador->buscar(),
    $ruta === '/tiempo/menu'                   => $controlador->menu(),
    $ruta === '/tiempo/actual'                 => $controlador->actual(),
    $ruta === '/tiempo/horas'                  => $controlador->horas(),
    $ruta === '/tiempo/semana'                 => $controlador->semana(),
    default => (function() {
        http_response_code(404);
        echo '<h1>404 - Página no encontrada</h1>';
    })(),
};
