<?php
$params = http_build_query([
    'ciudad' => $ubicacion['ciudad'],
    'pais'   => $ubicacion['pais'],
    'lat'    => $ubicacion['lat'],
    'lon'    => $ubicacion['lon'],
    'id'     => $ubicacion['id'],
]);
$nombresDias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
$etiquetas   = array_map(fn($d) => $nombresDias[date('w', $d['dt'])] . ' ' . date('d/m', $d['dt']), $dias);
$maximas     = array_map(fn($d) => round($d['main']['temp_max'], 1), $dias);
$minimas     = array_map(fn($d) => round($d['main']['temp_min'], 1), $dias);
$humedades   = array_map(fn($d) => $d['main']['humidity'], $dias);
?>

<div class="cabecera-pagina">
    <a href="/tiempo/menu?<?= $params ?>" class="btn-volver">&#8592; Volver</a>
    <h1>Previsión semanal &mdash; <?= $ubicacion['ciudad'] ?>, <?= $ubicacion['pais'] ?></h1>
</div>

<div class="tarjetas-semana">
<?php foreach ($dias as $i => $dia): ?>
<div class="tarjeta-dia tarjeta">
    <div class="nombre-dia"><?= $etiquetas[$i] ?></div>
    <img src="https://openweathermap.org/img/wn/<?= $dia['weather'][0]['icon'] ?>@2x.png" width="56" alt="">
    <div class="desc-dia"><?= ucfirst($dia['weather'][0]['description']) ?></div>
    <div class="temps-dia">
        <span class="temp-maxima"><?= round($dia['main']['temp_max']) ?>°</span>
        <span class="temp-minima"><?= round($dia['main']['temp_min']) ?>°</span>
    </div>
    <div class="detalles-dia">
        <span>Hum. <?= $dia['main']['humidity'] ?>%</span>
        <span>Viento <?= round($dia['wind']['speed'] * 3.6) ?> km/h</span>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="fila-graficas" style="margin-top:1.5rem">
    <div class="tarjeta tarjeta-grafica">
        <h3>Temperaturas por día (°C)</h3>
        <canvas id="graficaSemana" height="130"></canvas>
    </div>
    <div class="tarjeta tarjeta-grafica">
        <h3>Humedad por día (%)</h3>
        <canvas id="graficaHumSemana" height="130"></canvas>
    </div>
</div>

<script>
const etiquetas = <?= json_encode($etiquetas) ?>;

new Chart(document.getElementById('graficaSemana'), {
    type: 'line',
    data: {
        labels: etiquetas,
        datasets: [
            { label: 'Máxima (°C)', data: <?= json_encode($maximas) ?>, borderColor: '#d63031', backgroundColor: 'rgba(214,48,49,.1)', tension: 0.4, fill: '+1' },
            { label: 'Mínima (°C)', data: <?= json_encode($minimas) ?>, borderColor: '#74b9ff', backgroundColor: 'rgba(116,185,255,.1)', tension: 0.4, fill: false },
        ]
    },
    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { title: { display: true, text: '°C' } } } }
});

new Chart(document.getElementById('graficaHumSemana'), {
    type: 'bar',
    data: {
        labels: etiquetas,
        datasets: [{
            label: 'Humedad (%)',
            data: <?= json_encode($humedades) ?>,
            backgroundColor: <?= json_encode($humedades) ?>.map(v => `hsla(${220 - v},80%,60%,.7)`),
            borderRadius: 6,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { max: 100, title: { display: true, text: '%' } } } }
});
</script>
