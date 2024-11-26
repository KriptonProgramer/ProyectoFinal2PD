<?php

include '../../config/Conexion.php';

class STC
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Ejecutar una consulta y devolver el resultado
    public function get_query($sql)
    {
        $result = pg_query($this->conexion, $sql);
        if (!$result) {
            return false;
        }
        $data = [];
        while ($row = pg_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }


    // Función para generar la cabecera de la tabla con una columna "Acción" opcional
    public function arma_headers_tabla($header, $header_class = '', $add_actions = false)
    {
        $ret = "<thead><tr>\n";
        foreach ($header as $key => $val) {
            $ret .= "<th class='{$header_class}'>{$val}</th>\n";
        }
        if ($add_actions) {
            $ret .= "<th class='{$header_class}'>Acción</th>\n"; // Agregar columna "Acción"
        }
        $ret .= "</tr></thead>\n";
        return $ret;
    }

    // Función para generar el cuerpo de la tabla con una columna "Acción" opcional
    public function arma_body_tabla($data, $header, $row_class = '', $cell_class = '', $add_actions = false, $pdf = false)
    {
        $ret = "<tbody>\n";
        foreach ($data as $row) {
            $ret .= "<tr class='{$row_class}'>\n";
            foreach ($header as $campo => $titulo) {
                // Verificar si el campo es la imagen principal
                if ($campo === 'imagen_principal') {
                    // Generar el tag <img> con la ruta de la imagen
                    $ret .= "<td class='{$cell_class}'>
                                <img src='ruta/a/imagenes/{$row[$campo]}' alt='Imagen' style='width: 50px; height: auto;' />
                             </td>\n";
                } else {
                    $ret .= "<td class='{$cell_class}'>" . (isset($row[$campo]) ? $row[$campo] : '') . "</td>\n";
                }
            }
            if ($add_actions) {
                // Agregar botones de acción con iconos de editar y eliminar
                $ret .= "<td class='{$cell_class}' style='text-align: center; display: flex; justify-content: space-around'>
                        <a href='#' class='edit-icon' data-id='{$row['id']}' onclick='edit({$row['id']})'><i class='fas fa-edit'></i></a>
                        <a href='#' class='delete-icon' data-id='{$row['id']}' onclick='delete({$row['id']})'><i class='fas fa-trash'></i></a>";
                if ($pdf) {
                    // Agregar botón para PDF
                    $ret .= "<a href='#' class='pdf-icon'><i class='fas fa-file-pdf'></i></a>";
                }
                $ret .= "</td>\n"; // Cerrar la celda de acciones
            }
            $ret .= "</tr>\n"; // Cerrar la fila
        }
        $ret .= "</tbody>\n";
        return $ret;
    }
    
    // Función principal para armar la tabla completa con clases CSS opcionales
    public function genera_tabla($data, $header, $table_class = '', $header_class = '', $row_class = '', $cell_class = '', $table_id = '', $add_actions = false, $pdf = false)
    {
        if (empty($data)) {
            return "No se encontraron resultados.";
        }

        $table = "<table id='{$table_id}' class='{$table_class}'>\n";
        $table .= $this->arma_headers_tabla($header, $header_class, $add_actions); // Generar encabezado con clase y opción de columna de acción
        $table .= $this->arma_body_tabla($data, $header, $row_class, $cell_class, $add_actions, $pdf); // Generar cuerpo con clase y opción de columna de acción
        $table .= "</table>\n";
        return $table;
    }

    /**
     * Trae el valor del primer campo del primer registro del query
     *
     * @param string $query Sql query
     * @return mixed Valor solicitado
     * @author Roberto Gimenez
     * @since 2011-10-01
     */
    function get_queryval($query)
    {
        $res = $this->get_query($query);
        if (!is_null($res) && count($res)) {
            foreach ($res[0] as $val)
                break;
            return $val;
        }
        $this->error("Sin Registros<br />\n");
        return NULL;
    }

    /**
    * Genera una tabla html para editar un registro
    * @global object $txtlng
    * @global object $txtlngfile
    * @global int $debug
    * @global string $tiempos
    * @global type $inicio
    * @param array|object $reg array u objeto del registro a editar
    * @param array $edtreg array con los parametros para la edicion de cada campo
    * el formato es de la forma
    * array(<br>
    * 		'nombre_campo'=>array(<br >
    * 			\t'titulo'='nombre a aprecer a la izquierda del input',
    * 			'type'=>'tipo de input los valores pueden ser'
    * 			<ul>
    * 				<li><b>text</b> input de tipo text
    * 				<li><b>password</b> input de tipo password
    * 				<li><b>porcentaje</b> muestra % al final
    * 				<li><b>moneda</b> muestra simbolo monetario al principio
    * 				<li><b>textarea</b> input de tipo textarea
    * 				<li><b>checkbox</b> input checkbox
    * 				<li><b>readonly</b> input de tipo text de solo lectura
    * 				<li><b>hidden</b> input de tipo hidden
    * 				<li><b>hiddenshw</b> agrega un hidden y muestra el valor sin posibilidad de modificacion
    * 				<li><b>select</b> input de tipo select
    * 				<li><b>date</b> input de tipo text para fecha Hora
    * 				<li><b>datewoh</b> input de tipo para fecha sin Hora
    * 				<li><b>showd</b> muestra una fecha transformando formato yyyy-mm-dd en dd/mm/yyyy
    * 				<li><b>showdspan</b> muestra una fecha transformando formato yyyy-mm-dd en dd/mm/yyyy
    * 									y lo coloca en un span de id = nombre de campo
    * 				<li><b>showdspan</b> muestra un texto y coloca en un span de id = nombre de campo
    * 				<li><b>file</b> input de tipo file
    * 				<li><b>wysiwyg</b> input de tipo FCKeditor
    * 				<li><b>radio</b> input de tipo radio
    * 				<li><b>siNo</b> muestra 0,1 true,false en si,no
    * 			</ul>
    *                  'select'=>array con los valores a aplicar al tipo select, se descarta en cualquier otro tipo
    *                  <ul>
    *                      <li><b>tabla</b> Nombre de tabla
    *                      <li><b>campos</b> obj( 'id'=>'nombre de Campo', 'txt'=>array( nombre de Campos ), 'txtsep'=>'separador de campos')
    *                      <li><b>sele</b> valor del campo id que seleccionara la option
    *                      <li><b>filtro</b> valor a aplicar al query para filtrar Ej.: 'where id > 1'
    *                      <li><b>add</b> array de objetos a agregar al resultado del query en formato del registro
    *                      <li><b>decode</b> si se debe aplicar urldecode al resultado
    *                      <li><b>order</b> valor a aplicar al query para ordenar Ej.: 'order by id'
    *                  </ul>

    * 		)<br>
    * )<br>
    * @return type
    */
    function edit_reg($reg, $edtreg = array())
    {
        global $txtlng, $txtlngfile, $debug, $tiempos, $inicio;

        // Convertir objeto a array si es necesario
        if (is_object($reg)) {
            $reg = (array) $reg;
        }

        // Si $edtreg está vacío, inicializarlo con campos vacíos
        if (empty($edtreg)) {
            foreach ($reg as $fld => $val) {
                $edtreg[$fld] = array();
            }
        }

        if ($debug) {
            $this->prr($edtreg, '$edtreg'); // Imprimir la estructura de $edtreg si está en modo debug
        }

        foreach ($reg as $fld => $val) {
            // Registrar el tiempo transcurrido en la ejecución
            $tiempos[] = __FILE__ . " " . __LINE__ . " edit_reg $fld, segundos: " . (time() - $inicio);

            if (isset($edtreg[$fld])) {
                // Configurar atributos por defecto
                $edtreg[$fld]['type'] = $edtreg[$fld]['type'] ?? 'text';
                $edtreg[$fld]['name'] = $edtreg[$fld]['name'] ?? true;
                $edtreg[$fld]['id'] = $edtreg[$fld]['id'] ?? false;

                // Obtener el nombre y ID para el campo
                $nameid = $this->get_name_id($fld, $edtreg[$fld]['name'], $edtreg[$fld]['id']);
                // Generar el input y asignarlo al registro
                $reg[$fld] = $this->generate_input($fld, $val, $edtreg[$fld], $nameid);
            }
        }

        return $reg; // Retornar el registro editado
    }

    private function get_name_id($fld, $hasName, $hasId)
    {
        $nameid = '';
        if ($hasName) {
            $nameid .= " name='$fld'"; // Agregar nombre si está habilitado
        }
        if ($hasId) {
            $nameid .= " id='$fld'"; // Agregar ID si está habilitado
        }
        return $nameid; // Retornar el string con el nombre e ID
    }

    private function generate_input($fld, $val, $edtreg, $nameid)
    {
        // Verificar que el tipo es válido
        if (!in_array($edtreg['type'], ['text', 'password', 'porcentaje', 'moneda', 'textarea', 'checkbox', 'readonly', 'hidden', 'select', 'date', 'datewoh'])) {
            return ''; // Tipo no válido
        }

        $tr = ''; // Variable para almacenar el HTML del input
        // Obtener el valor final, aplicando función si está definida
        $value = !empty($edtreg['function']) ? $edtreg['function']($val) : $val;
        $params = $edtreg['parameters'] ?? "class='input'"; // Parámetros para el input

        // Generar el HTML basado en el tipo de input
        switch ($edtreg['type']) {
            case 'text':
            case 'password':
            case 'porcentaje':
                $tr = "<input$nameid type='{$edtreg['type']}' value='" . htmlspecialchars($value) . "' $params />";
                if ($edtreg['type'] === 'porcentaje') {
                    $tr .= "%"; // Añadir símbolo de porcentaje
                }
                break;
            case 'moneda':
                $tr = "$<input$nameid value='" . sprintf("%01.2f", $val) . "' $params />";
                break;
            case 'textarea':
                $tr = "<textarea$nameid $params>" . htmlspecialchars($value) . "</textarea>";
                break;
            case 'checkbox':
                $checked = ($val > 0 || $val == 't') ? " checked" : "";
                $tr = "<input$nameid type='checkbox' value='1'$checked $params />";
                break;
            case 'readonly':
                $tr = "<input$nameid readonly value='" . htmlspecialchars($value) . "' $params />";
                break;
            case 'hidden':
                $tr = "<input$nameid type='hidden' value='" . htmlspecialchars($val) . "' />";
                break;
            case 'select':
                $tr = $this->generate_select($fld, $val, $edtreg, $nameid);
                break;
            case 'date':
            case 'datewoh':
                $tr = "<input$nameid type='date' value='" . htmlspecialchars($value) . "' $params />";
                break;
            default:
                break;
        }

        // Si hay parámetros adicionales de posición, añadirlos
        if (isset($edtreg['pos'])) {
            $tr .= $edtreg['pos'];
        }

        return $tr; // Retornar el HTML generado
    }

    private function generate_select($fld, $val, $edtreg, $nameid)
    {
        $options = ""; // Opciones del select
        $params = $edtreg['parameters'] ?? "class='sele'"; // Parámetros del select
        $t = $edtreg['select'] ?? []; // Obtener configuración del select

        // Generar las opciones del select
        if (isset($t['valores'])) {
            foreach ($t['valores'] as $valor) {
                $selected = ($valor->id == $val) ? " selected" : ""; // Marcar opción seleccionada
                $options .= "<option value='" . htmlspecialchars($valor->id) . "'$selected>" . htmlspecialchars($valor->txt) . "</option>";
            }
        } else {
            // Manejar caso de error
            $options .= "<option>Error: falta información de la tabla o campos en select</option>";
        }

        return "<select$nameid $params>$options</select>"; // Retornar el select completo
    }

    private function generate_date_input($fld, $val, $edtreg, $nameid)
    {
        // Determinar la clase del input basado en el tipo
        $class = ($edtreg['type'] == "datewoh") ? "inputd datepicker" : "input";
        // Formatear el valor de la fecha
        $value = ($edtreg['type'] == 'datewoh') ? $this->iso2fecha($val, true) : $this->iso2fecha($val, false);

        $tr = "<input$nameid type='text' value='" . htmlspecialchars($value) . "' class='$class' readonly />"; // Input de fecha

        if ($edtreg['type'] == "date") {
            // Botón para cambiar fecha
            $tr .= "&nbsp;<img src='images/cal.png' width='16' height='16' border='0' onClick=\"var cal = new Calendario(document.form1.$fld,'dd/mm/yyyy hh:ii:ss'); cal = null;\" title='Cambiar Fecha' style='cursor:pointer' />";
        } else {
            // Botón para anular fecha
            $tr .= "&nbsp;<button type='button' onclick='blanquearFecha(\"$fld\")'>Anular fecha</button> ";
        }

        return $tr; // Retornar el HTML generado para el input de fecha
    }


    /**
     * Toma string en formato fecha (dd/mm/yyyy HH:ii:ss) y lo transforma en iso (yyyy-mm-dd HH:ii:ss)
     * @param string $fecha
     * @return string
     */
    function iso2fecha($dia, $withhours = true)
    {
        global $txtlng, $txtlngfile, $debug;
        if (empty($dia))
            return '';

        // Validar formato de fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $dia)) {
            return 'Error: formato de fecha inválido';
        }

        $tr = substr($dia, 8, 2);
        $tr .= "/" . substr($dia, 5, 2);
        $tr .= "/" . substr($dia, 0, 4);
        if ($withhours)
            $tr .= substr($dia, 10, 9);
        return $tr;
    }

    /**
     * Muestra un registro
     * @param mixed $reg Registro a mostrar (array u objeto)
     * @param array $shwreg Array que define qué campos mostrar
     * @return string HTML de la tabla con los datos del registro
     */
    public function show_reg($reg, $shwreg = array())
    {
        global $txtlng, $txtlngfile, $debug, $inicio;

        // Verificar que $reg sea un array o un objeto
        if (!is_array($reg) && !is_object($reg)) {
            return "Error: el registro debe ser un array o un objeto.";
        }

        // Si es un objeto, convertir a array
        if (is_object($reg)) {
            $reg = (array) $reg;
        }

        // Inicializar título
        $this->title = "";

        // Loop para mostrar el registro
        $html = '';
        foreach ($reg as $fld => $val) {
            // Comprobar si el campo debe ser mostrado
            if (isset($shwreg[$fld]) && $shwreg[$fld]) {
                // Registrar el tiempo de procesamiento
                $tiempos[] = __FILE__ . " " . __LINE__ . " show_reg $fld, segundos: " . (time() - $inicio);

                // Construir la fila de la tabla
                $tr = "<tr><td>" . htmlspecialchars($fld) . "</td><td>" . htmlspecialchars($val) . "</td></tr>";
                $html .= $tr;
            }
        }

        // Si el debug está activado, imprimir la información de depuración
        if ($debug) {
            $this->prr($shwreg, '$shwreg'); // Se puede ajustar según las necesidades de depuración
        }

        return $html;
    }
    /**
     * Renderiza un registro para editar o mostrar.
     * @param mixed $reg Registro a mostrar o editar (array u objeto)
     * @param array $fields Array que define qué campos mostrar o editar
     * @param bool $isEditing Indica si se debe renderizar como formulario de edición
     * @return string HTML del registro
     */
    public function render_reg($reg, $fields = array(), $isEditing = false)
    {
        global $txtlng, $txtlngfile, $debug, $inicio;

        // Verificar que $reg sea un array o un objeto
        if (!is_array($reg) && !is_object($reg)) {
            return "Error: el registro debe ser un array o un objeto.";
        }

        // Si es un objeto, convertir a array
        if (is_object($reg)) {
            $reg = (array) $reg;
        }
        $classTR = '';
        $classTD = '';
        // Inicializar HTML
        $html = '<table class="table table-bordered fadeIn">';

        foreach ($fields as $field => $edtreg) {
            // Establecer valor por defecto si no hay valor en $reg
            $value = isset($reg[$field]) ? $reg[$field] : '';

            // Generar input
            $inputHtml = $this->generate_input($field, $value, $edtreg, $this->get_name_id($field, true, true));
            $html .= "<tr class={$classTR}>";
            $html .= "<td class={$classTD}>{$edtreg['titulo']}</td>";
            $html .= "<td class={$classTD}>$inputHtml</td>";
            $html .= "</tr>";
        }

        $html .= '</table>';

        return $html; // Retornar el HTML generado
    }





    public function prr1($var, $boolean)
    {
        echo "<pre>";
        echo print_r($var, $boolean);
        echo __FILE__ . ' ' . __LINE__ . "\n";
        echo "</pre>";

    }
    public function parr($var)
    {
        echo "<pre>";
        echo print_r($var, true); // Imprime la variable en formato legible
        $backtrace = debug_backtrace(); // Obtiene información sobre la pila de llamadas
        if (isset($backtrace[0])) {
            // Muestra el archivo y la línea de donde se llamó a la función
            echo "" . $backtrace[0]['file'] . ' línea ' . $backtrace[0]['line'] . "\n";
        }
        echo "</pre>";
    }


    function generarNombreHexadecimal($longitud)
    {

        // Calcula la cantidad de bytes necesarios
        $bytes = ceil($longitud / 2);

        // Genera los bytes aleatorios y los convierte a hexadecimal
        $nombreHex = bin2hex(random_bytes($bytes));

        // Devuelve solo los primeros 5 o 6 caracteres
        return substr($nombreHex, 0, $longitud);
    }



    function crearCarousel($items, $tipo = 'producto')
    {
        if (empty($items)) {
            return '<p>No hay elementos para mostrar.</p>';
        }

        // Contenedor del carrusel
        $carouselHtml = '<div class="carousel-container">';
        $carouselHtml .= '<div id="carouselExample" class="carousel slide" data-ride="carousel">';
        $carouselHtml .= '<div class="carousel-inner-carousel">';

        // Controla el índice y la clase activa
        $activeClass = 'active';

        // Generar las tarjetas
        foreach ($items as $index => $item) {
            // Comienza un nuevo carousel-item
            if ($index % 3 === 0) {
                if ($index > 0) {
                    $carouselHtml .= '</div>'; // Cierra la fila anterior
                }
                $carouselHtml .= '<div class="carousel-item-carousel ' . $activeClass . '">';
                $activeClass = ''; // Quita la clase activa después del primer elemento
            }

            // Tarjeta de producto
            if ($tipo === 'producto') {
                $carouselHtml .= "
                    <div class='card-carousel'>
                        <img src='{$item['producto_imagen1']}' class='card-img-top' alt='{$item['nombre']}'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$item['nombre']}</h5>
                            <p class='card-text'>{$item['descripcion']}</p>
                            <p class='card-price'>Precio: $ {$item['precio']}</p>
                            <button class='btn btn-primary ver-detalles' data-id='{$item['id']}'>Ver detalles</button>
                        </div>
                    </div>";
            } else {
                // Si solo quieres mostrar imágenes
                $carouselHtml .= "
                    <div class='card-carousel'>
                        <img src='{$item['imagen']}' class='card-img-top' alt='Imagen'>
                    </div>";
            }

            // Cierra el último carousel-item si es necesario
            if ($index === count($items) - 1) {
                $carouselHtml .= '</div>'; // Cierra el carousel-item final
            }
        }

        $carouselHtml .= '</div>'; // Cierra el carousel-inner
        $carouselHtml .= '
            <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>'; // Cierra el carousel

        $carouselHtml .= '</div>'; // Cierra el contenedor del carrusel

        return $carouselHtml;
    }


    // el que sigue es el cierre de clase
}

