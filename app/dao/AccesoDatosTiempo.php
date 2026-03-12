<?php

/**
 * AccesoDatosTiempo — DAO para operaciones con la base de datos.
 */
class AccesoDatosTiempo {
    private PDO $bd;

    public function __construct() {
        $this->bd = BaseDatos::obtenerInstancia();
    }

    // ── Búsquedas de ciudades ──────────────────────────────────────

    public function guardarBusqueda(string $ciudad, float $lat, float $lon, string $pais): int {
        $consulta = $this->bd->prepare(
            'INSERT INTO busquedas_ciudad (nombre_ciudad, lat, lon, pais, fecha_busqueda)
             VALUES (:ciudad, :lat, :lon, :pais, NOW())'
        );
        $consulta->execute([
            ':ciudad' => $ciudad,
            ':lat'    => $lat,
            ':lon'    => $lon,
            ':pais'   => $pais,
        ]);
        return (int) $this->bd->lastInsertId();
    }

    public function obtenerBusquedasRecientes(int $limite = 10): array {
        $consulta = $this->bd->prepare(
            'SELECT DISTINCT nombre_ciudad, pais, lat, lon, MAX(fecha_busqueda) AS ultima_busqueda
             FROM busquedas_ciudad
             GROUP BY nombre_ciudad, pais, lat, lon
             ORDER BY ultima_busqueda DESC
             LIMIT :lim'
        );
        $consulta->bindValue(':lim', $limite, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll();
    }

    // ── Registros meteorológicos ───────────────────────────────────

    public function guardarTiempoActual(int $idBusqueda, array $datos): void {
        $consulta = $this->bd->prepare(
            'INSERT INTO registros_meteorologicos
             (id_busqueda, tipo, temperatura, sensacion_termica, humedad, presion,
              velocidad_viento, direccion_viento, descripcion, icono, fecha_prevision, fecha_registro)
             VALUES
             (:id, :tipo, :temp, :sensacion, :humedad, :presion,
              :vel_viento, :dir_viento, :descripcion, :icono, :fecha_prev, NOW())'
        );
        $consulta->execute([
            ':id'          => $idBusqueda,
            ':tipo'        => 'actual',
            ':temp'        => $datos['main']['temp'],
            ':sensacion'   => $datos['main']['feels_like'],
            ':humedad'     => $datos['main']['humidity'],
            ':presion'     => $datos['main']['pressure'],
            ':vel_viento'  => $datos['wind']['speed'],
            ':dir_viento'  => $datos['wind']['deg'] ?? 0,
            ':descripcion' => $datos['weather'][0]['description'],
            ':icono'       => $datos['weather'][0]['icon'],
            ':fecha_prev'  => date('Y-m-d H:i:s', $datos['dt']),
        ]);
    }

    public function guardarPrevisionElementos(int $idBusqueda, string $tipo, array $elementos): void {
        $consulta = $this->bd->prepare(
            'INSERT INTO registros_meteorologicos
             (id_busqueda, tipo, temperatura, sensacion_termica, humedad, presion,
              velocidad_viento, direccion_viento, descripcion, icono, fecha_prevision, fecha_registro)
             VALUES
             (:id, :tipo, :temp, :sensacion, :humedad, :presion,
              :vel_viento, :dir_viento, :descripcion, :icono, :fecha_prev, NOW())'
        );
        foreach ($elementos as $elemento) {
            $consulta->execute([
                ':id'          => $idBusqueda,
                ':tipo'        => $tipo,
                ':temp'        => $elemento['main']['temp'],
                ':sensacion'   => $elemento['main']['feels_like'],
                ':humedad'     => $elemento['main']['humidity'],
                ':presion'     => $elemento['main']['pressure'],
                ':vel_viento'  => $elemento['wind']['speed'],
                ':dir_viento'  => $elemento['wind']['deg'] ?? 0,
                ':descripcion' => $elemento['weather'][0]['description'],
                ':icono'       => $elemento['weather'][0]['icon'],
                ':fecha_prev'  => date('Y-m-d H:i:s', $elemento['dt']),
            ]);
        }
    }

    public function obtenerCiudadesMasConsultadas(): array {
        $consulta = $this->bd->query(
            'SELECT nombre_ciudad, pais, COUNT(*) AS total
             FROM busquedas_ciudad
             GROUP BY nombre_ciudad, pais
             ORDER BY total DESC
             LIMIT 5'
        );
        return $consulta->fetchAll();
    }
}
