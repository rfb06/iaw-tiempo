<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="/css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<header>
    <nav>
        <a href="/" class="marca">&#9925; <?= APP_NAME ?></a>
        <form action="/buscar" method="post" class="buscador-nav">
            <input type="text" name="ciudad" placeholder="Buscar ciudad..." required>
            <button type="submit">Buscar</button>
        </form>
    </nav>
</header>

<main>
    <?= $contenido ?>
</main>

<footer>
    <p>Datos de <a href="https://openweathermap.org" target="_blank">OpenWeatherMap</a></p>
</footer>
</body>
</html>
