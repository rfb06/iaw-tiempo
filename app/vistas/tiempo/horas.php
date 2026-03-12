<?php
$params = http_build_query([
    'ciudad' => $ubicacion['ciudad'],
    'pais'   => $ubicacion['pais'],
    'lat'    => $ubicacion['lat'],
    'lon'    => $ubicacion['lon'],
    'id'     => $ubicacion['id'],
]);
$etiquetas   = array_map(fn($e) => date('H:i', $e['dt']), $elementos);
$temperaturas = array_map(fn($e) => round($e['main']['temp'], 1), $elementos);
$sensaciones  = array_map(fn($e) => round($e['main']['feels_like'], 1), $elementos);
$humedades    = array_map(fn($e) => $e['main']['humidity'], $elementos);
$lluvias      = array_map(fn($e) => round($e['rain']['3h'] ?? 0, 2), $elementos);
?>

<div class="cabecera-pagina">
    <a href="/tiempo/menu?<?= $params ?>" class="btn-volver">&#8592; Volver</a>
    <h1>Previsión por horas &mdash; <?= $ubicacion['ciudad'] ?>, <?= $ubicacion['pais'] ?></h1>
    <p class="gris"><?= date('d/m/Y') ?></p>
</div>

<div class="tarjeta">
    <h3>Temperatura a lo largo del día (°C)</h3>
    <canvas id="graficaHoras" height="100"></canvas>
</div>

<div class="tarjeta" style="margin-top:1rem">
    <h3>Humedad y precipitación</h3>
    <canvas id="graficaLluvia" height="100"></canvas>
</div>

<div class="tarjeta" style="margin-top:1rem">
    <h3>Tabla de previsión por horas</h3>
    <div class="tabla-scroll">
    <table>
        <thead>
            <tr>
                <th>Hora</th><th>Estado</th><th>Temp.</th><th>Sensación</th>
                <th>Humedad</th><th>Viento</th><th>Lluvia (3h)</th><th>Descripción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($elementos as $elemento): ?>
        <tr>
            <td><?= date('H:i', $elemento['dt']) ?></td>
            <td><img src="https://openweathermap.org/img/wn/<?= $elemento['weather'][0]['icon'] ?>.png" width="36" alt=""></td>
            <td><?= round($elemento['main']['temp']) ?>°C</td>
            <td><?= round($elemento['main']['feels_like']) ?>°C</td>
            <td><?= $elemento['main']['humidity'] ?>%</td>
            <td><?= round($elemento['wind']['speed'] * 3.6, 1) ?> km/h <?= $servicio->direccionViento($elemento['wind']['deg'] ?? 0) ?></td>
            <td><?= round($elemento['rain']['3h'] ?? 0, 2) ?> mm</td>
            <td><?= ucfirst($elemento['weather'][0]['description']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<script>
const etiquetas = <?= json_encode($etiquetas) ?>;

new Chart(document.getElementById('graficaHoras'), {
    type: 'line',
    data: {
        labels: etiquetas,
        datasets: [
            { label: 'Temperatura (°C)', data: <?= json_encode($temperaturas) ?>, borderColor: '#d63031', backgroundColor: 'rgba(214,48,49,.1)', tension: 0.4, fill: true },
            { label: 'Sensación (°C)',   data: <?= json_encode($sensaciones) ?>,  borderColor: '#fdcb6e', borderDash: [5,5], tension: 0.4 },
        ]
    },
    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { title: { display: true, text: '°C' } } } }
});

new Chart(document.getElementById('graficaLluvia'), {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [
            { label: 'Humedad (%)', data: <?= json_encode($humedades) ?>, backgroundColor: 'rgba(9,132,227,.6)', yAxisID: 'y' },
            { label: 'Lluvia (mm)', data: <?= json_encode($lluvias) ?>,   type: 'line', backgroundColor: 'rgba(116,185,255,.9)', borderColor:'#74b9ff', yAxisID: 'y2', tension: 0.4 },
        ]
    },
    options: {
        plugins: { legend: { position: 'bottom' } },
        scales: {
            y:  { title: { display: true, text: '%' },  position: 'left' },
            y2: { title: { display: true, text: 'mm' }, position: 'right', grid: { drawOnChartArea: false } }
        }
    }
});
</script>
