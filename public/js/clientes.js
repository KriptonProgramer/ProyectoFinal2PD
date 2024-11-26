document.addEventListener("DOMContentLoaded", function() {
    // Obtener el botón por su ID
    var toggleButton = document.getElementById('toggleButton');

    // Verificar si el botón existe antes de añadir el listener
    if (toggleButton) {
        // Añadir un evento 'click' al botón para mostrar/ocultar el formulario
        toggleButton.addEventListener('click', toggleForm);
    } else {
        console.error('Botón con id "toggleButton" no encontrado');
    }

    // Función para mostrar/ocultar el formulario
    function toggleForm() {
        // Obtener el formulario por su ID
        var form = document.getElementById('tablaForm');

        // Verificar si el formulario existe
        if (form) {
            // Alternar entre mostrar y ocultar el formulario
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block'; // Mostrar el formulario
            } else {
                form.style.display = 'none'; // Ocultar el formulario
            }
        } else {
            // Mostrar un error si no se encuentra el formulario
            console.error('Elemento con id "tablaForm" no encontrado');
        }
    }


       // Agregar evento para los iconos de editar
       document.querySelectorAll('.edit-icon').forEach(function(icon) {
        icon.addEventListener('click', function(event) {
            event.preventDefault(); // Evitar que se siga el enlace
            var clientId = this.getAttribute('data-id'); // Obtener el ID del cliente
            loadClientData(clientId); // Cargar los datos del cliente
        });
    });

    function loadClientData(clientId) {
        console.log('Client ID:', clientId); // Para verificar el ID del cliente
        fetch(`clientes.pro.php?id=${clientId}`)
    .then(response => {
        // Asegúrate de que la respuesta sea JSON
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.statusText);
        }
        console.log(response)
        return response.json();
    })
    .then(data => {
        console.log('Data:', data);
        if (data.error) {
            console.error('Error en datos:', data.error);
            return;
        }
        fillForm(data);
        toggleForm();
    })
    .catch(error => console.error('Error:', error));

    }
    

    function fillForm(data) {
        document.getElementById('clientId').value = data.id || '';
        document.getElementById('clientCodigo').value = data.codigo || '';
        document.getElementById('clientRsocial').value = data.rsocial || '';
        document.getElementById('clientContacto').value = data.contacto || '';
        document.getElementById('clientDireccion').value = data.direccion || '';
        document.getElementById('clientLocalidad').value = data.localidad || '';
        document.getElementById('clientProvincia').value = data.provincia || '';
        document.getElementById('clientCpostal').value = data.cpostal || '';
        document.getElementById('clientDocumento').value = data.documento || '';
        document.getElementById('clientTelefonos').value = data.telefonos || '';
        document.getElementById('clientIva').value = data.iva || '';
        document.getElementById('clientVendedor').value = data.vendedor || '';
        document.getElementById('clientEmail').value = data.email || '';
        document.getElementById('clientFechaAlta').value = data.fechaalta || '';
        document.getElementById('clientDescuento').value = data.descuento || '';
    }

    function createCliente() {
        fetch('clientes.pro.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data) // data debe ser un objeto JavaScript con la información del cliente
        })
        .then(response => response.json())
        .then(result => {
            console.log('Resultado:', result);
        })
        .catch(error => console.error('Error:', error));
        
    }

    function createClient() {
        fetch(`clientes.pro.php?id=${clientId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedData) // updatedData debe contener los datos actualizados del cliente
        })
        .then(response => response.json())
        .then(result => {
            console.log('Resultado:', result);
        })
        .catch(error => console.error('Error:', error));
        
    }
    function deleste() {
        fetch(`clientes.pro.php?id=${clientId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updatedData) // updatedData debe contener los datos actualizados del cliente
        })
        .then(response => response.json())
        .then(result => {
            console.log('Resultado:', result);
        })
        .catch(error => console.error('Error:', error));
        
    }

    //Lo que sigue es el  Cierre del document
});
