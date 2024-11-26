<?php
include dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/classes/stc.class.php'; // Corrige aquí

class CLI extends STC
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        parent::__construct($conexion); // Llamar al constructor de STC si es necesario
    }


    // Función para obtener todos los registros y mostrarlos en una tabla
    public function cli_gets_all()
    {
        $sql = 'SELECT * FROM clientes'; // Consulta para obtener todos los registros
        $data = $this->get_query($sql); // Obtener los datos

        // Definir los encabezados de la tabla
        $header = [
            'id' => 'ID Persona',
            'rsocial' => 'Nombre',
            'documento' => 'DNI',
            'fechaalta' => 'Instalado',
            'direccion' => 'Dirección',
            'telefonos' => 'Teléfono',
            'email' => 'Email',
        ];
        // Definir clases CSS para la tabla, encabezado, filas y celdas
        $table_class = 'table table-bordered'; // Clase de Bootstrap como ejemplo
        $header_class = 'table-dark';
        $row_class = 'table-row fadeIn';
        $cell_class = 'table-cell';
        $id_tabla = 'clientesAll';
        // Generar la tabla utilizando el método de STC
        return $this->genera_tabla($data, $header, $table_class, $header_class, $row_class, $cell_class, $id_tabla, true, true);
    }

    public function getClient($clientId)
    {
        // Consulta preparada para prevenir inyecciones SQL
        $sql = "SELECT * FROM clientes WHERE id = $1";
        $result = pg_query_params($this->conexion, $sql, [$clientId]);

        if ($result) {
            return pg_fetch_assoc($result);
        } else {
            return ['error' => 'Error al ejecutar la consulta'];
        }
    }

}