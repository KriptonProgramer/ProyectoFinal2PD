<?php
session_start(); // Inicia la sesión
// Destruir todas las variables de sesión
$_SESSION = array();
// Destruir la sesión
session_destroy();
// Redirigir al usuario a la página de inicio de sesión
header('Location: https://panel.casasantoni.com.ar/login.php');
exit();
?>