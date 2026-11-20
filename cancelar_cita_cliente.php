<?php
session_start();
include("conexion.php");
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Cliente') { header("Location: login.php"); exit(); }
if (isset($_GET['id'])) {
    $id_cita = intval($_GET['id']);
    $id_usuario = $_SESSION['id_usuario'];
    $sql_cli = "SELECT id_cliente FROM cliente WHERE id_usuario = $id_usuario";
    $res_cli = mysqli_query($conexion, $sql_cli);
    $cli = mysqli_fetch_assoc($res_cli);
    $id_cliente = $cli['id_cliente'];
    
    $sql = "UPDATE cita SET id_estado_cita = 4 WHERE id_cita = $id_cita AND id_cliente = $id_cliente AND id_estado_cita = 1";
    mysqli_query($conexion, $sql);
}
header("Location: mis_citas_cliente.php");
exit();
?>