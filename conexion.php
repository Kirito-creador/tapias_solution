<<<<<<< HEAD

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
=======
>>>>>>> 73e7b6adbb3344fe72f7a20e5999f4cb1fe82bea
