<?php
class WeatherModel
{
    // ── Geocoding ────────────────────────────────────────────────────────────

    /**
     * Obtiene lat/lon de una ciudad vía Geocoding API.
     * Lanza RuntimeException si no existe o hay error de red.
     *
     * @return array  ['name', 'country', 'state', 'lat', 'lon']
     */
    public function geocode(string $city): array
    {
        $data = $this->cachedRequest('geo_' . $city, OW_GEO_URL, [
            'q'     => $city,
            'limit' => 1,
            'appid' => OW_API_KEY,
        ]);

        if (empty($data)) {
            throw new RuntimeException("Ciudad «{$city}» no encontrada. Verifica el nombre e inténtalo de nuevo.");
        }

        return [
            'name'    => $data[0]['name'],
            'country' => $data[0]['country'],
            'state'   => $data[0]['state']   ?? '',
            'lat'     => $data[0]['lat'],
            'lon'     => $data[0]['lon'],
        ];
    }

    // ── Current weather ──────────────────────────────────────────────────────

    public function getCurrent(float $lat, float $lon): array
    {
        $raw = $this->cachedRequest("current_{$lat}_{$lon}", OW_CURRENT_URL, [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => OW_API_KEY,
            'units' => 'metric',
            'lang'  => 'es',
        ]);

        return [
            'temperature'  => round($raw['main']['temp']),
            'feels_like'   => round($raw['main']['feels_like']),
            'temp_min'     => round($raw['main']['temp_min']),
            'temp_max'     => round($raw['main']['temp_max']),
            'humidity'     => $raw['main']['humidity'],
            'pressure'     => $raw['main']['pressure'],
            'description'  => ucfirst($raw['weather'][0]['description']),
            'icon'         => $raw['weather'][0]['icon'],
            'wind_speed'   => $raw['wind']['speed'],
            'wind_deg'     => $raw['wind']['deg'] ?? 0,
            'visibility'   => isset($raw['visibility']) ? round($raw['visibility'] / 1000, 1) : null,
            'clouds'       => $raw['clouds']['all'] ?? 0,
            'sunrise'      => date('H:i', $raw['sys']['sunrise']),
            'sunset'       => date('H:i', $raw['sys']['sunset']),
            'dt'           => date('d/m/Y H:i', $raw['dt']),
        ];
    }

    // ── Hourly forecast (today only, from /forecast 3h) ──────────────────────

    public function getHourly(float $lat, float $lon): array
    {
        $raw = $this->cachedRequest("forecast_{$lat}_{$lon}", OW_FORECAST_URL, [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => OW_API_KEY,
            'units' => 'metric',
            'lang'  => 'es',
            'cnt'   => 40,  // 5 días × 8 franjas de 3h
        ]);

        $today  = date('Y-m-d');
        $result = [];

        foreach ($raw['list'] as $item) {
            if (date('Y-m-d', $item['dt']) !== $today) {
                continue;
            }
            $result[] = [
                'time'        => date('H:i', $item['dt']),
                'temperature' => round($item['main']['temp']),
                'feels_like'  => round($item['main']['feels_like']),
                'humidity'    => $item['main']['humidity'],
                'description' => ucfirst($item['weather'][0]['description']),
                'icon'        => $item['weather'][0]['icon'],
                'wind_speed'  => $item['wind']['speed'],
                'rain'        => $item['rain']['3h'] ?? 0,
                'pop'         => round(($item['pop'] ?? 0) * 100),  // % precipitación
            ];
        }

        return $result;
    }

    // ── Weekly forecast (daily aggregated from /forecast 3h) ─────────────────

    public function getWeekly(float $lat, float $lon): array
    {
        $raw = $this->cachedRequest("forecast_{$lat}_{$lon}", OW_FORECAST_URL, [
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => OW_API_KEY,
            'units' => 'metric',
            'lang'  => 'es',
            'cnt'   => 40,
        ]);

        // Agrupar por día
        $days = [];
        foreach ($raw['list'] as $item) {
            $day = date('Y-m-d', $item['dt']);
            if (!isset($days[$day])) {
                $days[$day] = [
                    'date'        => $day,
                    'label'       => $this->dayLabel($item['dt']),
                    'temps'       => [],
                    'humidity'    => [],
                    'icons'       => [],
                    'descriptions'=> [],
                    'wind'        => [],
                    'pop'         => [],
                ];
            }
            $days[$day]['temps'][]        = $item['main']['temp'];
            $days[$day]['humidity'][]     = $item['main']['humidity'];
            $days[$day]['icons'][]        = $item['weather'][0]['icon'];
            $days[$day]['descriptions'][] = $item['weather'][0]['description'];
            $days[$day]['wind'][]         = $item['wind']['speed'];
            $days[$day]['pop'][]          = $item['pop'] ?? 0;
        }

        // Resumir cada día
        $result = [];
        foreach ($days as $day) {
            $result[] = [
                'date'        => $day['date'],
                'label'       => $day['label'],
                'temp_max'    => round(max($day['temps'])),
                'temp_min'    => round(min($day['temps'])),
                'humidity'    => round(array_sum($day['humidity']) / count($day['humidity'])),
                'icon'        => $this->dominantIcon($day['icons']),
                'description' => ucfirst($this->dominant($day['descriptions'])),
                'wind_speed'  => round(array_sum($day['wind']) / count($day['wind']), 1),
                'pop'         => round(max($day['pop']) * 100),
            ];
        }

        return array_values($result);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function dayLabel(int $ts): string
    {
        $days = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        $dow  = (int) date('w', $ts);
        return $days[$dow] . ' ' . date('d/m', $ts);
    }

    private function dominant(array $arr): string
    {
        $counts = array_count_values($arr);
        arsort($counts);
        return array_key_first($counts);
    }

    private function dominantIcon(array $icons): string
    {
        // Preferir iconos de día
        $day = array_filter($icons, fn($i) => str_ends_with($i, 'd'));
        return $this->dominant($day ?: $icons);
    }

    // ── HTTP + cache ─────────────────────────────────────────────────────────

    private function cachedRequest(string $key, string $url, array $params): array
    {
        if (!is_dir(CACHE_DIR)) {
            mkdir(CACHE_DIR, 0755, true);
        }

        $cacheFile = CACHE_DIR . md5($key) . '.json';

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL) {
            return json_decode(file_get_contents($cacheFile), true);
        }

        $fullUrl  = $url . '?' . http_build_query($params);
        $context  = stream_context_create(['http' => ['timeout' => 6]]);
        $response = @file_get_contents($fullUrl, false, $context);

        if ($response === false) {
            throw new RuntimeException("Error de conexión con OpenWeather. Intenta más tarde.");
        }

        $data = json_decode($response, true);

        // Verificar códigos de error de la API
        if (isset($data['cod']) && !in_array((string)$data['cod'], ['200', '0'])) {
            throw new RuntimeException($data['message'] ?? "Error en la API de OpenWeather.");
        }

        file_put_contents($cacheFile, json_encode($data));
        return $data;
    }
}
