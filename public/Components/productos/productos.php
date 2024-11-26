<?php

$debug = false;

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

}

session_start(); // Asegúrate de iniciar la sesión

if (!isset($_SESSION['sesionIniciada']) ||$_SESSION['sesionIniciada'] !== true) {
    echo 'Sesión no iniciada. Variables de sesión: ';
    print_r($_SESSION); // Para ver qué hay en $_SESSION
    exit();

    header('Location: https://panel.casasantoni.com.ar/login.php');
    exit();
}



// Conexión a PostgreSQL
require '../../header.panel.php';

include '../../classes/grl.class.php';
include '../../classes/producto.class.php';


// Inicializa la conexión
$grl = new GRL($conexion);
$prod = new PROD($conexion);

if (!$conexion) {
    die('Error en la conexión a la base de datos.');
}
$categorias = $grl->gets_all_categorias();
$subcategorias = $grl->gets_subcategorias();
$productos = $prod->productos_gets_all()
    ?>
<!-- Botón para abrir el modal -->
<div style="text-align: center;">
    <button type="button" class="btn btn-primary" id="createProdBtn" data-toggle="modal"
        data-target="#editProductModal">Crear Nuevo Producto</button>

    <a href="editarProductos.php" class="btn btn-secondary" style="margin-left: 10px;">
        <i class="fas fa-download"></i> Descargar Productos Xlsx
    </a>

    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#importProductModal" style="margin-left: 10px;">
        <i class="fas fa-upload"></i> Importar Productos
    </button>
</div>

<!-- Modal para importar productos -->
<div class="modal fade" id="importProductModal" tabindex="-1" role="dialog" aria-labelledby="importProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProductModalLabel">Importar Productos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="actualizarExel.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Selecciona el archivo Excel:</label>
                        <input type="file" name="file" accept=".xlsx" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Importar Productos</button>
                </form>
            </div>
        </div>
    </div>
</div>

<br>



<!-- Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Editar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    <div class="form-group" id="idProductGroup">
                        <label for="id">ID</label>
                        <input type="text" class="form-control" id="id" name="id" readonly />
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre del Producto</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="categoriaSelect">Selecciona una Categoría</label>
                        <select class="form-control" id="categoriaSelect" name="categoria_id" required>
                            <option value="">Seleccione una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id']; ?>"><?= htmlspecialchars($categoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subcategoriaSelect">Selecciona una Subcategoría</label>
                        <select class="form-control" id="subcategoriaSelect" name="subcategoria_id" required>
                            <option value="">Seleccione una subcategoría</option>
                            <?php foreach ($subcategorias as $subcategoria): ?>
                                <option value="<?= $subcategoria['id']; ?>">
                                    <?= htmlspecialchars($subcategoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="marca">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="producto_imagen1">Imagen 1</label>
                        <input type="file" class="form-control" id="producto_imagen1" name="producto_imagen1"
                            accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="producto_imagen2">Imagen 2</label>
                        <input type="file" class="form-control" id="producto_imagen2" name="producto_imagen2"
                            accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="producto_imagen3">Imagen 3</label>
                        <input type="file" class="form-control" id="producto_imagen3" name="producto_imagen3"
                            accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="condicion">Disponible</label>
                        <input type="checkbox" id="condicion" name="condicion" value="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="../../../js/productos.js"></script>
</body>
<?php
echo $productos;
include '../../footer.php';
?>