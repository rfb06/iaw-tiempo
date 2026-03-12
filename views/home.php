<h1>Consulta del tiempo</h1>

<?php if (!empty($error)): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif ?>

<form class="search-row" action="<?= APP_BASE ?>/search" method="POST">
    <input type="text" name="city" placeholder="Ciudad" value="<?= htmlspecialchars($cityInput ?? '') ?>" required autofocus autocomplete="off">
    <button class="btn" type="submit">Buscar</button>
</form>

<?php if (!empty($history)): ?>
<section>
    <h2>Búsquedas recientes</h2>
    <ul class="history">
        <?php foreach ($history as $h): ?>
        <li>
            <a href="<?= APP_BASE ?>/city?<?= WeatherController::geoQs($h) ?>">
                <span><?= htmlspecialchars($h['name']) ?><?= $h['state'] ? ', '.htmlspecialchars($h['state']) : '' ?></span>
                <span class="cc"><?= htmlspecialchars($h['country']) ?></span>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</section>
<?php endif ?>
