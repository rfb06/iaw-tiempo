-- Esquema de base de datos para El Tiempo
CREATE DATABASE IF NOT EXISTS weather_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE weather_db;

-- Historial de búsquedas de ciudades
CREATE TABLE IF NOT EXISTS busquedas_ciudad (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_ciudad  VARCHAR(100) NOT NULL,
    pais           CHAR(2)      NOT NULL,
    lat            DECIMAL(9,6) NOT NULL,
    lon            DECIMAL(9,6) NOT NULL,
    fecha_busqueda DATETIME     NOT NULL,
    INDEX idx_ciudad       (nombre_ciudad),
    INDEX idx_fecha        (fecha_busqueda)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Registros meteorológicos guardados
CREATE TABLE IF NOT EXISTS registros_meteorologicos (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_busqueda       INT UNSIGNED NOT NULL,
    tipo              ENUM('actual','horas','semana') NOT NULL,
    temperatura       DECIMAL(5,2),
    sensacion_termica DECIMAL(5,2),
    humedad           TINYINT UNSIGNED,
    presion           SMALLINT UNSIGNED,
    velocidad_viento  DECIMAL(5,2),
    direccion_viento  SMALLINT UNSIGNED,
    descripcion       VARCHAR(100),
    icono             VARCHAR(10),
    fecha_prevision   DATETIME,
    fecha_registro    DATETIME NOT NULL,
    FOREIGN KEY (id_busqueda) REFERENCES busquedas_ciudad(id) ON DELETE CASCADE,
    INDEX idx_busqueda_tipo  (id_busqueda, tipo),
    INDEX idx_fecha_prevision (fecha_prevision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
