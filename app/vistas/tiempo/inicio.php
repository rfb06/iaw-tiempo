<section class="portada">
    <h1>&#9925; El Tiempo</h1>
    <p>Consulta el tiempo actual, por horas y semanal de cualquier ciudad.</p>

    <?php if (!empty($_GET['error'])): ?>
        <div class="aviso aviso-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="/buscar" method="post" class="formulario-busqueda">
        <div class="caja-busqueda">
            <input type="text" name="ciudad" placeholder="Ej: Madrid, Tokio, Nueva York..." required autofocus>
            <button type="submit">Buscar</button>
        </div>
    </form>
</section>

<div class="rejilla-2">
    <?php if (!empty($busquedasRecientes)): ?>
    <section class="tarjeta">
        <h2>Búsquedas recientes</h2>
        <ul class="lista-ciudades">
            <?php foreach ($busquedasRecientes as $b): ?>
            <li>
                <a href="/tiempo/menu?<?= http_build_query([
                    'ciudad' => $b['nombre_ciudad'],
                    'pais'   => $b['pais'],
                    'lat'    => $b['lat'],
                    'lon'    => $b['lon'],
                ]) ?>">
                    <?= htmlspecialchars($b['nombre_ciudad']) ?>, <?= htmlspecialchars($b['pais']) ?>
                </a>
                <span class="gris"><?= date('d/m H:i', strtotime($b['ultima_busqueda'])) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($ciudadesMasVisitadas)): ?>
    <section class="tarjeta">
        <h2>Ciudades más consultadas</h2>
        <ul class="lista-ciudades">
            <?php foreach ($ciudadesMasVisitadas as $c): ?>
            <li>
                <span><?= htmlspecialchars($c['nombre_ciudad']) ?>, <?= htmlspecialchars($c['pais']) ?></span>
                <span class="etiqueta"><?= $c['total'] ?> consultas</span>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>
</div>
