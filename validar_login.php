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

        // Se asume que las contraseñas están en texto plano por tu código original
        if ($password == $usuario['password_hash']) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['nombre_rol'];

            // REDIRECCIÓN SEGÚN EL ROL
            if ($usuario['nombre_rol'] == 'Administrador') {
                header("Location: panel.php");
            } elseif ($usuario['nombre_rol'] == 'Agente') {
                header("Location: panel_agente.php"); // Redirige al panel de agentes
            } elseif ($usuario['nombre_rol'] == 'Cliente') {
                // Como el archivo panel_cliente.php no existe, te recomiendo crearlo. 
                // Por ahora, si intentan entrar como cliente, los manda al index.
                header("Location: index.php"); 
            } else {
                header("Location: panel.php"); // Por defecto
            }
            exit();

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