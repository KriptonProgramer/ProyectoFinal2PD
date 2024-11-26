<?php
// Configurar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer parámetros de cookie de sesión
session_start(); // Asegúrate de iniciar la sesión

// Mostrar el mensaje de error si existe
if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']); // Limpiar el mensaje después de mostrarlo
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Verificar las credenciales
if ($username === 'admin' && $password === 'tito' ) {
    $_SESSION['sesionIniciada'] = true;
    echo 'Sesión iniciada: ' . $_SESSION['sesionIniciada']; // Verifica si se establece
    header('Location: https://panel.casasantoni.com.ar/Components/productos/productos.php');
    exit();
}

 else {
        $_SESSION['error'] = 'Nombre de usuario o contraseña incorrectos';
        header('Location: https://panel.casasantoni.com.ar/login.php'); // Redirigir a login
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SIS | Login</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" href="https://panel.casasantoni.com.ar/assets/img/logo1.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="text-align: center; background-color: #0770fd;">
    <div class="mt-5">
        <img style="width: auto; height: 150px;" src="https://panel.casasantoni.com.ar/assets/img/logo1.png" alt="Logo">
    </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Iniciar Sesión</div>
                    <div class="card-body">
                        <?php if (isset($errorMessage)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $errorMessage; ?>
                            </div>
                        <?php endif; ?>
                        <form id="loginForm" method="POST">
                            <div class="mb-3" style="text-align: center;">
                                <label for="username" class="form-label">Nombre de usuario</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3" style="text-align: center;">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
