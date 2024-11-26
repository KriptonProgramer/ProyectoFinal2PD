$(document).ready(function() {
    // Editar producto
    $(document).on('click', '.edit-icon', function(e) {
        e.preventDefault();
        var idProd = $(this).data('id');
        edit(idProd);
    });

    // Eliminar producto
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var idProd = $(this).data('id');
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Una vez eliminado, no podrás recuperar este producto.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "No, cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteProd(idProd);
            } else {
                Swal.fire("El producto no ha sido eliminado.");
            }
        });
    });

    // Crear nuevo producto
    $('#createProdBtn').on('click', function() {
        createNewProd();
    });

    // Envío del formulario
  // Envío del formulario
$('#editProductForm').on('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    const idProd = $('#id').val();
    const url = idProd ? `productos.pro.php?id=${idProd}` : 'productos.pro.php';

    $.ajax({
        url: url,
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        success: function(response) {
            console.log(response);
            
            try {
                const jsonResponse = JSON.parse(response);

                console.log('Producto actualizado o creado:', jsonResponse);
                console.log('Respuesta del servidor:', response); // Agrega esta línea
                if (jsonResponse.status == true) {
                    // Asegúrate de cerrar el modal solo si la respuesta es correcta
                    $('#editProductModal').modal('hide');
                    Swal.fire("¡Éxito!", response.message, "success")
                        .then(() => {
                            // Opcionalmente, puedes hacer una recarga de la página o una actualización del listado
                            location.reload();
                        });
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            } catch (error) {
                console.log(response);
                
                console.error('Error al parsear la respuesta:', error);
                Swal.fire("Error", "Ocurrió un error al procesar la respuesta del servidor.", "error");
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error al actualizar o crear el producto:', textStatus, errorThrown);
            Swal.fire("Error", "Ocurrió un error al intentar actualizar o crear el producto.", "error");
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    CKEDITOR.replace('descripcion', {
        height: 200,
        toolbar: [
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv'] },
            { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'PageBreak', 'SpecialChar'] },
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
        ]
    });
});
});

// Crear nuevo producto
function createNewProd() {
    $('#editProductForm').trigger('reset');
    $('#editProductModal').modal('show');
}

// Eliminar producto
function deleteProd(idProd) {
    $.ajax({
        url: `productos.pro.php?id=${idProd}`,
        type: 'DELETE',
        success: function(response) {
            console.log('Producto eliminado:', response);
            Swal.fire("¡Eliminado!", "El producto ha sido eliminado correctamente.", "success")
                .then(() => location.reload());
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error al eliminar el producto:', textStatus, errorThrown);
            Swal.fire("Error", "Ocurrió un error al intentar eliminar el producto.", "error");
        }
    });
}

// Editar producto
function edit(idProd) {
    console.log('Editing product with ID:', idProd);
    $.ajax({
        url: 'productos.pro.php',
        type: 'GET',
        data: { id: idProd },
        dataType: 'json',
        success: function(data) {
            console.log('Datos del producto:', data);
            $('#id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#categoriaSelect').val(data.categoria_id);
            $('#subcategoriaSelect').val(data.subcategoria_id);
            $('#marca').val(data.marca);
            $('#precio').val(data.precio);
            $('#stock').val(data.stock);
            $('#descripcion').val(data.descripcion);
            $('#condicion').prop('checked', data.condicion == 1);
            $('#editProductModal').modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud:', textStatus, errorThrown);
        }
    });
}
