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

                  if ($password == $usuario['password_hash']) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['nombre_rol'];

            // Redirección solo para Admin y Agente
            if ($_SESSION['rol'] == 'admin') {
                header("Location: panel.php"); // Tu archivo panel.php
            } elseif ($_SESSION['rol'] == 'agente') {
                header("Location: panel_agente.php"); // Tu archivo panel_agente.php
            } else {
                // Si es cliente (o cualquier otro rol), por ahora lo regresamos al login
                // o le mostramos un mensaje de "Acceso no autorizado"
                $_SESSION['error'] = "Acceso exclusivo para administradores y agentes.";
                header("Location: login.php");
            }
            exit();

        } else 
        {
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: login.php");
            exit();
        }
        ?>