<?php
$pageLabel = 'Tiempo actual';
function windDir(int $deg): string {
    $dirs = ['N','NE','E','SE','S','SO','O','NO'];
    return $dirs[round($deg / 45) % 8];
}
?>

<h1><?= htmlspecialchars($geo['name']) ?>, <?= htmlspecialchars($geo['country']) ?></h1>
<p class="coords"><?= $weather['dt'] ?></p>

<div class="current-main">
    <img src="https://openweathermap.org/img/wn/<?= $weather['icon'] ?>@2x.png" alt="">
    <div>
        <div class="big-temp"><?= $weather['temperature'] ?><sup>°C</sup></div>
        <div class="desc"><?= htmlspecialchars($weather['description']) ?></div>
        <div class="feels">Sensación: <?= $weather['feels_like'] ?>°C</div>
    </div>
</div>

<div class="stats">
    <div class="stat"><div class="lbl">Mín / Máx</div><div class="val" style="font-size:15px"><?= $weather['temp_min'] ?>° / <?= $weather['temp_max'] ?>°</div></div>
    <div class="stat"><div class="lbl">Humedad</div><div class="val"><?= $weather['humidity'] ?><span class="unit">%</span></div></div>
    <div class="stat"><div class="lbl">Viento</div><div class="val"><?= $weather['wind_speed'] ?><span class="unit"> m/s</span></div><div style="font-size:11px;color:#888"><?= windDir($weather['wind_deg']) ?></div></div>
    <div class="stat"><div class="lbl">Presión</div><div class="val"><?= $weather['pressure'] ?><span class="unit"> hPa</span></div></div>
    <?php if ($weather['visibility'] !== null): ?>
    <div class="stat"><div class="lbl">Visibilidad</div><div class="val"><?= $weather['visibility'] ?><span class="unit"> km</span></div></div>
    <?php endif ?>
    <div class="stat"><div class="lbl">Nubosidad</div><div class="val"><?= $weather['clouds'] ?><span class="unit">%</span></div></div>
    <div class="stat"><div class="lbl">Amanecer</div><div class="val" style="font-size:16px"><?= $weather['sunrise'] ?></div></div>
    <div class="stat"><div class="lbl">Atardecer</div><div class="val" style="font-size:16px"><?= $weather['sunset'] ?></div></div>
</div>

<div class="nav-row">
    <a class="btn btn-plain" href="<?= APP_BASE ?>/city?<?= WeatherController::geoQs($geo) ?>">← Volver</a>
    <a class="btn btn-plain" href="<?= APP_BASE ?>/hourly?<?= WeatherController::geoQs($geo) ?>">Por horas</a>
    <a class="btn btn-plain" href="<?= APP_BASE ?>/weekly?<?= WeatherController::geoQs($geo) ?>">Semana</a>
</div>
