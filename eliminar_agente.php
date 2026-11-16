<?php
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    die("Acceso denegado.");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Al tener llaves foráneas en la tabla propiedades, MySQL no dejará borrar al agente si tiene propiedades.
    // Una buena práctica es desactivar al usuario en vez de borrarlo:
    $sql = "UPDATE usuario SET activo = 0 WHERE id_usuario = $id";
    
    if (mysqli_query($conexion, $sql)) {
        header("Location: agentes.php?mensaje=eliminado");
    } else {
        echo "Error al eliminar: " . mysqli_error($conexion);
    }
}
?>