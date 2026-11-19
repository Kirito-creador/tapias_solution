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
    
    $hora_fin = date('H:i:s', strtotime($hora_inicio . ' +2 hours'));

    try {
        if ($tipo_cliente == 'registrado') {
            $id_cliente = intval($_POST['id_cliente']);
        } else {
            $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_manual']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono_manual']);
            $email = mysqli_real_escape_string($conexion, $_POST['email_manual']);

            if (empty($email)) {
                $email = "whatsapp_" . time() . "@temp.com";
            }

            $check = mysqli_query($conexion, "SELECT id_cliente FROM cliente WHERE email = '$email'");
            
            if (mysqli_num_rows($check) > 0) {
                $row = mysqli_fetch_assoc($check);
                $id_cliente = $row['id_cliente'];
            } else {
                $sql_cli = "INSERT INTO cliente (nombre_completo, email, telefono, id_estado_prospecto) 
                            VALUES ('$nombre', '$email', '$telefono', 1)";
                
                if (!mysqli_query($conexion, $sql_cli)) {
                    throw new Exception("Error al crear el cliente: " . mysqli_error($conexion));
                }
                $id_cliente = mysqli_insert_id($conexion);
            }
        }

        $id_estado_cita = 1;
        
        $sql = "INSERT INTO cita (fecha_cita, hora_inicio, hora_fin, observaciones, id_cliente, id_propiedad, id_agente, id_estado_cita) 
                VALUES ('$fecha_cita', '$hora_inicio', '$hora_fin', '$observaciones', $id_cliente, $id_propiedad, $id_agente, $id_estado_cita)";
        
        if (!mysqli_query($conexion, $sql)) {
            throw new Exception("Error al registrar la cita: " . mysqli_error($conexion));
        }

       
        header("Location: citas_agente.php");
        exit();

    } catch (Exception $e) {
     
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px; font-family: sans-serif;'>";
        echo "<h3>Ups, algo salió mal</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<a href='agendar_cita.php' style='color: #C1512F; font-weight: bold;'>Haz clic aquí para volver al formulario</a>";
        echo "</div>";
    }
} else {
    header("Location: agendar_cita.php");
    exit();
}
?>