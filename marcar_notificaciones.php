<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

if (isset($_GET['id'])) {
    $id_notificacion = intval($_GET['id']);
    $sql = "UPDATE notificacion SET leida = TRUE, fecha_lectura = NOW() 
            WHERE id_notificacion = $id_notificacion AND id_usuario = $id_usuario";
} else {
    $sql = "UPDATE notificacion SET leida = TRUE, fecha_lectura = NOW() 
            WHERE id_usuario = $id_usuario AND leida = FALSE";
}

if (mysqli_query($conexion, $sql)) {
    if ($_SESSION['rol'] == 'Cliente') {
        header("Location: notificaciones_cliente.php");
    } else {
        header("Location: notificaciones.php");
    }
    exit();
} else {
    echo "Error al marcar las notificaciones: " . mysqli_error($conexion);
}
?>