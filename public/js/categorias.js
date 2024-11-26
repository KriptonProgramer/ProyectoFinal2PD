$(document).ready(function() {
    // Asigna el evento de clic a los íconos de edición
    $(document).on('click', '.edit-icon', function(e) {
        e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
        var idCat = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id
        $('#idCat').val(idCat); // Establecer el ID en el campo oculto

        edit(idCat); // Llama a la función editPlan con el ID del plan
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
        var idCat = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id
        $('#idCat').val(idCat); // Establecer el ID en el campo oculto// Establecer el ID en el campo oculto

       
    });
// Asigna el evento de clic para eliminar un plan
$(document).on('click', '.delete-icon', function(e) {
    e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
    var idCat = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id

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
            deleteCate(idCat); // Llama a la función deleteCate
        } else {
            Swal.fire("La categoria no ha sido eliminado."); // Mensaje de cancelación opcional
        }
    });
    
});

    // Asigna el evento de clic para crear un nuevo Cate
    $('#createCateBtn').on('click', function() {
        createNewCate(); // Llama a la función que abre el modal para crear un nuevo Cate
    });
});

// Función para crear un nuevo Cate (abre el modal vacío)
function createNewCate() {
    // Limpia los campos del formulario
    $('#editCateForm').trigger('reset');
    $('#editCateModal').modal('show');
    
    
    // Asegurarse de que el formulario no esté asociado a ningún Cate existente
    $('#editCateForm').removeData('idCat');
}

function deleteCate(idCat) {
    $.ajax({
        url: `categorias.pro.php?id=${idCat}`,
        type: 'DELETE',
        success: function(response) {
            console.log('Categoria eliminada:', response);
            Swal.fire("¡Eliminada!", "La categoria ha sido eliminada correctamente.", "success")
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
function edit(idCat) {
    console.log('Editing category with ID:', idCat);
    
    $.ajax({
        url: 'categorias.pro.php', // Cambia esto al endpoint correcto para obtener los datos del plan
        type: 'GET',
        data: { id: idCat },
        dataType: 'json',
        success: function(data) {
            console.log('Datos del plan:', data); // Imprimir los datos del plan en la consola
            
            // Rellenar el formulario con los datos existentes
            $('#idCat').val(data.id);
            $('#categoriaDescripcion').val(data.descripcion);
            $('#nameCat').val(data.nombre);
            
           
            if (data.condicion == 1) {
                $('#condicion').prop('checked', true);
            } else {
                $('#condicion').prop('checked', false);
            }
            
            // Abrir el modal
            $('#editCateModal').modal('show');
            
            // Guardar el ID del plan para usarlo en el envío del formulario
            $('#editCateForm').data('idCat', idCat);
           
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud:', textStatus, errorThrown);
        }
    });
}

// Manejar el envío del formulario para creación o edición
$('#editCateForm').on('submit', function(event) {
    event.preventDefault(); // Prevenir el envío normal del formulario

    const idCat = $('#idCat').val(); // Obtener el ID del plan desde el campo de entrada
    const formData = $(this).serializeArray(); // Obtener los datos del formulario en un formato de array

    // Convertir a un objeto JSON
    let data = {};
    formData.forEach(item => {
        data[item.name] = item.value;
    });

    // Verificar si estamos creando un nuevo plan o editando uno existente
    const requestType = idCat ? 'POST' : 'POST'; // Siempre es POST en este caso
    const url = idCat ? `categorias.pro.php?id=${idCat}` : 'categorias.pro.php'; // Cambia el endpoint en función de si estamos creando o editando

    // Enviar la solicitud AJAX con JSON
    $.ajax({
        url: url,
        type: requestType,
        contentType: 'application/json', // Indica que el contenido es JSON
        data: JSON.stringify(data), // Convertir los datos a JSON
        success: function (response) {
            console.log('Categoria actualizada o creada:', response);
            $('#editCateModal').modal('hide');
            if(url === 'categorias.pro.php'){
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

