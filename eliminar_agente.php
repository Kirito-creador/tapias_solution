<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    die("Acceso denegado.");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    
    $sql = "UPDATE usuario SET activo = 0 WHERE id_usuario = $id";
    
    if (mysqli_query($conexion, $sql)) {
        header("Location: agentes.php?mensaje=eliminado");
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>