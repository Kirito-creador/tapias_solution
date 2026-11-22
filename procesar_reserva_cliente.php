<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Cliente') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id_usuario'];
    $id_propiedad = intval($_POST['id_propiedad']);
    $fecha_cita = $_POST['fecha_cita'];
    $hora_inicio = $_POST['hora_inicio'];
    $observaciones = mysqli_real_escape_string($conexion, $_POST['observaciones']);

    $sql_cliente = "SELECT id_cliente FROM cliente WHERE id_usuario = $id_usuario";
    $res_cliente = mysqli_query($conexion, $sql_cliente);
    $cliente = mysqli_fetch_assoc($res_cliente);

    if (!$cliente) {
        die("No se encontró tu perfil de cliente.");
    }
    $id_cliente = $cliente['id_cliente'];

    $sql_prop = "SELECT id_agente FROM propiedad WHERE id_propiedad = $id_propiedad";
    $res_prop = mysqli_query($conexion, $sql_prop);
    $prop = mysqli_fetch_assoc($res_prop);
    if (!$prop) {
        die("Propiedad no encontrada.");
    }
    $id_agente = $prop['id_agente'];

    $hora_fin = date('H:i:s', strtotime($hora_inicio . ' +2 hours'));
    $id_estado_cita = 1;

    $sql = "INSERT INTO cita (fecha_cita, hora_inicio, hora_fin, observaciones, id_cliente, id_propiedad, id_agente, id_estado_cita) 
            VALUES ('$fecha_cita', '$hora_inicio', '$hora_fin', '$observaciones', $id_cliente, $id_propiedad, $id_agente, $id_estado_cita)";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "Tu visita para el $fecha_cita a las $hora_inicio ha sido solicitada. El agente la confirmará pronto.";
        $sql_noti = "INSERT INTO notificacion (tipo, mensaje, leida, id_usuario) 
                     VALUES ('Cita', '$mensaje', FALSE, $id_usuario)";
        mysqli_query($conexion, $sql_noti);

        $mensaje_agente = "Nueva solicitud de visita para el $fecha_cita a las $hora_inicio.";
        $sql_noti_agente = "INSERT INTO notificacion (tipo, mensaje, leida, id_usuario) 
                            VALUES ('Cita', '$mensaje_agente', FALSE, $id_agente)";
        mysqli_query($conexion, $sql_noti_agente);

        header("Location: panel_cliente.php?mensaje=reserva_exitosa");
        exit();
    } else {
        echo "Error al registrar la reserva: " . mysqli_error($conexion);
    }
} else {
    header("Location: propiedades_publicas.php");
    exit();
}
?>