<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../classes/grl.class.php';
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear una instancia de la clase de conexión
$db = new GRL($conexion); // Asegúrate de que la conexión se maneje correctamente
$conexion = $db->getConnection(); // Obtener la conexión

// Verificar la conexión
if (!$conexion) {
    die("Conexión fallida: " . pg_last_error());
}

// Consulta para obtener todos los productos
$sql = "SELECT * FROM productos"; // Obtener todos los campos
$result = pg_query($conexion, $sql);
if (!$result) {
    die("Error en la consulta: " . pg_last_error());
}

// Crear un nuevo archivo de Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Establecer encabezados
$fields = [
    'ID', 
    'Nombre', 
    'Categoría ID', 
    'Subcategoría ID', 
    'Marca', 
    'Precio', 
    'Stock', 
    'Imagen 1', 
    'Imagen 2', 
    'Imagen 3', 
    'Condición', 
    'Carpeta ID', 
    'Descripción'
];

$column = 'A';
foreach ($fields as $field) {
    $sheet->setCellValue($column . '1', $field);
    $column++;
}

// Llenar los datos de los productos
$row = 2; // Comenzar en la segunda fila
while ($producto = pg_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $row, $producto['id']);
    $sheet->setCellValue('B' . $row, $producto['nombre']);
    $sheet->setCellValue('C' . $row, $producto['categoria_id']);
    $sheet->setCellValue('D' . $row, $producto['subcategoria_id']);
    $sheet->setCellValue('E' . $row, $producto['marca']);
    $sheet->setCellValue('F' . $row, $producto['precio']);
    $sheet->setCellValue('G' . $row, $producto['stock']);
    $sheet->setCellValue('H' . $row, $producto['producto_imagen1']);
    $sheet->setCellValue('I' . $row, $producto['producto_imagen2']);
    $sheet->setCellValue('J' . $row, $producto['producto_imagen3']);
    $sheet->setCellValue('K' . $row, $producto['condicion']);
    $sheet->setCellValue('L' . $row, $producto['carpeta_id']);
    $sheet->setCellValue('M' . $row, $producto['descripcion']);
    $row++;
}

// Cerrar la conexión
pg_close($conexion);

// Crear el archivo Excel
$writer = new Xlsx($spreadsheet);
$fecha = new DateTime();
$filename = "productos_" . $fecha->format('Y-m') . ".xlsx";


// Establecer cabeceras para descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Expires: 0');

// Limpiar la salida anterior y guardar el archivo
ob_end_clean();
$writer->save('php://output');
exit;
?>
