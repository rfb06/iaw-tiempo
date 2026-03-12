<?php

/**
 * ControladorTiempo — gestiona las peticiones HTTP y coordina modelo/vista.
 */
class ControladorTiempo {

    private ServicioTiempo    $servicio;
    private AccesoDatosTiempo $dao;

    public function __construct() {
        $this->servicio = new ServicioTiempo();
        $this->dao      = new AccesoDatosTiempo();
    }

    // GET / — Página de inicio con buscador
    public function inicio(): void {
        $busquedasRecientes  = $this->dao->obtenerBusquedasRecientes(8);
        $ciudadesMasVisitadas = $this->dao->obtenerCiudadesMasConsultadas();
        $this->renderizar('tiempo/inicio', compact('busquedasRecientes', 'ciudadesMasVisitadas'));
    }

    // POST /buscar — geocodifica la ciudad y redirige al menú
    public function buscar(): void {
        $ciudad = trim($_POST['ciudad'] ?? '');
        if ($ciudad === '') {
            $this->redirigir('/?error=vacio');
            return;
        }

        $ubicacion = $this->servicio->geocodificar($ciudad);
        if (!$ubicacion) {
            $error = urlencode("Ciudad '{$ciudad}' no encontrada.");
            $this->redirigir("/?error={$error}");
            return;
        }

        $idBusqueda = $this->dao->guardarBusqueda(
            $ubicacion['nombre'], $ubicacion['lat'], $ubicacion['lon'], $ubicacion['pais']
        );

        $parametros = http_build_query([
            'ciudad'    => $ubicacion['nombre'],
            'pais'      => $ubicacion['pais'],
            'lat'       => $ubicacion['lat'],
            'lon'       => $ubicacion['lon'],
            'id'        => $idBusqueda,
        ]);
        $this->redirigir("/tiempo/menu?{$parametros}");
    }

    // GET /tiempo/menu — elegir tipo de consulta
    public function menu(): void {
        $ubicacion = $this->ubicacionDesdeGet();
        if (!$ubicacion) { $this->redirigir('/'); return; }
        $this->renderizar('tiempo/menu', compact('ubicacion'));
    }

    // GET /tiempo/actual
    public function actual(): void {
        $ubicacion = $this->ubicacionDesdeGet();
        if (!$ubicacion) { $this->redirigir('/'); return; }

        $datos = $this->servicio->obtenerTiempoActual($ubicacion['lat'], $ubicacion['lon']);
        if (!$datos) { $this->renderizarError('No se pudo obtener el tiempo actual.'); return; }

        if ($ubicacion['id']) {
            try { $this->dao->guardarTiempoActual((int)$ubicacion['id'], $datos); } catch (\Throwable) {}
        }

        $this->renderizar('tiempo/actual', compact('ubicacion', 'datos'));
    }

    // GET /tiempo/horas
    public function horas(): void {
        $ubicacion = $this->ubicacionDesdeGet();
        if (!$ubicacion) { $this->redirigir('/'); return; }

        $prevision = $this->servicio->obtenerPrevision($ubicacion['lat'], $ubicacion['lon']);
        if (!$prevision) { $this->renderizarError('No se pudo obtener la previsión por horas.'); return; }

        $elementos = $this->servicio->filtrarHoy($prevision['list']);
        if (empty($elementos)) {
            $elementos = array_slice($prevision['list'], 0, 8);
        }

        if ($ubicacion['id']) {
            try { $this->dao->guardarPrevisionElementos((int)$ubicacion['id'], 'horas', $elementos); } catch (\Throwable) {}
        }

        $this->renderizar('tiempo/horas', compact('ubicacion', 'elementos'));
    }

    // GET /tiempo/semana
    public function semana(): void {
        $ubicacion = $this->ubicacionDesdeGet();
        if (!$ubicacion) { $this->redirigir('/'); return; }

        $prevision = $this->servicio->obtenerPrevision($ubicacion['lat'], $ubicacion['lon']);
        if (!$prevision) { $this->renderizarError('No se pudo obtener la previsión semanal.'); return; }

        $dias = $this->servicio->filtrarSemanal($prevision['list']);

        if ($ubicacion['id']) {
            try { $this->dao->guardarPrevisionElementos((int)$ubicacion['id'], 'semana', $dias); } catch (\Throwable) {}
        }

        $this->renderizar('tiempo/semana', compact('ubicacion', 'dias'));
    }

    // ── Métodos privados ─────────────────────────────────────────

    private function ubicacionDesdeGet(): ?array {
        $lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
        $lon = filter_input(INPUT_GET, 'lon', FILTER_VALIDATE_FLOAT);
        if ($lat === false || $lon === false || $lat === null || $lon === null) return null;
        return [
            'ciudad' => htmlspecialchars($_GET['ciudad'] ?? ''),
            'pais'   => htmlspecialchars($_GET['pais']   ?? ''),
            'lat'    => $lat,
            'lon'    => $lon,
            'id'     => $_GET['id'] ?? null,
        ];
    }

    private function renderizar(string $vista, array $variables = []): void {
        extract($variables);
        $servicio  = $this->servicio;
        $archivoVista   = __DIR__ . "/../vistas/{$vista}.php";
        $archivoPlantilla = __DIR__ . '/../vistas/parciales/plantilla.php';
        ob_start();
        require $archivoVista;
        $contenido = ob_get_clean();
        require $archivoPlantilla;
    }

    private function renderizarError(string $mensaje): void {
        $this->renderizar('tiempo/error', compact('mensaje'));
    }

    private function redirigir(string $url): void {
        header("Location: {$url}");
        exit;
    }
}
