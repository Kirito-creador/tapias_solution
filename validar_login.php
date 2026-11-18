<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $password = $_POST['password'];

    $consulta = "SELECT u.id_usuario, u.nombre_completo, u.password_hash, r.nombre_rol 
                 FROM usuario u 
                 INNER JOIN rol r ON u.id_rol = r.id_rol 
                 WHERE u.email = '$email' AND u.activo = 1";
    
    $resultado = mysqli_query($conexion, $consulta);

    if (mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);

        // Verificamos la contraseña
        if ($password == $usuario['password_hash']) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['nombre_rol'];
            die("El rol que leyó de la base de datos es: " . $_SESSION['rol']);

            // Redirección solo para Admin y Agente
            if ($_SESSION['rol'] == 'admin') {
                header("Location: panel.php"); 
            } elseif ($_SESSION['rol'] == 'agente') {
                header("Location: panel_agente.php"); 
            } else {
                $_SESSION['error'] = "Acceso exclusivo para administradores y agentes.";
                header("Location: login.php");
            }
            exit();

        } else {
            // Contraseña incorrecta
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: login.php");
            exit();
        }
    } else {
        // El correo no existe o el usuario está inactivo
        $_SESSION['error'] = "El correo electrónico no está registrado.";
        header("Location: login.php");
        exit();
    }
} else {
    // Si intentan entrar directo al archivo sin usar el formulario
    header("Location: login.php");
    exit();
}
?>