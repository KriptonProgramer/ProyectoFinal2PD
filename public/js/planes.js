$(document).ready(function() {
    // Asigna el evento de clic a los íconos de edición
    $(document).on('click', '.edit-icon', function(e) {
        e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
        var planId = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id
        $('#planId').val(planId); // Establecer el ID en el campo oculto

        edit(planId); // Llama a la función editPlan con el ID del plan
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
        var planId = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id
        $('#planId').val(planId); // Establecer el ID en el campo oculto

       
    });
// Asigna el evento de clic para eliminar un plan
$(document).on('click', '.delete-icon', function(e) {
    e.preventDefault(); // Evita que el enlace siga el comportamiento predeterminado
    var planId = $(this).data('id'); // Obtiene el ID del plan desde el atributo data-id

    // Confirmar eliminación con SweetAlert
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Una vez eliminado, no podrás recuperar este plan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            deletePlan(planId); // Llama a la función deletePlan
        } else {
            Swal.fire("El plan no ha sido eliminado."); // Mensaje de cancelación opcional
        }
    });
    
});

    // Asigna el evento de clic para crear un nuevo plan
    $('#createPlanBtn').on('click', function() {
        createNewPlan(); // Llama a la función que abre el modal para crear un nuevo plan
    });
});

// Función para crear un nuevo plan (abre el modal vacío)
function createNewPlan() {
    // Limpia los campos del formulario
    $('#editPlanForm').trigger('reset');
    $('#editPlanModal').modal('show');
    
    // Asegurarse de que el formulario no esté asociado a ningún plan existente
    $('#editPlanForm').removeData('planId');
}

function deletePlan(planId) {
    $.ajax({
        url: `planes.pro.php?id=${planId}`,
        type: 'DELETE',
        success: function(response) {
            console.log('Plan eliminado:', response);
            Swal.fire("¡Eliminado!", "El plan ha sido eliminado correctamente.", "success")
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
function edit(planId) {
    console.log('Editing plan with ID:', planId);
    
    $.ajax({
        url: 'planes.pro.php', // Cambia esto al endpoint correcto para obtener los datos del plan
        type: 'GET',
        data: { id: planId },
        dataType: 'json',
        success: function(data) {
            console.log('Datos del plan:', data); // Imprimir los datos del plan en la consola
            
            // Rellenar el formulario con los datos existentes
            $('#planCodigo').val(data.codigo);
            $('#planDescripcion').val(data.descripcion);
            $('#planId').val(data.id);
            $('#planAbono').val(data.abono);
            $('#planMonAbono').val(data.mon_abono);
            $('#planSubida').val(data.subida);
            $('#planBajada').val(data.bajada);
            
            // Abrir el modal
            $('#editPlanModal').modal('show');
            
            // Guardar el ID del plan para usarlo en el envío del formulario
            $('#editPlanForm').data('planId', planId);
           
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud:', textStatus, errorThrown);
        }
    });
}

// Manejar el envío del formulario para creación o edición
$('#editPlanForm').on('submit', function(event) {
    event.preventDefault(); // Prevenir el envío normal del formulario

    const planId = $('#planId').val(); // Obtener el ID del plan desde el campo de entrada
    const formData = $(this).serializeArray(); // Obtener los datos del formulario en un formato de array

    // Convertir a un objeto JSON
    let data = {};
    formData.forEach(item => {
        data[item.name] = item.value;
    });

    // Verificar si estamos creando un nuevo plan o editando uno existente
    const requestType = planId ? 'POST' : 'POST'; // Siempre es POST en este caso
    const url = planId ? `planes.pro.php?id=${planId}` : 'planes.pro.php'; // Cambia el endpoint en función de si estamos creando o editando

    // Enviar la solicitud AJAX con JSON
    $.ajax({
        url: url,
        type: requestType,
        contentType: 'application/json', // Indica que el contenido es JSON
        data: JSON.stringify(data), // Convertir los datos a JSON
        success: function (response) {
            console.log('Plan actualizado o creado:', response);
            $('#editPlanModal').modal('hide');
            if(url === 'planes.pro.php'){
            Swal.fire("¡Éxito!", "El plan ha sido creado correctamente.", "success")
                .then(() => {
                    // Recargar la página para mostrar los cambios
                    location.reload();
                });
        } else{

                Swal.fire("¡Éxito!", "El plan ha sido actualizado correctamente.", "success")
                .then(() => {
                    // Recargar la página para mostrar los cambios
                    location.reload();
                });
            }

            // Aquí puedes actualizar la lista o la vista de los planes
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error al actualizar o crear el plan:', textStatus, errorThrown);
            swal("Error", "Ocurrió un error al intentar actualizar el plan.", "error");

        }
    });
    
});

