<?php

// Configurar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Asegúrate de iniciar la sesión

// // Verificar si la sesión está iniciada
// if (!isset($_SESSION['sesionIniciada']) || $_SESSION['sesionIniciada'] !== true) {
//     // Redirigir al usuario a la página de login si no ha iniciado sesión
//     header('Location: https://panel.casasantoni.com.ar/login.php');
//     exit();
// }
// Prevenir el almacenamiento en caché
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proteger en navegadores



// Modo de depuración
define('DEBUG_MODE', false);
$debug = DEBUG_MODE;

// Conexión a PostgreSQL
require '../../header.panel.php';

include '../../classes/grl.class.php'; // Luego incluye CLI

// Inicializa la conexión
$grl = new GRL($conexion);

if (!$conexion) {
    die('Error en la conexión a la base de datos.'); // Considerar usar una función de log para guardar errores
}
?>
<div style="text-align: center;">
    <button type="button" class="btn btn-primary" id="createCateBtn">Crear Nueva Categoria</button>
</div>
<br>
<?php
echo $grl->categorias_gets_all();

?>

<!-- Modal -->
<div class="modal fade" id="editCateModal" tabindex="-1" role="dialog" aria-labelledby="editCategoriaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoriaModalLabel">Editar Categoria</h5> <!-- Título dinámico -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editCateForm">
                    <div class="form-group" id="idPlanGroup">
                        <label for="idCat">Id Plan</label>
                        <input type="text" class="form-control" id="idCat" name="id" readonly />
                    </div>
                    <div class="form-group">
                        <label for="nameCat">Nombre Categoría</label>
                        <input type="text" class="form-control" id="nameCat" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="categoriaDescripcion">Descripción</label>
                        <input class="form-control" id="categoriaDescripcion" name="descripcion" required></input>
                    </div>
                    <div class="form-group">
                        <label for="condicion">Disponible</label>
                        <input type="checkbox" id="condicion" name="condicion" />
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../../../js/categorias.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<?php include '../../footer.php'; ?>