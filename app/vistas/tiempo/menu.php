<div class="cabecera-pagina">
    <h1><?= $ubicacion['ciudad'] ?>, <?= $ubicacion['pais'] ?></h1>
    <p class="gris">Lat: <?= number_format($ubicacion['lat'], 4) ?> &mdash; Lon: <?= number_format($ubicacion['lon'], 4) ?></p>
</div>

<?php
$params = http_build_query([
    'ciudad' => $ubicacion['ciudad'],
    'pais'   => $ubicacion['pais'],
    'lat'    => $ubicacion['lat'],
    'lon'    => $ubicacion['lon'],
    'id'     => $ubicacion['id'],
]);
$opciones = [
    ['icono' => '&#9728;',  'titulo' => 'Tiempo actual',       'url' => "/tiempo/actual?{$params}", 'desc' => 'Temperatura, humedad y viento en este momento.'],
    ['icono' => '&#128336;','titulo' => 'Previsión por horas', 'url' => "/tiempo/horas?{$params}",  'desc' => 'Evolución del tiempo durante el día de hoy.'],
    ['icono' => '&#128197;','titulo' => 'Previsión semanal',   'url' => "/tiempo/semana?{$params}", 'desc' => 'Resumen del tiempo para los próximos 5 días.'],
];
?>

<div class="tarjetas-menu">
    <?php foreach ($opciones as $opcion): ?>
    <a href="<?= $opcion['url'] ?>" class="tarjeta-menu">
        <span class="icono-menu"><?= $opcion['icono'] ?></span>
        <h2><?= $opcion['titulo'] ?></h2>
        <p><?= $opcion['desc'] ?></p>
    </a>
    <?php endforeach; ?>
</div>
