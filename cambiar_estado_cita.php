<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id_cita = intval($_GET['id']);
    $nuevo_estado = intval($_GET['estado']);
    $id_agente = $_SESSION['id_usuario'];

    // Validar que la cita pertenezca a este agente
    $check = mysqli_query($conexion, "SELECT id_cita FROM cita WHERE id_cita = $id_cita AND id_agente = $id_agente");
    
    if (mysqli_num_rows($check) > 0) {
        // Actualizar el estado
        $sql = "UPDATE cita SET id_estado_cita = $nuevo_estado WHERE id_cita = $id_cita";
        mysqli_query($conexion, $sql);
    }
    
    // Redirigir de vuelta a la lista de citas
    header("Location: citas_agente.php");
    exit();
}
?>