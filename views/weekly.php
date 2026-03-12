<?php
$pageLabel = 'Semanal';
?>

<h1><?= htmlspecialchars($geo['name']) ?>, <?= htmlspecialchars($geo['country']) ?></h1>
<p class="coords">Próximos 5 días · desde <?= date('d/m/Y') ?></p>

<?php if (empty($days)): ?>
    <p style="color:#888;font-size:14px">No hay datos disponibles.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Día</th><th></th><th>Descripción</th><th>Máx</th><th>Mín</th><th>Humedad</th><th>Viento</th><th>Lluvia</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($days as $d): ?>
        <tr>
            <td><strong><?= htmlspecialchars($d['label']) ?></strong></td>
            <td><img src="https://openweathermap.org/img/wn/<?= $d['icon'] ?>.png" width="32" height="32" alt=""></td>
            <td style="font-size:12px;color:#555"><?= htmlspecialchars($d['description']) ?></td>
            <td><strong><?= $d['temp_max'] ?>°C</strong></td>
            <td style="color:#888"><?= $d['temp_min'] ?>°C</td>
            <td><?= $d['humidity'] ?>%</td>
            <td><?= $d['wind_speed'] ?> m/s</td>
            <td><?= $d['pop'] > 0 ? $d['pop'].'%' : '—' ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php endif ?>

<div class="nav-row">
    <a class="btn btn-plain" href="<?= APP_BASE ?>/city?<?= WeatherController::geoQs($geo) ?>">← Volver</a>
    <a class="btn btn-plain" href="<?= APP_BASE ?>/current?<?= WeatherController::geoQs($geo) ?>">Actual</a>
    <a class="btn btn-plain" href="<?= APP_BASE ?>/hourly?<?= WeatherController::geoQs($geo) ?>">Por horas</a>
</div>
