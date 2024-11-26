<?php
// $servername = "localhost";
// $username = "root";
// $password = "@Simple123";
// $dbname = "shopping";
include('../public/config/Conexion.php');
// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>