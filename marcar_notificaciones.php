<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

$id_agente = $_SESSION['id_usuario'];

$sql = "UPDATE notificacion SET leida = TRUE, fecha_lectura = NOW() WHERE id_usuario = $id_agente AND leida = FALSE";

if (mysqli_query($conexion, $sql)) {

    header("Location: panel_agente.php");
    exit();
} else {
    echo "Error al marcar las notificaciones: " . mysqli_error($conexion);
}
?>