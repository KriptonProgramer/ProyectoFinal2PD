<?php
require_once "global.php";
global $servername, $username, $password, $dbname;
// var_dump($servername, $username, $password, $dbname); // Para depurar

$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar si hubo un error en la conexión
if ($conexion->connect_error) {
    die("Falló en la conexión con la base de datos: " . $conexion->connect_error);
}


// Configurar la codificación de caracteres para MySQL (si es necesario)
$conexion->set_charset("utf8mb4");










// function cambiarColacion($conexion, $tabla, $columna, $nuevoCharset = 'utf8mb4', $nuevaColacion = 'utf8mb4_general_ci')
// {
//     // Generar la consulta SQL para modificar la colación de una columna
//     $query = "ALTER TABLE $tabla MODIFY COLUMN $columna VARCHAR(255) CHARACTER SET $nuevoCharset COLLATE $nuevaColacion";

//     // Ejecutar la consulta
//     if (mysqli_query($conexion, $query)) {
//         echo "La colación de la columna $columna en la tabla $tabla ha sido cambiada exitosamente a $nuevoCharset/$nuevaColacion.";
//     } else {
//         echo "Error al cambiar la colación: " . mysqli_error($conexion);
//     }
// }
// // Llamar a la función para cambiar la colación de una columna específica
// cambiarColacion($conexion, 'products', 'productName');
// cambiarColacion($conexion, 'products', 'productDescription');
// cambiarColacion($conexion, 'products', 'productCompany');

// // Cerrar la conexión
// mysqli_close($conexion);
?>