<?php
function windDir(int $deg): string {
    $dirs = ['N','NE','E','SE','S','SO','O','NO'];
    return $dirs[round($deg / 45) % 8];
}
?>

<h1><?= htmlspecialchars($geo['name']) ?>, <?= htmlspecialchars($geo['country']) ?></h1>
<p class="coords">
    <?php if ($geo['state']): ?><?= htmlspecialchars($geo['state']) ?> · <?php endif ?>
    <?= number_format($geo['lat'], 4) ?>, <?= number_format($geo['lon'], 4) ?>
</p>

<?php if (!empty($apiError)): ?>
    <div class="error"><?= htmlspecialchars($apiError) ?></div>
<?php endif ?>

<?php if ($weather): ?>

<!-- ── TIEMPO ACTUAL ─────────────────────────────────────────────── -->
<section>
    <h2>Tiempo actual</h2>

    <div class="current-main">
        <img src="https://openweathermap.org/img/wn/<?= $weather['icon'] ?>@2x.png" alt="">
        <div>
            <div class="big-temp"><?= $weather['temperature'] ?><sup>°C</sup></div>
            <div class="desc"><?= htmlspecialchars($weather['description']) ?></div>
            <div class="feels">Sensación: <?= $weather['feels_like'] ?>°C &nbsp;·&nbsp; Actualizado: <?= $weather['dt'] ?></div>
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
</section>

<!-- ── PREVISIÓN POR HORAS ───────────────────────────────────────── -->
<section>
    <h2>Previsión por horas — hoy <?= date('d/m/Y') ?></h2>

    <?php if (empty($hours)): ?>
        <p style="color:#888;font-size:13px">No quedan franjas horarias para hoy.</p>
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
            <tr><th>Hora</th><th>Temp.</th><th>Sensación</th><th>Humedad</th><th>Viento</th><th>Lluvia</th></tr>
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
</section>

<!-- ── PREVISIÓN SEMANAL ─────────────────────────────────────────── -->
<section>
    <h2>Previsión semanal</h2>

    <?php if (empty($days)): ?>
        <p style="color:#888;font-size:13px">No hay datos disponibles.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>Día</th><th></th><th>Descripción</th><th>Máx</th><th>Mín</th><th>Humedad</th><th>Viento</th><th>Lluvia</th></tr>
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
</section>

<?php endif ?>

<div class="nav-row">
    <a class="btn btn-plain" href="<?= APP_BASE ?>/">← Nueva búsqueda</a>
</div>
