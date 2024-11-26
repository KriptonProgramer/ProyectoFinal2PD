$(document).ready(function() {
    // Asigna el evento de clic a los íconos de edición
    $(document).on('click', '.edit-icon', function(e) {
        e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
        var id = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id
        $('#id').val(id); // Establecer el ID en el campo oculto
        edit(id); // Llama a la función editPlan con el ID del plan
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
        var id = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id
        $('#id').val(id); // Establecer el ID en el campo oculto// Establecer el ID en el campo oculto

       
    });
// Asigna el evento de clic para eliminar un plan
$(document).on('click', '.delete-icon', function(e) {
    e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
    var id = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id

    // Confirmar eliminación con SweetAlert
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Una vez eliminado, no podrás recuperar esta categoria.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCate(id); // Llama a la función deleteCate
        } else {
            Swal.fire("La categoria no ha sido eliminado."); // Mensaje de cancelación opcional
        }
    });
    
});

    // Asigna el evento de clic para crear un nuevo Cate
    $('#createSubCateBtn').on('click', function() {
        createNewCate(); // Llama a la función que abre el modal para crear un nuevo Cate
    });
});

// Función para crear un nuevo Cate (abre el modal vacío)
function createNewCate() {
    // Limpia los campos del formulario
    $('#editSubCateForm').trigger('reset');
    $('#editSubCateModal').modal('show');
    
    
    // Asegurarse de que el formulario no esté asociado a ningún Cate existente
    $('#editSubCateForm').removeData('id');
}

function deleteCate(id) {
    $.ajax({
        url: `subcategorias.pro.php?id=${id}`,
        type: 'DELETE',
        success: function(response) {
            console.log('Categoria eliminada:', response);
            Swal.fire("¡Eliminada!", "La subcategoria ha sido eliminada correctamente.", "success")
                .then(() => {
                    // Recargar la página para mostrar los cambios
                    location.reload();
                });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error al eliminar el plan:', textStatus, errorThrown);
            swal("Error", "Ocurrió un error al intentar eliminar el plan.", "error");
        }
    });
}
// Función para editar el plan
function edit(id) {
    console.log('Editing category with ID:', id);
    
    $.ajax({
        url: 'subcategorias.pro.php', // Endpoint correcto
        type: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            console.log('Datos de la subcategoria:', data);
            
            // Rellenar el formulario con los datos existentes
            $('#id').val(data.id);
            $('#name').val(data.nombre); // Asegúrate de que esto esté en el JSON
            $('#categoriaSelect').val(data.categoria_id); // Asegúrate de que esto esté en el JSON
            
            // Abrir el modal
            $('#editSubCateModal').modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud:', textStatus, errorThrown);
        }
    });
}

// Manejar el envío del formulario para creación o edición
$('#editSubCateForm').on('submit', function(event) {
    event.preventDefault(); // Prevenir el envío normal del formulario

    const id = $('#id').val(); // Obtener el ID del plan desde el campo de entrada
    const formData = $(this).serializeArray(); // Obtener los datos del formulario en un formato de array

    // Convertir a un objeto JSON
    let data = {};
    formData.forEach(item => {
        data[item.name] = item.value;
    });

    // Verificar si estamos creando un nuevo plan o editando uno existente
    const requestType = id ? 'POST' : 'POST'; // Siempre es POST en este caso
    const url = id ? `subcategorias.pro.php?id=${id}` : 'subcategorias.pro.php'; // Cambia el endpoint en función de si estamos creando o editando

    // Enviar la solicitud AJAX con JSON
    $.ajax({
        url: url,
        type: requestType,
        contentType: 'application/json', // Indica que el contenido es JSON
        data: JSON.stringify(data), // Convertir los datos a JSON
        success: function (response) {
            console.log('Categoria actualizada o creada:', response);
            $('#editSubCateModal').modal('hide');
            if(url === 'subcategorias.pro.php'){
            Swal.fire("¡Éxito!", "La categoria ha sido creada correctamente.", "success")
                .then(() => {
                    // Recargar la página para mostrar los cambios
                    location.reload();
                });
        } else{

                Swal.fire("¡Éxito!", "La categoria ha sido actualizada correctamente.", "success")
                .then(() => {
                    // Recargar la página para mostrar los cambios
                    location.reload();
                });
            }

            // Aquí puedes actualizar la lista o la vista de los categorias
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error al actualizar o crear el plan:', textStatus, errorThrown);
            swal("Error", "Ocurrió un error al intentar actualizar el plan.", "error");

        }
    });
    
});

