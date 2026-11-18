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

        // Comparación directa (asumiendo que tus contraseñas están en texto plano)
        if ($password == $usuario['password_hash']) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['nombre_rol'];

            // Lógica de redirección según el rol
            if ($_SESSION['rol'] == 'Administrador') {
                header("Location: panel.php"); // El admin sí va a panel.php
                exit();
            } elseif ($_SESSION['rol'] == 'Agente') {
                header("Location: panel_agente.php"); // El agente va a su propio panel
                exit();
            } elseif ($_SESSION['rol'] == 'Cliente') {
                // Aún no tienes panel_cliente.php, así que puedes mandarlos temporalmente al index o login
                // Si ya lo creas, cambia esto a: header("Location: panel_cliente.php");
                header("Location: index.php"); 
                exit();
            } else {
                // Por si hay otro rol desconocido
                header("Location: login.php");
                exit();
            }

        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "El correo electrónico no está registrado.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>