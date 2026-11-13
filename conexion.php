
<?php
$servidor = "localhost";
$usuario = "root";
$contrasena = ""; 
$basedatos = "tapia_solution";

$conexion = mysqli_connect($servidor, $usuario, $contrasena, $basedatos);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
$conexion->set_charset("utf8");
?>
