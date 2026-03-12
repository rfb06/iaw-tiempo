<?php

/**
 * ServicioTiempo — llamadas a la API de OpenWeatherMap.
 */
class ServicioTiempo {

    // ── Geocodificación ───────────────────────────────────────────

    public function geocodificar(string $ciudad): ?array {
        $url = OWM_GEO_URL . '?' . http_build_query([
            'q'     => $ciudad,
            'limit' => 1,
            'appid' => OWM_API_KEY,
        ]);
        $datos = $this->obtenerJson($url);
        if (empty($datos)) return null;
        $loc = $datos[0];
        return [
            'nombre'  => $loc['name'],
            'pais'    => $loc['country'],
            'estado'  => $loc['state'] ?? '',
            'lat'     => (float) $loc['lat'],
            'lon'     => (float) $loc['lon'],
        ];
    }

    // ── Tiempo actual ─────────────────────────────────────────────

    public function obtenerTiempoActual(float $lat, float $lon): ?array {
        $url = OWM_CURRENT_URL . '?' . http_build_query([
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => OWM_API_KEY,
            'units' => OWM_UNITS,
            'lang'  => OWM_LANG,
        ]);
        return $this->obtenerJson($url);
    }

    // ── Previsión 5 días / 3 horas ────────────────────────────────

    public function obtenerPrevision(float $lat, float $lon): ?array {
        $url = OWM_FORECAST_URL . '?' . http_build_query([
            'lat'   => $lat,
            'lon'   => $lon,
            'appid' => OWM_API_KEY,
            'units' => OWM_UNITS,
            'lang'  => OWM_LANG,
        ]);
        return $this->obtenerJson($url);
    }

    // ── Filtros ───────────────────────────────────────────────────

    public function filtrarHoy(array $listaPrevision): array {
        $hoy = date('Y-m-d');
        return array_values(array_filter($listaPrevision,
            fn($elemento) => str_starts_with($elemento['dt_txt'], $hoy)
        ));
    }

    public function filtrarSemanal(array $listaPrevision): array {
        // Un registro por día, el más cercano al mediodía
        $dias = [];
        foreach ($listaPrevision as $elemento) {
            $dia  = date('Y-m-d', $elemento['dt']);
            $hora = (int) date('H', $elemento['dt']);
            if (!isset($dias[$dia]) || abs($hora - 12) < abs((int) date('H', $dias[$dia]['dt']) - 12)) {
                $dias[$dia] = $elemento;
            }
        }
        return array_values($dias);
    }

    public function direccionViento(int $grados): string {
        $direcciones = ['N','NE','E','SE','S','SO','O','NO'];
        return $direcciones[round($grados / 45) % 8];
    }

    // ── HTTP ──────────────────────────────────────────────────────

    private function obtenerJson(string $url): mixed {
        $contexto = stream_context_create(['http' => [
            'timeout'       => 10,
            'ignore_errors' => true,
        ]]);
        $respuesta = @file_get_contents($url, false, $contexto);
        if ($respuesta === false) return null;
        $datos = json_decode($respuesta, true);
        if (isset($datos['cod']) && (int)$datos['cod'] !== 200) return null;
        return $datos;
    }
}
