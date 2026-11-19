<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_propiedad = intval($_POST['id_propiedad']);
    $precio_final = floatval($_POST['precio_final']);
    $tipo_operacion_cierre = $_POST['tipo_operacion_cierre'];
    
    $estado_nuevo = 'Vendido';
    $porc_empresa = 0;
    $porc_agente = 0;

    if ($tipo_operacion_cierre == 'Venta') {
        $estado_nuevo = 'Vendido';
        $porc_empresa = 5.00;
        $porc_agente = 1.00;
    } elseif ($tipo_operacion_cierre == 'Alquiler') {
        $estado_nuevo = 'Alquilado';
        $porc_empresa = 5.00;
        $porc_agente = 1.00;
    } elseif ($tipo_operacion_cierre == 'Anticretico') {
        $estado_nuevo = 'Anticretico';
        $porc_empresa = 3.00;
        $porc_agente = 0.50;
    }

    $sql = "UPDATE propiedad SET 
                precio = $precio_final,
                estado = '$estado_nuevo',
                tipo_operacion = '$tipo_operacion_cierre',
                comision_empresa_porcentaje = $porc_empresa,
                comision_agente_porcentaje = $porc_agente,
                fecha_cierre = CURDATE()
            WHERE id_propiedad = $id_propiedad";

    if (mysqli_query($conexion, $sql)) {
        header("Location: propiedades.php");
        exit();
    } else {
        echo "Error al cerrar la operación: " . mysqli_error($conexion);
    }
} else {
    header("Location: propiedades.php");
    exit();
}
?>