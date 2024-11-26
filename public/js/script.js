function showImage(src) {
    // Cambia la imagen principal por la seleccionada
    document.getElementById('detail-img').src = src;
}
// const urls = 'http://panel.casasantoni.com.ar';

$(document).ready(function () {
    const productContainer = $('#product-container');
    let currentPage = 1;
    const productsPerPage = 15;

    // Función para cargar los detalles de un producto
    function loadProductDetails(productId) {
        $.ajax({
            url: 'public/Components/productos/productos.ajax.php',
            method: 'POST',
            data: { id: productId, modo: 'getProducto' },
            success: function (response) {
                productContainer.html(response);
                
                // Listener para el botón de contactar al vendedor
                $('#contactarVendedorBtn').on('click', contactarVendedor);
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar los detalles del producto:', error);
            }
        });
    }

    // Función para buscar productos
    $('#searchForm').on('submit', function (event) {
        event.preventDefault();
        const query = $('#searchQuery').val().trim();

        if (query) {
            $.ajax({
            url: 'public/Components/productos/productos.ajax.php',
                method: 'POST',
                data: { modo: 'buscar', query: encodeURIComponent(query) },
                success: function (data) {
                    $('#product-container').html(data);
                },
                error: function (xhr, status, error) {
                    console.error('Error al buscar productos:', status, error);
                }
            });
        } else {
            location.reload();
            // alert("Por favor ingresa un término de búsqueda válido.");
        }
    });

    // Función para generar el mensaje de WhatsApp basado en el producto actual
    function generarMensajeWhatsApp() {
        const producto = {
            nombre: $('#contactarVendedorBtn').data('title'),
            precio: $('#contactarVendedorBtn').data('price'),
            // descripcion: $('#contactarVendedorBtn').data('descripcion')
        };

        // return `Hola, me gustaría saber más sobre el producto:\n\nProducto: ${producto.nombre}\nPrecio: $${producto.precio}\nDescripción: ${producto.descripcion}\n\n`;
        return `Hola, me gustaría saber más sobre el producto:\n\nProducto: ${producto.nombre}\nPrecio: $${producto.precio}\n\n`;
    }

    // Función para contactar al vendedor
    function contactarVendedor() {
        const numeroVendedor = "5491125469707";
        const mensaje = generarMensajeWhatsApp();
        const urlWhatsApp = `https://api.whatsapp.com/send?phone=${numeroVendedor}&text=${encodeURIComponent(mensaje)}`;
        window.open(urlWhatsApp, "_blank");
    }

    // Función para ocultar o mostrar los asides en dispositivos móviles
    function toggleAsides() {
        if (window.innerWidth < 768) {
            $('.asideLeft, .asideRight').hide();
        } else {
            $('.asideLeft, .asideRight').show();
        }
    }

    toggleAsides();
    $(window).on('resize', toggleAsides);

    // Cargar los detalles del producto si hay uno seleccionado en el almacenamiento local
    const storedProductId = localStorage.getItem('selectedProductId');
    if (storedProductId) {
        loadProductDetails(storedProductId);
    }

    // Manejar el desplazamiento para cargar más productos
    function handleScroll() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 500) {
            currentPage++;
            // loadProducts(currentPage); // Descomentar cuando implementes la carga de productos
        }
    }

    $(window).on('scroll', handleScroll);

    // Escuchar el clic en el botón "Ver detalles"
    $(document).on('click', '.ver-detalles', function () {
        const productId = $(this).data('id');
        localStorage.setItem('selectedProductId', productId);
        loadProductDetails(productId);
    });

    // Controlar el botón de volver a la lista
    productContainer.on('click', '#back-button', function () {
        localStorage.removeItem('selectedProductId');
        location.reload();
    });

    // Cargar los primeros productos al iniciar (descomentar si implementas la función)
});
