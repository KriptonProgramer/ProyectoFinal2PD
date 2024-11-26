<?php
include '../../config/Conexion.php'; // Asegúrate de que esta ruta sea correcta

if (!class_exists('STC')) {
    require_once '../../classes/stc.class.php';
}

class PROD extends STC
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        parent::__construct($conexion); // Llamar al constructor de STC si es necesario
    }

    // Obtener todos los productos
    public function productos_gets()
    {
        $sql = 'SELECT * FROM productos ORDER BY id ASC'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos
        return $data;
    }
    public function productos_gets_all()
    {
        $sql = 'SELECT * FROM productos ORDER BY id ASC'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos

        // Definir los encabezados de la tabla
        $header = [
            'id' => 'ID',
            'nombre' => 'Nombre',
            // 'producto_imagen1' => 'Imagen Principal',
            'stock' => 'Stock',
            // Agrega más campos si es necesario
        ];

        // Definir clases CSS para la tabla
        $table_class = 'table-class table-bordered'; // Clase de Bootstrap como ejemplo
        $header_class = 'header-class';
        $row_class = 'row-class';
        $cell_class = 'cell-class';
        $id_tabla = 'tabla-dinamica';

        // Generar la tabla utilizando el método de STC
        return $this->genera_tabla($data, $header, $table_class, $header_class, $row_class, $cell_class, $id_tabla, true, false);
    }

    // Obtener un producto por ID
    public function getProducto($id)
    {
        $sql = "SELECT * FROM productos WHERE id = $1";
        $result = pg_query_params($this->conexion, $sql, [$id]);

        if ($result) {
            if (pg_num_rows($result) > 0) {
                return pg_fetch_assoc($result);
            } else {
                return ['error' => 'No se encontró el producto con el ID proporcionado.'];
            }
        } else {
            return ['error' => 'Error al ejecutar la consulta'];
        }
    }

    // Actualizar un producto
    public function updateProducto($id, $data, $files)
    {
        // Primero obtén los valores actuales del producto
        $sqlSelect = "SELECT * FROM productos WHERE id = $1";
        $resultSelect = pg_query_params($this->conexion, $sqlSelect, [$id]);

        if (!$resultSelect || pg_num_rows($resultSelect) == 0) {
            return ['error' => 'No se encontró el producto con el ID proporcionado'];
        }

        // Obtenemos el producto actual como arreglo asociativo
        $currentSelection = pg_fetch_assoc($resultSelect);

        // Actualiza los campos
        $nombre = $data['nombre'] ?? $currentSelection['nombre'];
        $categoria_id = $data['categoria_id'] ?? $currentSelection['categoria_id'];
        $subcategoria_id = $data['subcategoria_id'] ?? $currentSelection['subcategoria_id'];
        $marca = $data['marca'] ?? $currentSelection['marca'];
        $precio = $data['precio'] ?? $currentSelection['precio'];
        $stock = $data['stock'] ?? $currentSelection['stock'];
        $condicion = $data['condicion'] ?? $currentSelection['condicion'];
        $descripcion = $data['descripcion'] ?? $currentSelection['descripcion'];
        $carpeta_id = $currentSelection['carpeta_id'];

        // Manejo de la carga de imágenes
        $producto_imagen1 = $currentSelection['producto_imagen1'];
        $producto_imagen2 = $currentSelection['producto_imagen2'];
        $producto_imagen3 = $currentSelection['producto_imagen3'];

        // Definir la ruta de carga de imágenes
        $uploadDir = '../../assets/uploads/productos/' . $carpeta_id . '/';

        // Verificar si el directorio existe y es escribible
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            return ['status' => 'error', 'message' => 'El directorio de carga no existe o no es escribible'];
        }

        for ($i = 1; $i <= 3; $i++) {
            if (isset($files["producto_imagen$i"]) && $files["producto_imagen$i"]['error'] == 0) {
                // Nombre de la imagen que se va a subir
                $uploadImageName = basename($files["producto_imagen$i"]['name']);
                $targetFilePath = $uploadDir . $uploadImageName;

                // Verificar si la imagen ya existe
                if (in_array($uploadImageName, [$producto_imagen1, $producto_imagen2, $producto_imagen3]) || file_exists($targetFilePath)) {
                    // La imagen ya existe, no la subas
                    continue;
                }

                // Si no existe, sube la nueva imagen
                if (move_uploaded_file($files["producto_imagen$i"]['tmp_name'], $targetFilePath)) {
                    // Eliminar la imagen antigua si es diferente
                    // Eliminar la imagen antigua si es diferente
                    if ($i == 1 && $producto_imagen1 !== $uploadImageName) {
                        $oldImagePath = $uploadDir . $producto_imagen1;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        } else {
                            error_log("No se pudo encontrar la imagen para eliminar: $oldImagePath");
                        }
                    } elseif ($i == 2 && $producto_imagen2 !== $uploadImageName) {
                        $oldImagePath = $uploadDir . $producto_imagen2;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        } else {
                            error_log("No se pudo encontrar la imagen para eliminar: $oldImagePath");
                        }
                    } elseif ($i == 3 && $producto_imagen3 !== $uploadImageName) {
                        $oldImagePath = $uploadDir . $producto_imagen3;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        } else {
                            error_log("No se pudo encontrar la imagen para eliminar: $oldImagePath");
                        }
                    }


                    // Asignar la nueva imagen al producto
                    if ($i == 1) {
                        $producto_imagen1 = $uploadImageName;
                    } elseif ($i == 2) {
                        $producto_imagen2 = $uploadImageName;
                    } elseif ($i == 3) {
                        $producto_imagen3 = $uploadImageName;
                    }
                } else {
                    return ['status' => 'error', 'message' => "Error al mover la imagen $i: " . error_get_last()['message']];
                }
            }
        }

        // Actualiza el producto
        $sqlUpdate = "UPDATE productos SET 
                    nombre = $1,
                    categoria_id = $2,
                    subcategoria_id = $3,
                    marca = $4,
                    precio = $5,
                    stock = $6,
                    producto_imagen1 = $7,
                    producto_imagen2 = $8,
                    producto_imagen3 = $9,
                    condicion = $10,
                    descripcion = $11
                WHERE id = $12";

        $resultUpdate = pg_query_params($this->conexion, $sqlUpdate, [
            $nombre,
            $categoria_id,
            $subcategoria_id,
            $marca,
            $precio,
            $stock,
            $producto_imagen1,
            $producto_imagen2,
            $producto_imagen3,
            $condicion,
            $descripcion,
            $id
        ]);

        if ($resultUpdate && pg_affected_rows($resultUpdate) > 0) {
            return ['status' => true, 'message' => 'Producto actualizado exitosamente'];
        } else {
            return ['status' => false, 'message' => 'Error al actualizar el producto: ' . pg_last_error($this->conexion)];
        }
    }




    // Eliminar un producto
    public function deleteProducto($id)
    {
        $sql = "DELETE FROM productos WHERE id = $1";
        $result = pg_query_params($this->conexion, $sql, [$id]);

        if ($result) {
            return ['status' => 'success', 'message' => 'Producto eliminado exitosamente'];
        } else {
            return ['status' => 'error', 'message' => 'Error al eliminar el producto: ' . pg_last_error($this->conexion)];
        }
    }

    // Crear un nuevo producto
    public function createProducto($data, $files)
    {


        // Verifica que todos los campos necesarios estén presentes
        $nombre = $data['nombre'] ?? null;
        $categoria_id = $data['categoria_id'] ?? null;
        $subcategoria_id = $data['subcategoria_id'] ?? null;
        $marca = $data['marca'] ?? null;
        $precio = $data['precio'] ?? null;
        $stock = $data['stock'] ?? null;
        $condicion = isset($data['condicion']) ? 1 : 0;
        $descripcion = $data['descripcion'];
        // Validar campos obligatorios
        if (!$nombre || !$categoria_id || !$subcategoria_id || !$marca || !$precio || !$stock) {
            return json_encode(['status' => 'error', 'message' => 'Faltan algunos datos necesarios para crear el producto']);
        }
        $aleatorio = $this->generarNombreHexadecimal(5);
        $imageNames = [];
        $uploadDir = '../../assets/uploads/productos/' . $aleatorio . '/';

        // Verificar si el directorio existe y es escribible
        // Verificar si el directorio existe, si no, crearlo
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return json_encode(['status' => 'error', 'message' => 'No se pudo crear el directorio para las imágenes del producto']);
            }
        } elseif (!is_writable($uploadDir)) {
            return json_encode(['status' => 'error', 'message' => 'El directorio de carga no es escribible']);
        }

        for ($i = 1; $i <= 3; $i++) {
            if (isset($files["producto_imagen$i"]) && $files["producto_imagen$i"]['error'] == 0) {
                // Validación del tipo de imagen
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($files["producto_imagen$i"]['type'], $allowedTypes)) {
                    $imageName = basename($files["producto_imagen$i"]['name']);
                    $targetFilePath = $uploadDir . $imageName;

                    if (move_uploaded_file($files["producto_imagen$i"]['tmp_name'], $targetFilePath)) {
                        $imageNames[] = $imageName;
                    } else {
                        return json_encode(['status' => 'error', 'message' => "Error al subir la imagen $i: " . error_get_last()['message']]);
                    }
                } else {
                    return json_encode(['status' => 'error', 'message' => "Tipo de imagen no permitido para imagen $i"]);
                }
            } else {
                $imageNames[] = null; // Si no hay imagen, guardar null
            }
        }

        // Inserta el producto en la base de datos
        $query = "INSERT INTO productos (nombre, categoria_id, subcategoria_id, marca,descripcion, precio, stock, producto_imagen1, producto_imagen2, producto_imagen3, condicion,carpeta_id) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";

        $result = pg_query_params($this->conexion, $query, [
            $nombre,
            $categoria_id,
            $subcategoria_id,
            $marca,
            $descripcion,
            $precio,
            $stock,
            $imageNames[0],
            $imageNames[1],
            $imageNames[2],
            $condicion,
            $aleatorio
        ]);

        if ($result) {
            return ['status' => true, 'message' => 'Producto creado exitosamente'];
        } else {
            return ['status' => false, 'message' => 'Error al insertar producto: ' . pg_last_error($this->conexion)];
        }
        exit; // Asegúrate de que no haya nada más después de esto
    }


}
