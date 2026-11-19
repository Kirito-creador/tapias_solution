<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_agente = $_SESSION['id_usuario'];
    $id_propiedad = intval($_POST['id_propiedad']);
    $fecha_cita = $_POST['fecha_cita'];
    $hora_inicio = $_POST['hora_inicio'];
    $observaciones = mysqli_real_escape_string($conexion, $_POST['observaciones']);
    $tipo_cliente = $_POST['tipo_cliente'];
    
    // Calcular hora de fin (2 horas después)
    $hora_fin = date('H:i:s', strtotime($hora_inicio . ' +2 hours'));

    // Lógica para Cliente Registrado o Manual
    if ($tipo_cliente == 'registrado') {
        $id_cliente = intval($_POST['id_cliente']);
    } else {
        // Crear nuevo cliente con los datos de WhatsApp/Facebook
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_manual']);
        $telefono = mysqli_real_escape_string($conexion, $_POST['telefono_manual']);
        $email = mysqli_real_escape_string($conexion, $_POST['email_manual']);

        // Validar si el email ya existe (si lo proporcionó)
        if (!empty($email)) {
            $check = mysqli_query($conexion, "SELECT id_cliente FROM cliente WHERE email = '$email'");
            if (mysqli_num_rows($check) > 0) {
                $row = mysqli_fetch_assoc($check);
                $id_cliente = $row['id_cliente'];
            } else {
                // Insertar nuevo cliente
                $sql_cli = "INSERT INTO cliente (nombre_completo, email, telefono) VALUES ('$nombre', '$email', '$telefono')";
                mysqli_query($conexion, $sql_cli);
                $id_cliente = mysqli_insert_id($conexion);
            }
        } else {
            // Si no hay email, generamos uno genérico para no romper la restricción UNIQUE de la BD
            $email_fake = "whatsapp_" . time() . "@temp.com";
            $sql_cli = "INSERT INTO cliente (nombre_completo, email, telefono) VALUES ('$nombre', '$email_fake', '$telefono')";
            mysqli_query($conexion, $sql_cli);
            $id_cliente = mysqli_insert_id($conexion);
        }
    }

    // Insertar la cita con estado 'Pendiente' (id_estado_cita = 1)
    $id_estado_cita = 1;
    
    $sql = "INSERT INTO cita (fecha_cita, hora_inicio, hora_fin, observaciones, id_cliente, id_propiedad, id_agente, id_estado_cita) 
            VALUES ('$fecha_cita', '$hora_inicio', '$hora_fin', '$observaciones', $id_cliente, $id_propiedad, $id_agente, $id_estado_cita)";
    
    if (mysqli_query($conexion, $sql)) {
        // Redirigir a la lista de citas
        header("Location: citas_agente.php");
        exit();
    } else {
        echo "Error al registrar la cita: " . mysqli_error($conexion);
    }
} else {
    header("Location: agendar_cita.php");
    exit();
}
?>