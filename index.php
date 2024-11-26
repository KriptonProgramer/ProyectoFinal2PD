<?php
// index.php
$m = 0;

error_reporting(E_ALL);
ini_set('display_errors', 1);


include 'public/header.web.php';

include 'public/classes/web.class.php';

if (class_exists('STC')) {
    // Si la clase ya está definida, puedes intentar descargar el archivo anterior si es necesario (en proyectos grandes)
    // Opcional: Puedes realizar aquí alguna acción de "limpieza" si es necesario
    unset($stc);
}

// Si la clase no existe o fue limpiada, se carga la nueva clase
if (!class_exists('STC')) {
    require_once 'public/classes/stcweb.class.php';
}

$i = 0;
$web = new WEB($conexion);

// Cargar productos desde la base de datos
$productos = $web->datosOrdenados('products');
include 'public/config/Conexion.php';
$logo = "/public/assets/img/logo1.png";
// $logo = EMPRESA_LOGO;
$url = 'https://panel.casasantoni.com.ar'; // Define la URL base   



?>

<!--<div class="logoIdx">-->
<!--    <img src='<?php echo "public/assets/img/" . $logo ?> ' alt="logoEmpresa">-->
<!--</div>-->
<!-- <div class="logoIdx">
<!--    <img style="width: 100%; height: 650px; object-fit: cover;" src=<?php echo "/assets/img/" . $logo ?>
<!--        alt="Logo de la empresa">-->
<!--</div> -->
<?php include 'public/search.php'; ?>


<div id="searchResults"></div> <!-- Contenedor para mostrar los resultados -->
<div class="main-content">
    <!--<aside class="asideLeft">-->
    <!--    <h3>Publicidad</h3>-->
    <!--    <div id="imageCarousel" class="carousel slide" data-ride="carousel" data-interval="5000">-->
    <!--            <div class="carousel-inner">-->
    <?php
    // $directory = 'public/assets/img/'; // Ruta local
    // // $directory = dirname(__DIR__, 2) . 'public/assets/img/'; // Ruta local
    // $images = glob($directory . "/*.{jpg,png,gif,jpeg}", GLOB_BRACE); // Obtener todas las imágenes
    


    // // Verifica que hay imágenes
    // if (empty($images)) {
    //     echo "No hay imágenes disponibles.";
    // } else {
    //     $active = true; // Para marcar la primera imagen como activa
    
    //     foreach ($images as $image) {
    //         // Extrae solo el nombre del archivo para la URL
    //         $imageName = basename($image);
    //         echo '<div class="carousel-item' . ($active ? ' active' : '') . '">';
    //         echo '<img src="public/assets/img/' . $imageName . '" class="d-block w-100" alt="Publicidad">';
    //         echo '</div>';
    //         $active = false; // Solo la primera imagen debe ser activa
    //     }
    // }
    ?>
    <!--        </div>-->
    <!--    </div>-->

    <!--</aside>-->



    <div class="section">
        <div class="container" id="product-container">
            <div class="row card-container">
                <?php
                // Mostrar productos cargados desde la base de datos
                foreach ($productos as $producto) {
                    echo "<div class='col-md-3 mb-3'>"; // Cambiar a col-md-3 para 4 productos por fila
                    echo $web->cardProducto22($producto);
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        <!--<div id="loading" style="display: none;">-->
        <!--    Cargando más productos...-->
        <!--</div>-->
        <script>
            // Funcion para hacer que se muestre o no la descripcion total
            document.addEventListener('DOMContentLoaded', function () {
                const verMasButtons = document.querySelectorAll('.ver-mas');

                verMasButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const id = this.getAttribute('data-id');
                        const descripcionCompleta = document.getElementById(`descripcion-completa-${id}`);
                        const descripcion = document.getElementById(`descripcion-${id}`);

                        if (descripcionCompleta.style.display === 'none') {
                            descripcionCompleta.style.display = 'inline';
                            this.textContent = 'Ver menos';
                        } else {
                            descripcionCompleta.style.display = 'none';
                            this.textContent = 'Ver más';
                        }
                    });
                });
            });
        </script>


    </div>


</div>
</div>

<!--Pasar productos a JavaScript como un array usando JSON -->
<script>
    const productos = <?php echo json_encode($productos); ?>;
    // console.log(productos); // Verificar si los productos se pasan correctamente
</script>
<!--<hr style="color: red; font-size: 15px;">-->

<div class="main-container">

    <div class="container" id="product-container">
        <div class="row card-container">
            <div id="productDetailsContainer"></div>
        </div>
    </div>
</div>


<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<!-- Font-Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<link rel="stylesheet" href="/pf/public/css/web.css">

<!-- jQuery (la única versión que necesitas) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (asegúrate de cargar Popper.js y Bootstrap en el orden correcto) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- Tu script personalizado -->
<script src="<?php echo '/pf/public/js/script.js'; ?>"></script>

</body>

</html>