<?php
// Inicializa la conexión
$stc = new STC($conexion); // Suponiendo que $conexion ya está definida

// Obtener las categorías y subcategorías desde la base de datos
$categorias = $stc->gets_all_categorias();
$subcategorias = $stc->gets_subcategorias();

// Crear un arreglo que relaciona las categorías con sus subcategorías
$categoriasConSubcategorias = [];
foreach ($categorias as $categoria) {
    $categoriasConSubcategorias[$categoria['id']] = [
        'nombre' => $categoria['categoryName'],
        'subcategorias' => array_filter($subcategorias, function ($subcategoria) use ($categoria) {
            return $subcategoria['categoryid'] === $categoria['id']; // Relaciona las subcategorías con la categoría
        })
    ];
}
// $stc->parr($subcategorias);

// Obtener los parámetros de búsqueda
$searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
$categoryId = isset($_GET['cid']) ? $_GET['cid'] : '';
$subcategoryId = isset($_GET['subcategoryId']) ? $_GET['subcategoryId'] : '';
?>
<form id="searchForm" class="searchForm" method="GET" action="">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <input type="text" id="searchQuery" name="searchQuery" placeholder="Buscar productos..." />
        <input type="hidden" id="selectedSubcategory" name="subcategoryId" value="" />
        <!-- Campo oculto para la subcategoría -->
        <button class="myButton" type="submit"><i class="fa fa-search"></i></button>

        <!-- Barra de navegación -->
        <div class="container-fluid">
            <a class="navbar-brand" href="#"></a>

            <!-- Barra de navegación con categorías -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Menú de Categorías -->
                    <?php foreach ($categoriasConSubcategorias as $categoriaId => $categoria): ?>
                        <li class="nav-item" style="z-index: 1; position: relative;">
                            <!-- Categoría -->
                            <a class="nav-link" href="#" id="categoria-<?php echo $categoriaId; ?>"
                                style="cursor: pointer;">
                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                            </a>

                            <!-- Subcategorías (ocultas al principio) -->
                            <ul class="subcategorias" id="subcategorias-<?php echo $categoriaId; ?>"
                                style="display: none; position: absolute; top: 100%; left: 0; background-color: #fff; border: 1px solid #ccc; padding: 10px; list-style-type: none; margin: 0;">
                                <?php if (!empty($categoria['subcategorias'])): ?>
                                    <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
                                        <li>
                                            <a class="dropdown-item categoria-link" href="#"
                                                data-categoria-id="<?php echo $categoriaId; ?>"
                                                data-subcategoria-id="<?php echo $subcategoria['id']; ?>">
                                                <?php echo htmlspecialchars($subcategoria['subcategory']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><a class="dropdown-item disabled" href="#">Sin subcategorías</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
</form>

<!-- Agregar Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script JavaScript para manejar el clic en categorías y subcategorías -->
<script>
    // Obtenemos todos los elementos de categoría
    // Obtenemos todos los elementos de categoría
    const categoriaLinks = document.querySelectorAll('.nav-link');

    // Recorremos cada uno para agregar el evento de clic
    categoriaLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Evita la navegación al hacer clic

            const categoriaId = this.id.split('-')[1]; // Obtener el ID de la categoría

            // Primero ocultamos todas las subcategorías
            const allSubcategorias = document.querySelectorAll('.subcategorias');
            allSubcategorias.forEach(subcat => {
                subcat.style.display = 'none';
            });

            // Buscar el ul correspondiente con las subcategorías de la categoría seleccionada
            const subcategoriasList = document.getElementById('subcategorias-' + categoriaId);

            // Mostrar las subcategorías de la categoría seleccionada
            if (subcategoriasList.style.display === 'none' || subcategoriasList.style.display === '') {
                subcategoriasList.style.display = 'block'; // Mostrar subcategorías
            } else {
                subcategoriasList.style.display = 'none'; // Ocultar subcategorías si ya estaban visibles
            }
        });
    });

    // Cerrar cualquier subcategoría si se hace clic fuera de los menús
    document.addEventListener('click', function (event) {
        const isClickInsideMenu = event.target.closest('.nav-item'); // Verifica si el clic está dentro de un elemento de categoría

        if (!isClickInsideMenu) {
            // Si el clic está fuera del menú, ocultar todas las subcategorías
            const allSubcategorias = document.querySelectorAll('.subcategorias');
            allSubcategorias.forEach(subcat => {
                subcat.style.display = 'none';
            });
        }
    });

    // Obtenemos todos los enlaces de las subcategorías

    // Obtenemos todos los enlaces de subcategorías
    const subcategoriaLinks = document.querySelectorAll('.categoria-link');

    // Recorremos cada subcategoría para agregar el evento de clic
    subcategoriaLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Evita la navegación por defecto

            const categoriaId = this.dataset.categoriaId; // ID de la categoría
            const subcategoriaId = this.dataset.subcategoriaId; // ID de la subcategoría

            // Realizamos la llamada AJAX para obtener los productos usando el método POST
            fetch('public/Components/productos/productos.ajax.php', {
                method: 'POST',  // Usamos POST en lugar de GET
                headers: {
                    'Content-Type': 'application/json',  // Aseguramos que el cuerpo de la solicitud sea JSON
                },
                body: JSON.stringify({
                    modo: 'busqueda',  // El modo de búsqueda
                    categoriaId: categoriaId,
                    subcategoriaId: subcategoriaId
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al cargar los productos');
                    }
                    return response.text();  // Esperamos respuesta en formato HTML
                })
                .then(data => {
                    console.log(data); // Verifica lo que devuelve el backend
                    const productosContainer = document.getElementById('product-container');
                    if (!productosContainer) {
                        console.error('Contenedor de productos no encontrado.');
                        return;
                    }

                    productosContainer.innerHTML = data;  // Inserta el HTML recibido del servidor
                })
                .catch(error => {
                    console.error('Error al cargar los productos:', error);
                    const productosContainer = document.getElementById('product-container');
                    if (productosContainer) {
                        productosContainer.innerHTML = '<p>Hubo un error al cargar los productos.</p>';
                    } else {
                        console.error('Contenedor de productos no encontrado.');
                    }
                });
        });
    });
</script>



</script>

<style>
    /* Puedes agregar estilos personalizados para la barra de navegación */
    .navbar {
        margin-bottom: 20px;
        /* Agregar un margen debajo de la barra */
    }
</style>