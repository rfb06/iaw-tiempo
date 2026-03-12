<?php

/**
 * BaseDatos — Singleton de conexión PDO.
 */
class BaseDatos {
    private static ?PDO $instancia = null;

    public static function obtenerInstancia(): PDO {
        if (self::$instancia === null) {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                DB_HOST, DB_PORT, DB_NAME);
            self::$instancia = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instancia;
    }

    private function __construct() {}
}
