<?php
// Datos de conexión a la base de datos
if (!defined('DB_HOST')) {
    define('DB_HOST', 'empresas.dnatech.com.ar');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', '{$empresa->nombre}');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'postgresql');
}
if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', 'lancelot');
}
// Ruta base del sitio
define('BASE_URL', 'http://panel.casasantoni.com.ar');

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

// Configuraciones de la empresa
// Configuraciones de la empresa
define('EMPRESA_NOMBRE', "TIENDA");
define('EMPRESA_DOMINIO', "casasantoni.com.ar");
define('EMPRESA_LOGO', "logo1.png");
define('IMP_URL', "https://panel.casasantoni.com.ar");
