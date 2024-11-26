$(document).ready(function() {
    // Manejador de evento para el formulario de inicio de sesión
    $('#loginForm').submit(function(event) {
        // Evitar el envío del formulario por defecto
        event.preventDefault();

        // Obtener los valores del formulario
        var username = $('#username').val();
        var password = $('#password').val();

        // Validación simple
        if (!username || !password) {
            alert('Por favor, completa ambos campos.');
            return;
        }

        // Enviar el formulario
        this.submit(); // Enviar el formulario al servidor
    });

    // Evento de clic en el botón de cierre de sesión
    $('#btnCerrarSesion').click(function() {
        // Redirigir a la página de cierre de sesión
        window.location.href = 'https://panel.casasantoni.com.ar/logout.php';
    });
    
});
