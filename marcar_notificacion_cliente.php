<?php
session_start();
include("conexion.php");
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Cliente') { header("Location: login.php"); exit(); }
if (!isset($_GET['id'])) { header("Location: notificaciones_cliente.php"); exit(); }
$id_notificacion = intval($_GET['id']);
$id_usuario = $_SESSION['id_usuario'];
$sql = "UPDATE notificacion SET leida = TRUE, fecha_lectura = NOW() WHERE id_notificacion = $id_notificacion AND id_usuario = $id_usuario";
mysqli_query($conexion, $sql);
header("Location: notificaciones_cliente.php");
exit();
?>