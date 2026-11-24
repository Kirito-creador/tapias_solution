<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM propiedad WHERE id_propiedad = $id";
    
    if (mysqli_query($conexion, $sql)) {
        header("Location: propiedades.php");
        exit();
    } else {
        echo "<script>
                alert('No se pudo eliminar. Es posible que la propiedad esté vinculada a una venta o cita. Error: " . mysqli_error($conexion) . "');
                window.location.href = 'propiedades.php';
              </script>";
    }
} else {
    header("Location: propiedades.php");
    exit();
}
?>