<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/WeatherController.php';

$route = trim($_GET['url'] ?? '', '/');

$controller = new WeatherController();

// Tabla de rutas
$routes = [
    ''        => ['controller' => $controller, 'method' => 'index'],
    'search'  => ['controller' => $controller, 'method' => 'search'],
    'city'    => ['controller' => $controller, 'method' => 'city'],
    'current' => ['controller' => $controller, 'method' => 'current'],
    'hourly'  => ['controller' => $controller, 'method' => 'hourly'],
    'weekly'  => ['controller' => $controller, 'method' => 'weekly'],
];

if (isset($routes[$route])) {
    $r = $routes[$route];
    $r['controller']->{$r['method']}();
} else {
    http_response_code(404);
    require __DIR__ . '/views/layout/header.php';
    echo '<div class="not-found"><h2>404</h2><p>Página no encontrada</p><a href="' . APP_BASE . '/">← Volver al inicio</a></div>';
    require __DIR__ . '/views/layout/footer.php';
}
