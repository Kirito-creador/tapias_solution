<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: notificaciones.php");
    exit();
}

$id_notificacion = intval($_GET['id']);
$id_agente = $_SESSION['id_usuario'];

// Actualizar la notificación específica, asegurando que pertenezca al agente
$sql = "UPDATE notificacion SET leida = TRUE, fecha_lectura = NOW() 
        WHERE id_notificacion = $id_notificacion AND id_usuario = $id_agente";

if (mysqli_query($conexion, $sql)) {
    header("Location: notificaciones.php");
    exit();
} else {
    echo "Error al marcar la notificación: " . mysqli_error($conexion);
}
?>