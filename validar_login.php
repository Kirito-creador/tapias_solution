<?php
session_start();   
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $password = $_POST['password'];

    // Consulta para obtener los datos del usuario y su rol
    $consulta = "SELECT u.id_usuario, u.nombre_completo, u.password_hash, r.nombre_rol 
                 FROM usuario u 
                 INNER JOIN rol r ON u.id_rol = r.id_rol 
                 WHERE u.email = '$email' AND u.activo = 1";
    
    $resultado = mysqli_query($conexion, $consulta);

    if (mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Comparación directa de contraseña
        if ($password == $usuario['password_hash']) {
            // Guardamos los datos en la sesión
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['nombre_rol'];

            // Redirección según el rol (con Mayúscula Inicial tal como en tu BD)
            if ($_SESSION['rol'] == 'Administrador') {
                header("Location: panel.php"); // Panel de Administrador
                exit();
            } elseif ($_SESSION['rol'] == 'Agente') {
                header("Location: panel_agente.php"); // Panel de Agente
                exit();
            } elseif ($_SESSION['rol'] == 'Cliente') {
                header("Location: panel_cliente.php"); // Panel de Cliente (o index.php si aún no lo creas)
                exit();
            } else {
                $_SESSION['error'] = "Rol no reconocido.";
                header("Location: login.php");
                exit();
            }

        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "El correo electrónico no está registrado o el usuario está inactivo.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>