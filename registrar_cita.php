<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_agente = $_SESSION['id_usuario'];
    $id_cliente = intval($_POST['id_cliente']);
    $id_propiedad = intval($_POST['id_propiedad']);
    $fecha_cita = $_POST['fecha_cita'];
    $hora_inicio = $_POST['hora_inicio'];
    $observaciones = mysqli_real_escape_string($conexion, $_POST['observaciones']);
    
    // Calcular hora de fin (2 horas después)
    $hora_fin = date('H:i:s', strtotime($hora_inicio . ' +2 hours'));
    
    // El estado inicial es 'Pendiente' (asumiendo que el id 1 es Pendiente)
    $id_estado_cita = 1;

    // Insertar la cita
    $sql = "INSERT INTO cita (fecha_cita, hora_inicio, hora_fin, observaciones, id_cliente, id_propiedad, id_agente, id_estado_cita) 
            VALUES ('$fecha_cita', '$hora_inicio', '$hora_fin', '$observaciones', $id_cliente, $id_propiedad, $id_agente, $id_estado_cita)";
    
    if (mysqli_query($conexion, $sql)) {
        // Redirigir a la lista de citas con un mensaje de éxito
        header("Location: citas_agente.php?mensaje=exito");
        exit();
    } else {
        echo "Error al registrar la cita: " . mysqli_error($conexion);
    }
} else {
    header("Location: agendar_cita.php");
    exit();
}
?>