<?php
// ─── OpenWeather ────────────────────────────────────────────────────────────
define('OW_API_KEY',      getenv('OPENWEATHER_API_KEY') ?: 'TU_API_KEY_AQUI');
define('OW_GEO_URL',      'https://api.openweathermap.org/geo/1.0/direct');
define('OW_CURRENT_URL',  'https://api.openweathermap.org/data/2.5/weather');
define('OW_FORECAST_URL', 'https://api.openweathermap.org/data/2.5/forecast');
define('OW_ONECALL_URL',  'https://api.openweathermap.org/data/3.0/onecall');

// ─── App ─────────────────────────────────────────────────────────────────────
// En Docker el proyecto está en la raíz del VirtualHost → APP_BASE vacío
define('APP_BASE',   '');
define('CACHE_DIR',  __DIR__ . '/../cache/');
define('CACHE_TTL',  600);                     // 10 minutos
