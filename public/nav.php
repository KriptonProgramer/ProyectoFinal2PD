<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Bar</title>
    <style>
        /* Estilos básicos del nav */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #1f3e75;
            /* Color de fondo */
            overflow: hidden;
        }

        nav ul {
            list-style: none;
            /* Quitar viñetas */
            margin: 0;
            padding: 0;
            display: flex;
            /* Para mostrar los enlaces en una fila */
        }

        nav ul li {
            flex: 1;
            /* Todos los enlaces ocupan el mismo espacio */
        }

        nav ul li a {
            display: block;
            padding: 14px 20px;
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        nav ul li a:hover {
            background-color: #0770fd;
            /* Efecto hover */
        }

        /* Responsive: ajusta para pantallas más pequeñas */
        @media (max-width: 600px) {
            nav ul {
                flex-direction: column;
            }
        }
    </style>
</head>

<script>
    const urls = 'http://panel.casasantoni.com.ar';
</script>
<script src="../../js/login.js"></script>

<body>

    <nav>
        <ul>
            <li><a href="../categorias/categorias.php">Categorías</a>
            </li>
            <li><a
                    href="../subcategorias/subcategorias.php">Subcategorías</a>
            </li>
            <li><a href="../productos/productos.php">Productos</a>
            </li>
            <li><a id="btnCerrarSesion" class="btn1">Cerrar Sesión</a></li>
        </ul>
    </nav>

</body>

</html>