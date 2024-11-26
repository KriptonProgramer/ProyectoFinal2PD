<?php
// Configurar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir tu clase CLI para la conexión y operaciones
include '../../classes/producto.class.php'; // Asegúrate de que esta clase tenga las funciones necesarias para el CRUD

// Inicializa la conexión
$prod = new PROD($conexion);

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
            $idProd = $_GET['id'];
            $productoData = $prod->getProducto($idProd); // Llama al método para obtener el producto
            echo json_encode($productoData); // Devuelve los datos del producto como JSON
        } else {
            // Si no se proporciona ID, podrías devolver un mensaje de error o todos los productos
            $productos = $prod->productos_gets_all(); // Método para obtener todos los productos
            echo json_encode($productos);
        }
        break;

        case 'POST':
            if (isset($_GET['id'])) {
                // Actualización
                $id = $_GET['id'];
                // Aquí puedes acceder a los datos con $_POST
                $data = $_POST; // Obtener todos los datos del formulario
                // También verifica los archivos subidos
                $fileData = $_FILES;
                // Ahora usa $data y $fileData para procesar la actualización
                $result = $prod->updateProducto($id, $data, $fileData); // Asegúrate de tener este método modificado
                echo json_encode($result);
            } else {
                // Creación
                $data = $_POST; // Obtener todos los datos del formulario
                $fileData = $_FILES;
                // Verifica si los datos requeridos están presentes
                if (empty($data['nombre']) || empty($data['precio']) || empty($data['categoria_id']) || empty($data['stock'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Faltan algunos datos necesarios para crear el producto']);
                    exit;
                }
                $result = $prod->createProducto($data, $fileData); // Asegúrate de tener este método modificado
                echo json_encode($result);
            }
            break;
        
    case 'DELETE':
        // Verifica que se haya pasado un ID
        if (isset($_GET['id'])) {
            $idProd = $_GET['id'];
            $result = $prod->deleteProducto($idProd); // Asegúrate de tener este método
            echo json_encode($result);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'ID de producto no proporcionado']);
        }
        break;

    default:
        http_response_code(405); // Método no permitido
        echo json_encode(['message' => 'Método no permitido']);
        break;
}
?>
