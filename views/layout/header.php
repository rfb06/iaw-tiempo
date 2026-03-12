<?php
session_status() === PHP_SESSION_NONE && session_start();
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Weather App') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; font-size: 15px; background: #fff; color: #111; }
        .wrap { max-width: 600px; margin: 0 auto; padding: 24px 16px 40px; }

        nav {
            border-bottom: 1px solid #ddd;
            padding: 10px 16px;
            font-size: 13px;
            color: #666;
            display: flex; gap: 6px; align-items: center;
        }
        nav a { color: #1a56a4; text-decoration: none; }
        nav a:hover { text-decoration: underline; }

        h1 { font-size: 18px; margin-bottom: 14px; }
        h2 { font-size: 15px; margin-bottom: 10px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 6px; }

        .error {
            border: 1px solid #e0a0a0;
            background: #fff5f5;
            padding: 8px 12px;
            font-size: 13px;
            color: #900;
            margin-bottom: 12px;
        }

        .search-row { display: flex; gap: 6px; margin-bottom: 20px; }
        .search-row input[type=text] {
            flex: 1; padding: 7px 10px;
            border: 1px solid #bbb; font-size: 14px;
        }
        .search-row input:focus { outline: none; border-color: #1a56a4; }

        .btn {
            padding: 7px 14px; background: #1a56a4; color: #fff;
            border: none; font-size: 13px; cursor: pointer;
            text-decoration: none; display: inline-block;
        }
        .btn:hover { background: #154488; }
        .btn-plain {
            background: #fff; color: #1a56a4;
            border: 1px solid #1a56a4;
        }
        .btn-plain:hover { background: #f0f5ff; }

        section { margin-bottom: 20px; }

        /* History */
        .history { list-style: none; font-size: 14px; }
        .history li { border-bottom: 1px solid #eee; }
        .history li:last-child { border-bottom: none; }
        .history a {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 4px; color: #1a56a4; text-decoration: none;
        }
        .history a:hover { background: #f5f8ff; }
        .history .cc { color: #999; font-size: 12px; }

        /* Option list */
        .options { list-style: none; font-size: 14px; }
        .options li { border-bottom: 1px solid #eee; }
        .options li:last-child { border-bottom: none; }
        .options a {
            display: flex; align-items: center; justify-content: space-between;
            padding: 11px 4px; color: #111; text-decoration: none;
        }
        .options a:hover { background: #f5f8ff; }
        .options small { display: block; color: #888; font-size: 12px; margin-top: 1px; }

        /* Stats */
        .stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px,1fr)); gap: 1px; background: #ddd; border: 1px solid #ddd; margin-bottom: 16px; }
        .stat { background: #fff; padding: 10px 12px; }
        .stat .lbl { font-size: 11px; color: #888; text-transform: uppercase; margin-bottom: 2px; }
        .stat .val { font-size: 18px; font-weight: bold; }
        .stat .unit { font-size: 11px; color: #888; }

        /* Big temp */
        .current-main { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .current-main img { width: 56px; height: 56px; }
        .big-temp { font-size: 48px; font-weight: bold; line-height: 1; }
        .big-temp sup { font-size: 20px; vertical-align: super; }
        .desc { font-size: 14px; color: #555; margin-top: 2px; }
        .feels { font-size: 12px; color: #888; }

        /* Hourly */
        .hourly { display: flex; gap: 6px; overflow-x: auto; padding-bottom: 4px; margin-bottom: 14px; }
        .hourly::-webkit-scrollbar { height: 3px; }
        .hourly::-webkit-scrollbar-thumb { background: #ccc; }
        .hcard { flex: 0 0 80px; border: 1px solid #ddd; padding: 8px 4px; text-align: center; font-size: 12px; }
        .hcard .ht { font-weight: bold; color: #1a56a4; }
        .hcard img { width: 36px; height: 36px; }
        .hcard .htemp { font-size: 15px; font-weight: bold; }
        .hcard .hpop { color: #2563eb; font-size: 11px; margin-top: 2px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; padding: 6px 8px; background: #f5f5f5; border-bottom: 2px solid #ddd; font-size: 11px; text-transform: uppercase; color: #666; }
        td { padding: 7px 8px; border-bottom: 1px solid #eee; }
        tr:last-child td { border-bottom: none; }

        .nav-row { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 14px; }
        .coords { font-size: 12px; color: #888; margin-bottom: 14px; }

        section { margin-bottom: 28px; padding-bottom: 4px; border-top: 2px solid #111; padding-top: 16px; }
        section:first-of-type { border-top: none; padding-top: 0; }
        footer { text-align: center; font-size: 11px; color: #bbb; padding-top: 24px; }
    </style>
</head>
<body>

<nav>
    <a href="<?= APP_BASE ?>/">Inicio</a>
    <?php if (!empty($geo)): ?>
        <span>›</span>
        <a href="<?= APP_BASE ?>/city?<?= WeatherController::geoQs($geo) ?>">
            <?= htmlspecialchars($geo['name']) ?>, <?= htmlspecialchars($geo['country']) ?>
        </a>
    <?php endif ?>
    <?php if (!empty($pageLabel)): ?>
        <span>›</span>
        <span><?= htmlspecialchars($pageLabel) ?></span>
    <?php endif ?>
</nav>

<div class="wrap">
