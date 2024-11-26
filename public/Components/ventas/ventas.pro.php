<?php
// Configurar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir tu clase CLI para la conexión y operaciones
// require 'app.top.php';
include '../../classes/grl.class.php'; // Asegúrate de que esta clase tenga las funciones necesarias para el CRUD

// Inicializa la conexión
$grl = new GRL($conexion);

if (!$conexion) {
    die('Error en la conexión a la base de datos.');
}

// Verifica el método de la solicitud
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Maneja las diferentes operaciones
switch ($requestMethod) {
    case 'GET':
        // Verifica si se ha pasado un ID en la solicitud
        if (isset($_GET['id'])) {
            $idCat = $_GET['id'];
            $categoriaData = $grl->getCategoria($idCat); // Llama al método para obtener el plan
            echo json_encode($categoriaData); // Devuelve los datos del plan como JSON
        } else {
            // Si no se proporciona ID, podrías devolver un mensaje de error o todos los planes
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'ID de categoria no proporcionado']);
        }
        break;
    case 'POST':
        // Si hay ID, es una actualización; de lo contrario, es una creación
        if (isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $idCat = $_GET['id'];
            $nombre = $data['nombre'];
            $descripcion = $data['descripcion'];
            $condicion = $data['condicion'] ? 1 : 0;

            $result = $grl->updateCategorias($idCat, $nombre, $descripcion, $condicion);
            echo json_encode($result);
        } else {
            $data = json_decode(file_get_contents('php://input'), true);

            // Asegúrate de que todos los valores necesarios existan en $data antes de pasarlos a createPlan
            if (isset($data['nombre'])) {
                $nombre = $data['nombre'];
                $descripcion = $data['descripcion'];
                $condicion = $data['condicion'] ? 1 : 0;
                // Llamar a la función con los seis parámetros
                $result = $grl->createCategoria($nombre, $descripcion, $condicion);

                echo json_encode(['status' => 'success', 'message' => 'Categoria creada exitosamente']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Faltan algunos datos necesarios para crear la categoria']);
            }
        }
        break;


    case 'DELETE':
        // Verifica que se haya pasado un ID
        if (isset($_GET['id'])) {
            $idCat = $_GET['id'];
            $result = $grl->deleteCategoria($idCat);
            echo json_encode($result);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'ID de categoria no proporcionado']);
        }
        break;

    default:
        http_response_code(405); // Método no permitido
        echo json_encode(['message' => 'Método no permitido']);
        break;
}
?>