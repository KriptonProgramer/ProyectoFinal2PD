<?php
// Configurar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
$url = 'https://rutas.casasantoni.com.ar'; // Define la URL base
// Permitir CORS

// Si la solicitud es OPTIONS, no procesamos más
// if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//     http_response_code(200);
//     exit;
// }

// Incluir tu clase CLI para la conexión y operaciones
include '../../classes/web.class.php'; // Asegúrate de que esta clase tenga las funciones necesarias para el CRUD

// Inicializa la conexión
$web = new web($conexion);

if (!$conexion) {
    die(json_encode(['error' => 'Error en la conexión a la base de datos.']));
}
$data = json_decode(file_get_contents('php://input'), true);
// Obtener el modo del cuerpo de la solicitud
$modo = $data['modo'] ?? null;
;
if ($modo !== 'busqueda') {
    $modo = $_POST['modo'] ?? null; // Usa null como valor por defecto si 'modo' no está presente
}

switch ($modo) {
    case 'GET':
        // Manejo de paginación
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 15;

        // Calcular el offset
        $offset = ($page - 1) * $limit;

        // Llama a tu método para obtener productos con paginación
        $productos = $web->obtenerProductos($limit, $offset); // Asegúrate de tener este método implementado

        echo json_encode($productos ?: []); // Devuelve un array vacío si no hay productos
        break;

    case 'getProducto':
        // Verifica si se ha recibido un ID por POST
        if (isset($_POST['id'])) {
            $productoId = intval($_POST['id']);
            $producto = $web->obtenerProductoPorId($productoId); // Obtén los datos del producto

            echo $producto ? $web->cardProductoUnique($producto) : json_encode(['error' => 'Producto no encontrado.']);
        } else {
            echo json_encode(['error' => 'No se recibió el ID del producto.']);
        }
        break;

    case 'ProductosCategoria':
        // $idCategoria = $_POST['idcategoria'] ?? null; // Obtiene el ID de categoría

        // if ($idCategoria) {
        //     $productosCategoria = $web->obtenerProductosPorCategoria($idCategoria); // Método para obtener productos por categoría

        //     echo json_encode($productosCategoria ?: ['error' => 'No se encontraron productos en esta categoría.']);
        // } else {
        //     echo json_encode(['error' => 'No se recibió el ID de la categoría.']);
        // }
        break;
    case 'busqueda':
        //
        // Obtener los parámetros del cuerpo de la solicitud (JSON)
        $data = json_decode(file_get_contents('php://input'), true);

        // Verificamos si se recibieron los datos correctamente
        $categoriaId = $data['categoriaId'] ?? '';
        $subcategoryId = $data['subcategoriaId'] ?? '';

        // Verifica que el parámetro `subcategoriaId` no esté vacío
        $productos = [];
        if (!empty($subcategoryId)) {
            $productos = $web->getSubCategoria($subcategoryId); // Asegúrate de que este método esté preparado para devolver productos
        }

        // Comienza el contenedor de filas
        echo "<div class='row'>";

        if (!empty($productos)) {
            // Generamos las tarjetas de los productos
            foreach ($productos as $producto) {
                echo "<div class='col-md-3 mb-3'>"; // Cambiar a col-md-3 para 4 productos por fila
                echo $web->cardProducto22($producto);
                echo "</div>";
            }
        } else {
            // Si no se encontraron productos
            echo "<p>No se encontraron productos.</p>";
            echo "<div class='imgAvi'>";
            echo "<img src='public/assets/img/noencontrado.jpg' style='display: flex; justify-content: center; width:250px; height: 200px; margin-bottom: 150px> ;' alt='Logo de la empresa'";
            echo "</div>";
        }

        // Cerramos el contenedor de filas
        echo "</div>";
        break;


    case 'getDatosProducto':
        $productoId = $_POST['id'] ?? null;

        if ($productoId) {
            $productos = $web->cardProductoCategoria($productoId);
            echo is_array($productos) ? $web->crearCarousel($productos) : '<p>No hay productos para mostrar.</p>';
        } else {
            echo '<p>ID del producto no proporcionado.</p>';
        }
        break;

    case 'buscar':
        if (isset($_POST['query'])) {
            $buscar = $_POST['query'];
            $productos = $web->buscar($buscar); // Asumiendo que $tuObjeto es una instancia de tu clase

            echo "<div class='container mt-4'>";
            echo "<h2>Resultados de la búsqueda para: '$buscar'</h2>";
            echo "<div class='row'>"; // Comienza el contenedor de filas

            if (!empty($productos)) {
                foreach ($productos as $producto) {
                    echo $web->cardProducto22($producto, false); // Llama a la función para generar la tarjeta
                }
            } else {
                echo "<div class='imgAvi'>";
                echo "<p>No se encontraron productos.</p>";
                echo "<img src='public/assets/img/noencontrado.jpg' alt='Logo de la empresa'>";
                echo "</div>";
            }

            echo "</div>"; // Cierra el contenedor de filas
            echo "</div>"; // Cierra el contenedor principal
        } else {
            echo json_encode(['error' => 'No se recibió la consulta de búsqueda.']);
        }
        break;

    default:
        $web->parr($data);
        echo json_encode(['error' => "Metodo no permitido. $modo"]);
        break;
}
?>


<script>
    console.log(<?php echo $modo ?>)
</script>