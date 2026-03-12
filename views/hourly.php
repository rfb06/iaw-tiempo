<?php
$pageLabel = 'Por horas';
?>

<h1><?= htmlspecialchars($geo['name']) ?>, <?= htmlspecialchars($geo['country']) ?></h1>
<p class="coords">Hoy · <?= date('d/m/Y') ?></p>

<?php if (empty($hours)): ?>
    <p style="color:#888;font-size:14px">No hay franjas horarias disponibles para hoy.</p>
<?php else: ?>

<div class="hourly">
    <?php foreach ($hours as $h): ?>
    <div class="hcard">
        <div class="ht"><?= $h['time'] ?></div>
        <img src="https://openweathermap.org/img/wn/<?= $h['icon'] ?>.png" alt="">
        <div class="htemp"><?= $h['temperature'] ?>°</div>
        <?php if ($h['pop'] > 0): ?><div class="hpop">💧<?= $h['pop'] ?>%</div><?php endif ?>
    </div>
    <?php endforeach ?>
</div>

<table>
    <thead>
        <tr>
            <th>Hora</th><th>Temp.</th><th>Sensación</th><th>Humedad</th><th>Viento</th><th>Lluvia</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($hours as $h): ?>
        <tr>
            <td><strong><?= $h['time'] ?></strong></td>
            <td><?= $h['temperature'] ?>°C</td>
            <td><?= $h['feels_like'] ?>°C</td>
            <td><?= $h['humidity'] ?>%</td>
            <td><?= $h['wind_speed'] ?> m/s</td>
            <td><?= $h['pop'] > 0 ? $h['pop'].'%' : '—' ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php endif ?>

<div class="nav-row">
    <a class="btn btn-plain" href="<?= APP_BASE ?>/city?<?= WeatherController::geoQs($geo) ?>">← Volver</a>
    <a class="btn btn-plain" href="<?= APP_BASE ?>/current?<?= WeatherController::geoQs($geo) ?>">Actual</a>
    <a class="btn btn-plain" href="<?= APP_BASE ?>/weekly?<?= WeatherController::geoQs($geo) ?>">Semana</a>
</div>
