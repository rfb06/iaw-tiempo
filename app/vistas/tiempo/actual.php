<?php
$params = http_build_query([
    'ciudad' => $ubicacion['ciudad'],
    'pais'   => $ubicacion['pais'],
    'lat'    => $ubicacion['lat'],
    'lon'    => $ubicacion['lon'],
    'id'     => $ubicacion['id'],
]);
$icono   = "https://openweathermap.org/img/wn/{$datos['weather'][0]['icon']}@2x.png";
$amanecer = date('H:i', $datos['sys']['sunrise']);
$ocaso    = date('H:i', $datos['sys']['sunset']);
?>

<div class="cabecera-pagina">
    <a href="/tiempo/menu?<?= $params ?>" class="btn-volver">&#8592; Volver</a>
    <h1>Tiempo actual &mdash; <?= $ubicacion['ciudad'] ?>, <?= $ubicacion['pais'] ?></h1>
    <p class="gris"><?= date('d/m/Y H:i') ?></p>
</div>

<div class="tiempo-actual tarjeta">
    <div class="tiempo-izquierda">
        <img src="<?= $icono ?>" alt="<?= htmlspecialchars($datos['weather'][0]['description']) ?>">
        <div>
            <span class="temperatura-grande"><?= round($datos['main']['temp']) ?>°C</span>
            <span class="descripcion"><?= ucfirst($datos['weather'][0]['description']) ?></span>
        </div>
    </div>
    <div class="tiempo-derecha">
        <div class="rejilla-datos">
            <div class="dato"><span class="etiqueta-dato">Sensación térmica</span><span class="valor-dato"><?= round($datos['main']['feels_like']) ?>°C</span></div>
            <div class="dato"><span class="etiqueta-dato">Humedad</span><span class="valor-dato"><?= $datos['main']['humidity'] ?>%</span></div>
            <div class="dato"><span class="etiqueta-dato">Presión</span><span class="valor-dato"><?= $datos['main']['pressure'] ?> hPa</span></div>
            <div class="dato"><span class="etiqueta-dato">Viento</span><span class="valor-dato"><?= round($datos['wind']['speed'] * 3.6, 1) ?> km/h <?= $servicio->direccionViento($datos['wind']['deg'] ?? 0) ?></span></div>
            <div class="dato"><span class="etiqueta-dato">Nubosidad</span><span class="valor-dato"><?= $datos['clouds']['all'] ?>%</span></div>
            <div class="dato"><span class="etiqueta-dato">Visibilidad</span><span class="valor-dato"><?= number_format(($datos['visibility'] ?? 0) / 1000, 1) ?> km</span></div>
            <div class="dato"><span class="etiqueta-dato">Mín / Máx</span><span class="valor-dato"><?= round($datos['main']['temp_min']) ?>° / <?= round($datos['main']['temp_max']) ?>°</span></div>
            <div class="dato"><span class="etiqueta-dato">Amanecer / Ocaso</span><span class="valor-dato"><?= $amanecer ?> / <?= $ocaso ?></span></div>
        </div>
    </div>
</div>

<div class="fila-graficas">
    <div class="tarjeta tarjeta-grafica">
        <h3>Temperaturas (°C)</h3>
        <canvas id="graficaTemperatura" height="120"></canvas>
    </div>
    <div class="tarjeta tarjeta-grafica">
        <h3>Humedad y nubosidad (%)</h3>
        <canvas id="graficaHumedad" height="120"></canvas>
    </div>
</div>

<script>
new Chart(document.getElementById('graficaTemperatura'), {
    type: 'bar',
    data: {
        labels: ['Mínima', 'Actual', 'Máxima', 'Sensación'],
        datasets: [{
            label: '°C',
            data: [<?= round($datos['main']['temp_min']) ?>, <?= round($datos['main']['temp']) ?>, <?= round($datos['main']['temp_max']) ?>, <?= round($datos['main']['feels_like']) ?>],
            backgroundColor: ['#74b9ff','#0984e3','#d63031','#fdcb6e'],
            borderRadius: 6,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { title: { display: true, text: '°C' } } } }
});

new Chart(document.getElementById('graficaHumedad'), {
    type: 'doughnut',
    data: {
        labels: ['Humedad', 'Nubosidad', 'Despejado'],
        datasets: [{
            data: [<?= $datos['main']['humidity'] ?>, <?= $datos['clouds']['all'] ?>, <?= max(0, 100 - $datos['main']['humidity'] - $datos['clouds']['all']) ?>],
            backgroundColor: ['#0984e3','#b2bec3','#dfe6e9'],
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
