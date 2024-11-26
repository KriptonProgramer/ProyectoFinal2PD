<?php
// Datos de conexión a la base de datos
if (!defined('DB_HOST')) {
}
if (!defined('DB_NAME')) {
}
if (!defined('DB_USER')) {
}
if (!defined('DB_PASSWORD')) {
}
// Ruta base del sitio

// Otras configuraciones
define('APP_ENV', 'development');
// Cambia a 'production' en un entorno de producción

// Configuración de errores
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
