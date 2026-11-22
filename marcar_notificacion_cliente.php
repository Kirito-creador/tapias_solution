<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Cliente') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "UPDATE notificacion SET leida = TRUE, fecha_lectura = NOW() 
        WHERE id_usuario = $id_usuario AND leida = FALSE";

if (mysqli_query($conexion, $sql)) {
    header("Location: notificaciones_cliente.php");
    exit();
} else {
    echo "Error al marcar las notificaciones: " . mysqli_error($conexion);
}
?>