<?php
require_once __DIR__ . '/../config/Conexion.php';  // Si el archivo está en public/config
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!class_exists('STC')) {
    require_once 'stcweb.class.php';
}

class WEB extends STC
{
    private $conexion;
    // public $urls = 'https://rutas.casasantoni.com.ar';
    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        parent::__construct($conexion); // Llamar al constructor de STC si es necesario
    }

    /**
     * Función para obtener todos los registros en Array
     * @return array|bool
     */
    public function datosOrdenados($tabla)
    {
        // Elimina la extensión .php del nombre del archivo, si está presente
        $tabla = pathinfo($tabla, PATHINFO_FILENAME);

        // Validar o sanitizar el nombre de la tabla (opcional, si hay preocupación por seguridad)
        // Por ejemplo, asegurarse de que solo contiene caracteres alfanuméricos y guiones bajos
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tabla)) {
            return false;  // Si el nombre de la tabla no es válido, evitar la consulta
        }

        global $debug;

        // Consulta SQL para obtener los registros de la tabla ordenados por 'id' ascendente
        $sql = "SELECT * FROM $tabla ORDER BY id ASC";

        // Obtener los datos utilizando la función get_query()
        $data = $this->get_query($sql);

        // Devolver los datos obtenidos
        return $data;
    }



    public function planes_gets_all()
    {
        $sql = 'SELECT * FROM planes ORDER BY id ASC'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos

        // Definir los encabezados de la tabla
        $header = [
            'id' => 'ID',
            'codigo' => 'Codigo Plan',
            'descripcion' => 'Descripcion',
            'mon_abono' => 'Cotizacion',
            'abono' => 'Precio'
        ];

        // Definir clases CSS para la tabla, encabezado, filas y celdas
        $table_class = 'table table-bordered'; // Clase de Bootstrap como ejemplo
        $header_class = 'table-dark';
        $row_class = 'table-row fadeIn';
        $cell_class = 'table-cell';
        $id_tabla = 'clientesAll';

        // Generar la tabla utilizando el método de STC
        return $this->genera_tabla($data, $header, $table_class, $header_class, $row_class, $cell_class, $id_tabla, true, false);
    }

    public function getPlan($planId)
    {
        // Consulta preparada para prevenir inyecciones SQL
        $sql = "SELECT * FROM planes WHERE id = $1 ";
        $result = pg_query_params($this->conexion, $sql, [$planId]);

        if ($result) {
            // Verifica si hay resultados
            if (pg_num_rows($result) > 0) {
                return pg_fetch_assoc($result);
            } else {
                return ['error' => 'No se encontró el plan con el ID proporcionado.'];
            }
        } else {
            return ['error' => 'Error al ejecutar la consulta'];
        }
    }
    public function updatePlan($planId, $data)
    {
        // Primero obtén los valores actuales del plan
        $sqlSelect = "SELECT abono, bajada, codigo, descripcion, instalacion, medio, mon_abono, mon_inst, parametros, subida FROM planes WHERE id = $1";
        $resultSelect = pg_query_params($this->conexion, $sqlSelect, [$planId]);

        if (!$resultSelect || pg_num_rows($resultSelect) == 0) {
            return ['error' => 'No se encontró el plan con el ID proporcionado'];
        }

        // Obtenemos el plan actual como arreglo asociativo
        $currentPlan = pg_fetch_assoc($resultSelect);

        // Si el dato ha sido editado en $data, úsalo. Si no, toma el valor actual.
        $abono = isset($data['abono']) ? $data['abono'] : $currentPlan['abono'];
        $bajada = isset($data['bajada']) ? $data['bajada'] : $currentPlan['bajada'];
        $codigo = isset($data['codigo']) ? $data['codigo'] : $currentPlan['codigo'];
        $descripcion = isset($data['descripcion']) ? $data['descripcion'] : $currentPlan['descripcion'];
        $instalacion = isset($data['instalacion']) ? $data['instalacion'] : $currentPlan['instalacion'];
        $medio = isset($data['medio']) ? $data['medio'] : $currentPlan['medio'];
        $mon_abono = isset($data['mon_abono']) ? $data['mon_abono'] : $currentPlan['mon_abono'];
        $mon_inst = isset($data['mon_inst']) ? $data['mon_inst'] : $currentPlan['mon_inst'];
        $parametros = isset($data['parametros']) ? $data['parametros'] : $currentPlan['parametros'];
        $subida = isset($data['subida']) ? $data['subida'] : $currentPlan['subida'];

        // Ahora actualiza solo los campos modificados o mantén los originales
        $sqlUpdate = "UPDATE planes SET 
                        abono = $1,
                        bajada = $2,
                        codigo = $3,
                        descripcion = $4,
                        instalacion = $5,
                        medio = $6,
                        mon_abono = $7,
                        mon_inst = $8,
                        parametros = $9,
                        subida = $10 
                    WHERE id = $11";

        $resultUpdate = pg_query_params($this->conexion, $sqlUpdate, [
            $abono,
            $bajada,
            $codigo,
            $descripcion,
            $instalacion,
            $medio,
            $mon_abono,
            $mon_inst,
            $parametros,
            $subida,
            $planId // Usamos el planId que recibimos en la función
        ]);

        if ($resultUpdate && pg_affected_rows($resultUpdate) > 0) {
            return ['success' => true]; // Devuelve éxito si se actualizó
        } else {
            return ['error' => 'Hubo un error en la actualización o no se modificaron los datos'];
        }
    }
    public function createPlan($codigo, $descripcion, $bajada, $subida, $monAbono, $abono)
    {
        // Escribir la lógica para insertar un nuevo plan en la base de datos
        $sql = "INSERT INTO planes (codigo, descripcion, bajada, subida, mon_abono, abono) 
                VALUES ($1, $2, $3, $4, $5, $6)";

        // Ejecutar la consulta
        $result = pg_query_params($this->conexion, $sql, [
            $codigo,
            $descripcion,
            $bajada,
            $subida,
            $monAbono,
            $abono
        ]);

        if ($result) {
            return ['status' => 'success', 'message' => 'Plan creado exitosamente'];
        } else {
            return ['status' => 'error', 'message' => 'Error al crear el plan: ' . pg_last_error($this->conexion)];
        }
    }
    public function deletePlan($planId)
    {
        // Escribir la lógica para eliminar el plan de la base de datos
        $sql = "DELETE FROM planes WHERE id = $1";

        // Ejecutar la consulta
        $result = pg_query_params($this->conexion, $sql, [$planId]);

        if ($result) {
            return ['status' => 'success', 'message' => 'Plan eliminado exitosamente'];
        } else {
            return ['status' => 'error', 'message' => 'Error al eliminar el plan: ' . pg_last_error($this->conexion)];
        }
    }

    // CRUD CATEGORIAS 
    public function gets_all_categorias()
    {
        global $categorias;
        $sql = 'SELECT * FROM categorias ORDER BY id ASC'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos
        return $data;
    }

    public function categorias_gets_all()
    {
        global $categorias;
        $sql = 'SELECT * FROM categorias ORDER BY id ASC'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos
        $categorias = $data;
        // Definir los encabezados de la tabla
        $header = [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripcion',
        ];

        // Definir clases CSS para la tabla, encabezado, filas y celdas
        $table_class = 'table table-bordered'; // Clase de Bootstrap como ejemplo
        $header_class = 'table-dark';
        $row_class = 'table-row fadeIn';
        $cell_class = 'table-cell';
        $id_tabla = 'clientesAll';

        // Generar la tabla utilizando el método de STC
        return $this->genera_tabla($data, $header, $table_class, $header_class, $row_class, $cell_class, $id_tabla, true, false);
    }

    public function getCategoria($idCat)
    {
        // Consulta preparada para prevenir inyecciones SQL
        $sql = "SELECT * FROM categorias WHERE id = $1 ";
        $result = pg_query_params($this->conexion, $sql, [$idCat]);

        if ($result) {
            // Verifica si hay resultados
            if (pg_num_rows($result) > 0) {
                return pg_fetch_assoc($result);
            } else {
                return ['error' => 'No se encontró la categoria con el ID proporcionado.'];
            }
        } else {
            return ['error' => 'Error al ejecutar la consulta'];
        }
    }
    public function updateCategsorias($idCat, $data)
    {
        // Primero obtén los valores actuales del plan
        $sqlSelect = "SELECT nombre,descripcion, condicion FROM categorias WHERE id = $1";
        $resultSelect = pg_query_params($this->conexion, $sqlSelect, [$idCat]);

        if (!$resultSelect || pg_num_rows($resultSelect) == 0) {
            return ['error' => 'No se encontró el plan con el ID proporcionado'];
        }

        // Obtenemos el plan actual como arreglo asociativo
        $currentCategoria = pg_fetch_assoc($resultSelect);

        // Si el dato ha sido editado en $data, úsalo. Si no, toma el valor actual.
        // $id = isset($data['id']) ? $data['id'] : $currentCategoria['id'];
        $nombre = isset($data['nombre']) ? $data['nombre'] : $currentCategoria['nombre'];
        $descripcion = isset($data['descripcion']) ? $data['descripcion'] : $currentCategoria['descripcion'];
        $condicion = isset($data['condicion']) ? 1 : 0;

        // Ahora actualiza solo los campos modificados o mantén los originales
        $sqlUpdate = "UPDATE categorias SET 
                        nombre = $1,
                        descripcion = $2,
                        condicion = $3
                    WHERE id = $4";

        $resultUpdate = pg_query_params($this->conexion, $sqlUpdate, [
            $nombre,
            $descripcion,
            $condicion,
            $idCat // Usamos el planId que recibimos en la función
        ]);

        if ($resultUpdate && pg_affected_rows($resultUpdate) > 0) {
            return ['success' => true]; // Devuelve éxito si se actualizó
        } else {
            return ['error' => 'Hubo un error en la actualización o no se modificaron los datos'];
        }
    }
    public function createCategoriaw($nombre, $descripcion, $condicion)
    {
        // Escribir la lógica para insertar un nuevo plan en la base de datos
        $sql = "INSERT INTO categorias (nombre, descripcion, condicion) 
                VALUES ($1, $2, $3)";

        // Ejecutar la consulta
        $result = pg_query_params($this->conexion, $sql, [
            $nombre,
            $descripcion,
            $condicion,
        ]);

        if ($result) {
            return ['status' => 'success', 'message' => 'Plan creado exitosamente'];
        } else {
            return ['status' => 'error', 'message' => 'Error al crear el plan: ' . pg_last_error($this->conexion)];
        }
    }
    public function deleteCategoria($idCat)
    {
        // Escribir la lógica para eliminar el plan de la base de datos
        $sql = "DELETE FROM categorias WHERE id = $1";

        // Ejecutar la consulta
        $result = pg_query_params($this->conexion, $sql, [$idCat]);

        if ($result) {
            return ['status' => 'success', 'message' => 'Plan eliminado exitosamente'];
        } else {
            return ['status' => 'error', 'message' => 'Error al eliminar el plan: ' . pg_last_error($this->conexion)];
        }
    }
    function createCategoria($nombre, $descripcion, $condicion)
    {
        global $conexion; // Asegúrate de que la conexión a la base de datos esté correctamente establecida
        $query = "INSERT INTO categorias (nombre, descripcion, condicion) VALUES ($1, $2, $3)";
        $result = mysql_query($conexion, $query, array($nombre, $descripcion, $condicion));
        if ($result) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => pg_last_error($conexion)];
        }
    }

    function updateCategorias($id, $nombre, $descripcion, $condicion)
    {
        global $conexion; // Conexión a la base de datos
        $query = "UPDATE categorias SET nombre = $2, descripcion = $3, condicion = $4 WHERE id = $1";
        $result = pg_query_params($conexion, $query, array($id, $nombre, $descripcion, $condicion));
        if ($result) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => pg_last_error($conexion)];
        }
    }


    // CRUD SUBCATEGORIAS 
    public function subCategorias_gets_all()
    {
        $sql = 'SELECT subcategorias.*, categorias.nombre AS categoria_nombre 
        FROM subcategorias 
        LEFT JOIN categorias ON subcategorias.categoria_id = categorias.id 
        ORDER BY subcategorias.id ASC'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos


        // Definir los encabezados de la tabla
        $header = [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'categoria_nombre' => 'Categoria',
        ];


        // Definir clases CSS para la tabla, encabezado, filas y celdas
        $table_class = 'table table-bordered'; // Clase de Bootstrap como ejemplo
        $header_class = 'table-dark';
        $row_class = 'table-row fadeIn';
        $cell_class = 'table-cell';
        $id_tabla = 'clientesAll';

        // Generar la tabla utilizando el método de STC
        return $this->genera_tabla($data, $header, $table_class, $header_class, $row_class, $cell_class, $id_tabla, true, false);
    }
    public function gets_subcategorias()
    {
        global $categorias;
        $sql = 'SELECT * FROM subcategorias ORDER BY id ASC'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos
        return $data;
    }
    public function getSubCategoria($id)
    {
        // Consulta preparada para prevenir inyecciones SQL
        $sql = "SELECT * FROM products WHERE subcategory = ?"; // Suponemos que tienes una columna 'subcategoria_id' en la tabla de productos.

        // Preparamos la consulta
        $stmt = $this->conexion->prepare($sql);

        if ($stmt) {
            // Vinculamos el parámetro (en este caso el id) al marcador de posición '?'
            $stmt->bind_param("i", $id); // 'i' significa que el parámetro es un entero

            // Ejecutamos la consulta
            $stmt->execute();

            // Obtenemos los resultados
            $result = $stmt->get_result();

            // Verificamos si la consulta devolvió resultados
            if ($result->num_rows > 0) {
                $productos = [];
                while ($row = $result->fetch_assoc()) {
                    $productos[] = $row; // Añadimos el producto al array
                }
                return $productos; // Devolvemos el arreglo de productos
            } else {
                return []; // Devolvemos un arreglo vacío si no se encontraron productos
            }

            // Cerramos el statement
            $stmt->close();
        } else {
            return []; // Devolvemos un arreglo vacío en caso de error al preparar la consulta
        }
    }



    // public function updateSubCategorias($id, $data)
    // {
    //     // Primero obtén los valores actuales del plan
    //     $sqlSelect = "SELECT nombre, categoria_id FROM subcategorias WHERE id = $1";
    //     $resultSelect = pg_query_params($this->conexion, $sqlSelect, [$id]);

    //     if (!$resultSelect || pg_num_rows($resultSelect) == 0) {
    //         return ['error' => 'No se encontró el plan con el ID proporcionado'];
    //     }
    //     $this->prr($data);
    //     // Obtenemos el plan actual como arreglo asociativo
    //     $currentSelection = pg_fetch_assoc($resultSelect);

    //     // Si el dato ha sido editado en $data, úsalo. Si no, toma el valor actual.
    //     // $id = isset($data['id']) ? $data['id'] : $currentCategoria['id'];
    //     $nombre = isset($data['nombre']) ? $data['nombre'] : $currentSelection['nombre'];
    //     $categoria_id = $data['categoriaId'] ? $data['categoriaId'] : $currentSelection['categoria_id'];
    //     // Ahora actualiza solo los campos modificados o mantén los originales
    //     $sqlUpdate = "UPDATE subcategorias SET 
    //                     nombre = $1,
    //                     categoria_id = $2
    //                 WHERE id = $3";

    //     $resultUpdate = pg_query_params($this->conexion, $sqlUpdate, [
    //         $nombre,
    //         $categoria_id,
    //         $id // Usamos el planId que recibimos en la función
    //     ]);

    //     if ($resultUpdate && pg_affected_rows($resultUpdate) > 0) {
    //         return ['success' => true]; // Devuelve éxito si se actualizó
    //     } else {
    //         return ['error' => 'Hubo un error en la actualización o no se modificaron los datos'];
    //     }
    // }
    // public function createSubCategoriaw($nombre, $categoria_id)
    // {
    //     // Escribir la lógica para insertar un nuevo plan en la base de datos
    //     $sql = "INSERT INTO categorias (nombre,  categoria$categoria_id) 
    //             VALUES ($1, $2 )";

    //     // Ejecutar la consulta
    //     $result = pg_query_params($this->conexion, $sql, [
    //         $nombre,
    //         $categoria_id
    //     ]);

    //     if ($result) {
    //         return ['status' => 'success', 'message' => 'Plan creado exitosamente'];
    //     } else {
    //         return ['status' => 'error', 'message' => 'Error al crear el plan: ' . pg_last_error($this->conexion)];
    //     }
    // }
    // public function deleteSubCategoria($id)
    // {
    //     // Escribir la lógica para eliminar el plan de la base de datos
    //     $sql = "DELETE FROM subcategorias WHERE id = $1";

    //     // Ejecutar la consulta
    //     $result = pg_query_params($this->conexion, $sql, [$id]);

    //     if ($result) {
    //         return ['status' => 'success', 'message' => 'Plan eliminado exitosamente'];
    //     } else {
    //         return ['status' => 'error', 'message' => 'Error al eliminar el plan: ' . pg_last_error($this->conexion)];
    //     }
    // }
    // function createSubCategoria($nombre, $categoria_id)
    // {
    //     global $conexion; // Asegúrate de que la conexión a la base de datos esté correctamente establecida
    //     $query = "INSERT INTO subcategorias (nombre, categoria_id) VALUES ($1, $2)";
    //     $result = pg_query_params($conexion, $query, array($nombre, $categoria_id));
    //     if ($result) {
    //         return ['status' => 'success'];
    //     } else {
    //         return ['status' => 'error', 'message' => pg_last_error($conexion)];
    //     }
    // }

    // function updateSubCategorias($id, $nombre, $descripcion, $condicion)
    // {
    //     global $conexion; // Conexión a la base de datos
    //     $query = "UPDATE categorias SET nombre = $2, descripcion = $3, condicion = $4 WHERE id = $1";
    //     $result = pg_query_params($conexion, $query, array($id, $nombre, $descripcion, $condicion));
    //     if ($result) {
    //         return ['status' => 'success'];
    //     } else {
    //         return ['status' => 'error', 'message' => pg_last_error($conexion)];
    //     }
    // }
    function cardProducto22($producto, $bool = false)
    {
        // define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/pf/');
        $archivo = $_SERVER['PHP_SELF'];
        // $this->parr($producto);
        // die();
        // Extrae los datos del producto
        $id = $producto['id'];
        $nombre = $producto['productName'];
        $descripcion = $producto['productDescription'];
        $imagen = $producto['productImage1'];
        $precio = $producto['productPrice'];
        $carpeta_id = $producto['carpeta_id'];
        $rutaImagen = "/pf/admin/productimages/$id/$imagen";

        $descripcionCorta = substr($descripcion, 0, 100); // Limitar a 100 caracteres
        $mostrarBoton = strlen($descripcion) > 100; // Verificar si el texto es demasiado largo

        if ($bool === false) {
            return "
            
        <div class='card h-10'>
            <img src='$rutaImagen' class='card-img-top' alt='$nombre'>
            <div class='card-body'>
                <h5 class='card-title'>$nombre</h5>
               
                <p class='card-price'>Precio: $$precio</p>
                <button class='btn btn-primary ver-detalles' data-id='{$id}'>Ver detalles</button>
            </div>
        </div>";
        } else {
            return "
        <div class='col-md-3 mb-2'>
            <div class='card-carousel'>
                <img src='$rutaImagen' class='card-img-top' alt='$nombre'>
                <div class='card-body'>
                    <h5 class='card-title'>$nombre</h5>
                    <p class='card-text' id='descripcion-$id'>
                        $descripcionCorta" . ($mostrarBoton ? "..." : "") . "
                        <span id='descripcion-completa-$id' style='display: none;'>$descripcion</span>
                    </p>
                    " . ($mostrarBoton ? "<button class='btn btn-link ver-mas' data-id='$id'>Ver más</button>" : "") . "
                    <p class='card-price'>Precio: $$precio</p>
                    <button class='btn btn-primary ver-detalles' data-id='{$id}'>Ver detalles</button>
                </div>
            </div>
        </div>";
        }
    }

    function cardProducto($producto, $bool = false)
    {

        // Extrae los datos del producto (asumiendo que $producto es un array asociativo)
        // $urls = 'https://rutas.casasantoni.com.ar';

        $id = $producto['id'];
        $nombre = $producto['productName'];
        $descripcion = $producto['productDescription'];
        $imagen = $producto['productImage1'];
        $precio = $producto['productPrice'];
        $carpeta_id = $producto['id']; // Enlace al detalle del producto, por ejemplo
        $rutaImagen = "/pf/admin/productimages/$id/$imagen";
        // Verifica si la imagen comienza con "http"
        if (is_string($imagen) && strpos($imagen, 'http') === 0) {
            // Si empieza con http, usa la URLs completa
            $rutaImagen = $imagen;
        } else {
            // Si no, construye la ruta local
            $rutaImagen = "/pf/admin/productimages/$id/$imagen";
        }

        // $this->parr($rutaImagen);
        // Genera la tarjeta HTML
        return "
            <div class='card h-10'>
            <img src='$rutaImagen' class='card-img-top' alt='$nombre'>
            <div class='card-body'>
                <h5 class='card-title'>$nombre</h5>
               
                <p class='card-price'>Precio: $$precio</p>
                <button class='btn btn-primary ver-detalles' data-id='{$id}'>Ver detalles</button>
            </div>
        </div>";
    }
    function cardProductoUnique($producto)
    {

        $producto = $producto['data'];
        $id = $producto['id'];
        $nombre = $producto['productName'];
        $descripcion = $producto['productDescription'];
        $marca = $producto['productCompany'];
        $imagenes = [
            $producto['productImage1'],
            $producto['productImage2'],
            $producto['productImage3']
        ];
        $precio = (float) $producto['productPrice'];
        $precioDescuento = (float) $producto['productPriceBeforeDiscount'];
        $carpeta_id = $producto['id'];
        // Inicializa un array para las rutas de las imágenes
        $rutasImagenes = [];
        foreach ($imagenes as $imagen) {
            if (!is_null($imagen) && strpos($imagen, 'http') === 0) {
                $rutasImagenes[] = $imagen; // URL externa
            } elseif (!is_null($imagen)) {
                $rutasImagenes[] = "/pf/admin/productimages/$carpeta_id/$imagen"; // Ruta local
            }
        }


        // Imagen principal (la primera del array)
        $rutaImagenPrincipal = $rutasImagenes[0];
        $miniaturas = '';
        // Genera las miniaturas
        foreach ($rutasImagenes as $rutaImagen) {
            if (!empty($rutaImagen)) { // Asegúrate de que la ruta no esté vacía
                // $miniaturas .= "<img src='$rutaImagen' class='thumbnail' alt='$nombre' onclick='showImage(this.src)'>";
                $miniaturas .= "<img src='$rutaImagen' class='thumbnail' alt='$nombre' onclick='showImage(this.src)'>";
            }
        }

        // Mostrar el valor dividido en 6 cuotas
        // <p class='text-muted'>En 6 cuotas de $" . number_format($precio / 6, 2, ',', '.') . "</p>
        return "
            <div class='card h-100 detail'>
                <div class='row no-gutters'>
                    <div class='col-md-6'>
                        <img src='$rutaImagenPrincipal' class='card-img-top' id='detail-img' alt='$nombre'>
                        <div id='thumbnail-container'>
                            $miniaturas
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div id='detail-body' class='p-3'>
                            <h5 id='detail-title'>$nombre</h5>
                            <p id='detail-price'>$" . number_format($precio, 2, ',', '.') . "</p>
                            <div>
                            <p class='text'>$marca</p>
                            <h4>Caracteristicas y especificaciones</h4>
                            <p>$descripcion</p>
                            <div class='button-container'>
                            <button class='btn btn-secondary' id='back-button'>Volver a la lista</button>
                            <button class='btn btn-success' id='contactarVendedorBtn' data-product-id='$id' data-title='$nombre' data-price='$precio' data-descripcion='$descripcion'>WhatsApp al Vendedor</button>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }




    public function obtenerProductoPorId($id)
    {
        global $conexion; // Asegúrate de tener una conexión MySQL válida aquí

        // Preparar la consulta para evitar inyecciones SQL
        $query = "SELECT * FROM products WHERE id = ?";

        // Prepara la consulta
        if ($stmt = mysqli_prepare($conexion, $query)) {
            // Vincular el parámetro $id al marcador de posición ?
            mysqli_stmt_bind_param($stmt, "i", $id); // "i" es para enteros (integer)

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                // Obtener el resultado
                $result = mysqli_stmt_get_result($stmt);

                // Extraer los datos del producto
                $producto = mysqli_fetch_assoc($result);

                // Si no se encontró el producto, devuelve un error
                if (!$producto) {
                    return ['status' => 'error', 'message' => 'Producto no encontrado.'];
                }

                // Si se encontró el producto, devuelve sus datos
                return ['status' => 'success', 'data' => $producto];
            } else {
                // Si hay un error en la ejecución, devuelve el error
                return ['status' => 'error', 'message' => mysqli_error($conexion)];
            }

            // Cerrar la declaración preparada
            mysqli_stmt_close($stmt);
        } else {
            // Si no se pudo preparar la consulta, devuelve un error
            return ['status' => 'error', 'message' => mysqli_error($conexion)];
        }
    }


    public function obtenerProductos($limit, $offset)
    {
        // Utiliza parámetros para evitar inyección SQL
        $query = "SELECT * FROM productos LIMIT $1 OFFSET $2";
        $result = pg_query_params($this->conexion, $query, array($limit, $offset));

        if ($result) {
            $productos = pg_fetch_all($result);
            return $productos ?: []; // Devuelve un array vacío si no hay resultados
        } else {
            return ['error' => pg_last_error($this->conexion)]; // Devuelve un error si la consulta falla
        }
    }
    public function buscar($buscar)
    {
        $buscar = '%' . $buscar . '%'; // Añadir comodines para el LIKE

        // Consulta SQL usando LIKE con parámetros preparados
        $query = "SELECT * FROM products WHERE productName LIKE ? OR productDescription LIKE ? OR productCompany LIKE ? ";

        // Preparamos la consulta
        if ($stmt = mysqli_prepare($this->conexion, $query)) {
            // Vinculamos el parámetro $buscar a los tres marcadores de posición "?"
            mysqli_stmt_bind_param($stmt, "sss", $buscar, $buscar, $buscar); // "sss" indica que los parámetros son cadenas

            // Ejecutamos la consulta
            if (mysqli_stmt_execute($stmt)) {
                // Obtenemos el resultado de la consulta
                $result = mysqli_stmt_get_result($stmt);

                // Extraemos los productos
                $productos = mysqli_fetch_all($result, MYSQLI_ASSOC);

                // Devolvemos los productos si existen, o un array vacío si no hay resultados
                return $productos ?: [];
            } else {
                // En caso de error, devolvemos un mensaje con el error
                return ['error' => mysqli_error($this->conexion)];
            }

            // Cerramos la declaración preparada
            mysqli_stmt_close($stmt);
        } else {
            // Si no se pudo preparar la consulta, devolvemos el error
            return ['error' => mysqli_error($this->conexion)];
        }
    }




    // function cardProductoCategoria($productoid)
    // {
    //     global $conexion;
    //     $bool = true;
    //     // Obtener los datos del producto
    //     $data = $this->obtenerProductoPorId($productoid);

    //     if (!$data) {
    //         return '<p>Producto no encontrado.</p>'; // Mensaje si no se encuentra el producto
    //     }

    //     $datos = $data['data'];
    //     $categoria = $datos['categoria_id'];
    //     // echo $categoria; // Esto puede ser útil para depuración, pero quizás quieras quitarlo después

    //     $query = "SELECT * FROM productos WHERE categoria_id = $1";
    //     $result = pg_query_params($conexion, $query, array($categoria));

    //     if ($result) {
    //         $productos = '';
    //         while ($producto = pg_fetch_assoc($result)) {
    //             $productos .= $this->cardProducto($producto, true); // Concatenar los productos
    //         }
    //         return $productos ?: '<p>No hay productos en esta categoría.</p>'; // Mensaje si no hay productos
    //     }

    //     return ''; // Devuelve vacío si no hay productos
    // }
    function getProductImage($producto)
    {
        $carpeta_id = $producto['carpeta_id'];
        $imagen = $producto['producto_imagen1'];
        $rutaImagen = "panel/assets/uploads/productos/$carpeta_id/$imagen";
        $this->parr($rutaImagen);
        return (strpos($imagen, 'http') === 0) ? $imagen : $rutaImagen;
    }

    function cardProductoCategoria($productoid)
    {
        global $conexion;

        // Obtener los datos del producto
        $data = $this->obtenerProductoPorId($productoid);

        if (!$data) {
            return []; // Devuelve un array vacío si no se encuentra el producto
        }

        $datos = $data['data'];
        $categoria = $datos['categoria_id'];

        // Realiza la consulta para obtener los productos de la categoría
        $query = "SELECT * FROM productos WHERE categoria_id = $1";
        $result = pg_query_params($conexion, $query, array($categoria));

        if ($result) {
            $productos = [];
            while ($producto = pg_fetch_assoc($result)) {
                $productos[] = $producto; // Agrega cada producto al array
            }
            return $productos; // Devuelve el array de productos o un array vacío si no hay productos
        }

        return []; // Devuelve un array vacío si no hay productos
    }




}
