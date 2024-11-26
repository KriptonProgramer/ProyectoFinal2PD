<?php

$debug = true;

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// session_start(); // Asegúrate de iniciar la sesión
// if (!isset($_SESSION['sesionIniciada']) || $_SESSION['sesionIniciada'] !== true) {
//     echo 'Sesión no iniciada. Variables de sesión: ';
//     print_r($_SESSION); // Para ver qué hay en $_SESSION
//     exit();
//     header('Location: https://panel.casasantoni.com.ar/login.php');
//     exit();
// }

include '../../classes/grl.class.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Conexión a PostgreSQL
require '../../header.panel.php';
include '../../classes/producto.class.php';

// Inicializa la conexión
$grl = new GRL($conexion);
$prod = new PROD($conexion);

if (!$conexion) {
    die('Error en la conexión a la base de datos.');
}

// Verificar si se ha subido el archivo
if (isset($_FILES['file']['tmp_name'])) {
    $spreadsheet = IOFactory::load($_FILES['file']['tmp_name']);
    $sheet = $spreadsheet->getActiveSheet();

    // Recorrer las filas del archivo Excel
    foreach ($sheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cellIterator as $cell) {
            $data[] = $cell->getValue();
        }

        // Suponiendo que la primera columna es el ID
        if (isset($data[0]) && is_numeric($data[0])) {
            $id = $data[0];
            $nombre = $data[1];
            $categoria_id = $data[2];
            $subcategoria_id = $data[3];
            $marca = $data[4];
            $precio = $data[5];
            $stock = $data[6];
            $producto_imagen1 = $data[7];
            $producto_imagen2 = $data[8];
            $producto_imagen3 = $data[9];
            $condicion = $data[10];
            $carpeta_id = $data[11];
            $descripcion = $data[12];

            // Actualizar la base de datos
            $query = "UPDATE productos SET nombre = $1, categoria_id = $2, subcategoria_id = $3, marca = $4, precio = $5, stock = $6, producto_imagen1 = $7, producto_imagen2 = $8, producto_imagen3 = $9, condicion = $10, carpeta_id = $11, descripcion = $12 WHERE id = $13";
            $result = pg_query_params($conexion, $query, [
                $nombre, $categoria_id, $subcategoria_id, $marca, $precio, $stock,
                $producto_imagen1, $producto_imagen2, $producto_imagen3, $condicion,
                $carpeta_id, $descripcion, $id
            ]);

            if (!$result) {
                echo "Error al actualizar el producto con ID $id: " . pg_last_error($conexion) . "<br>";
            }
        }
    }

    // Cerrar la conexión
    pg_close($conexion);
 header('Location: productos.php');
    exit(); // Asegúrate de salir después de redirigir
} else {
    echo "No se ha subido ningún archivo.";
}
?>