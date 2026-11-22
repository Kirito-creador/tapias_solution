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

    $check = mysqli_query($conexion, "SELECT id_cita, id_cliente FROM cita WHERE id_cita = $id_cita AND id_agente = $id_agente");
    
    if (mysqli_num_rows($check) > 0) {
        $cita = mysqli_fetch_assoc($check);
        $id_cliente = $cita['id_cliente'];

        $sql = "UPDATE cita SET id_estado_cita = $nuevo_estado WHERE id_cita = $id_cita";
        mysqli_query($conexion, $sql);

        if ($nuevo_estado == 2) {
            $sql_cli = "SELECT id_usuario FROM cliente WHERE id_cliente = $id_cliente";
            $res_cli = mysqli_query($conexion, $sql_cli);
            $cli = mysqli_fetch_assoc($res_cli);
            
            if ($cli && !empty($cli['id_usuario'])) {
                $id_usuario_cliente = $cli['id_usuario'];
                $mensaje = "Tu visita para la cita #$id_cita ha sido CONFIRMADA por el agente.";
                
                $sql_noti = "INSERT INTO notificacion (tipo, mensaje, leida, id_usuario) 
                             VALUES ('Cita', '$mensaje', FALSE, $id_usuario_cliente)";
                mysqli_query($conexion, $sql_noti);
            }
        }
    }
    
    header("Location: citas_agente.php");
    exit();
}
?>