<?php
include  '../../config/Conexion.php';

// $currentFile = basename($_SERVER['PHP_SELF']);
// echo "El nombre del archivo actual es: " . $currentFile;
// $currentFile = basename(__FILE__);
// echo "El nombre del archivo actual es: " . $currentFile;


if (!class_exists('STC')) {
    require_once '../../classes/stc.class.php';
}


class GRL extends STC
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        parent::__construct($conexion); // Llamar al constructor de STC si es necesario
    }
      public function getConnection() {
        return $this->conexion;
    }

    public function closeConnection() {
        pg_close($this->conexion);
    }

    // Función para obtener todos los registros y mostrarlos en una tabla
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
        $table_class = 'table-class table-bordered'; // Clase de Bootstrap como ejemplo
        $header_class = 'header-class';
        $row_class = 'row-class';
        $cell_class = 'cell-class';
        $id_tabla = 'tabla-dinamica';

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
        $result = pg_query_params($conexion, $query, array($nombre, $descripcion, $condicion));
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
        $table_class = 'table-class table-bordered'; // Clase de Bootstrap como ejemplo
        $header_class = 'header-class';
        $row_class = 'row-class';
        $cell_class = 'cell-class';
        $id_tabla = 'tabla-dinamica';

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
        $sql = "SELECT * FROM subcategorias WHERE id = $1 ";
        $result = pg_query_params($this->conexion, $sql, [$id]);

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
    public function updateSubCategorias($id, $data)
    {
        // Primero obtén los valores actuales del plan
        $sqlSelect = "SELECT nombre, categoria_id FROM subcategorias WHERE id = $1";
        $resultSelect = pg_query_params($this->conexion, $sqlSelect, [$id]);

        if (!$resultSelect || pg_num_rows($resultSelect) == 0) {
            return ['error' => 'No se encontró el plan con el ID proporcionado'];
        }
        // $this->prr($data);
        // Obtenemos el plan actual como arreglo asociativo
        $currentSelection = pg_fetch_assoc($resultSelect);

        // Si el dato ha sido editado en $data, úsalo. Si no, toma el valor actual.
        // $id = isset($data['id']) ? $data['id'] : $currentCategoria['id'];
        $nombre = isset($data['nombre']) ? $data['nombre'] : $currentSelection['nombre'];
        $categoria_id = $data['categoria_id'] ? $data['categoria_id'] : $currentSelection['categoria_id'];
        // Ahora actualiza solo los campos modificados o mantén los originales
        $sqlUpdate = "UPDATE subcategorias SET 
                        nombre = $1,
                        categoria_id = $2
                    WHERE id = $3";

        $resultUpdate = pg_query_params($this->conexion, $sqlUpdate, [
            $nombre,
            $categoria_id,
            $id // Usamos el planId que recibimos en la función
        ]);

        if ($resultUpdate && pg_affected_rows($resultUpdate) > 0) {
            return ['success' => true]; // Devuelve éxito si se actualizó
        } else {
            return ['error' => 'Hubo un error en la actualización o no se modificaron los datos'];
        }
    }
    public function createSubCategoriaw($nombre, $categoria_id)
    {
        // Escribir la lógica para insertar un nuevo plan en la base de datos
        $sql = "INSERT INTO categorias (nombre,  categoria) 
                VALUES ($1, $2 )";

        // Ejecutar la consulta
        $result = pg_query_params($this->conexion, $sql, [
            $nombre,
            $categoria_id
        ]);

        if ($result) {
            return ['status' => 'success', 'message' => 'Plan creado exitosamente'];
        } else {
            return ['status' => 'error', 'message' => 'Error al crear el plan: ' . pg_last_error($this->conexion)];
        }
    }
    public function deleteSubCategoria($id)
    {
        // Escribir la lógica para eliminar el plan de la base de datos
        $sql = "DELETE FROM subcategorias WHERE id = $1";

        // Ejecutar la consulta
        $result = pg_query_params($this->conexion, $sql, [$id]);

        if ($result) {
            return ['status' => 'success', 'message' => 'Plan eliminado exitosamente'];
        } else {
            return ['status' => 'error', 'message' => 'Error al eliminar el plan: ' . pg_last_error($this->conexion)];
        }
    }
    function createSubCategoria($nombre, $categoria_id)
    {
        global $conexion; // Asegúrate de que la conexión a la base de datos esté correctamente establecida
        $query = "INSERT INTO subcategorias (nombre, categoria_id) VALUES ($1, $2)";
        $result = pg_query_params($conexion, $query, array($nombre, $categoria_id));
        if ($result) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => pg_last_error($conexion)];
        }
    }

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

    function toLog($message) {
        $logFile = basename(__FILE__) . '.log';
        $date = date('Y-m-d H:i:s'); 
        $entry = "[$date] $message" . PHP_EOL;
        file_put_contents($logFile, $entry, FILE_APPEND);
    }

}
