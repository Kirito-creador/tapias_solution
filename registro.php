<?php
session_start();
include("conexion.php");

$error = '';
$exito = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $check = mysqli_query($conexion, "SELECT id_usuario FROM usuario WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "El correo electrónico ya está registrado.";
        } else {
            $sql_usuario = "INSERT INTO usuario (nombre_completo, email, telefono, password_hash, id_rol) 
                            VALUES ('$nombre', '$email', '$telefono', '$password', 3)";
            
            if (mysqli_query($conexion, $sql_usuario)) {
                $id_usuario = mysqli_insert_id($conexion);
                
                $sql_cliente = "INSERT INTO cliente (nombre_completo, email, telefono, id_usuario, id_estado_prospecto) 
                                VALUES ('$nombre', '$email', '$telefono', $id_usuario, 1)";
                mysqli_query($conexion, $sql_cliente);
                
                $exito = "¡Registro exitoso! Ahora puedes iniciar sesión.";
            } else {
                $error = "Error al registrar: " . mysqli_error($conexion);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #5a2e1f 0%, #a74e2c 50%, #3d1e12 100%);
            font-family: 'Nunito Sans', sans-serif; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px 0;
        }
        .card-register {
            background-color: #f0e8d8;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 420px;
            padding: 30px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo-icon {
            font-size: 2.5rem;
            color: #3E2A1E;
            line-height: 1;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: #3E2A1E;
            margin-top: 5px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            color: #3E2A1E;
            font-size: 0.85rem;
            margin-bottom: 3px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 8px 12px;
            background-color: #fcfaf7;
            font-size: 0.9rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(193, 81, 47, 0.25);
            border-color: #C1512F;
        }
        .btn-register {
            background-color: #C1512F;
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }
        .btn-register:hover {
            background-color: #a34225;
            color: white;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.85rem;
            color: #3E2A1E;
        }
        .login-link a {
            color: #C1512F;
            font-weight: 700;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="card-register">
        <div class="logo-container">
            <div class="logo-icon"><i class="bi bi-house-door-fill"></i></div>
            <div class="logo-text">Tapia's Solution</div>
            <div class="subtitle">Crear Cuenta de Cliente</div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger p-2 text-center" style="font-size: 0.85rem;"><?= $error ?></div>
        <?php endif; ?>
        <?php if (!empty($exito)): ?>
            <div class="alert alert-success p-2 text-center" style="font-size: 0.85rem;"><?= $exito ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-2">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Juan Pérez">
            </div>
            <div class="mb-2">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required placeholder="ejemplo@tapiasolution.com">
            </div>
            <div class="mb-2">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" required placeholder="70012345">
            </div>
            <div class="mb-2">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="********">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="********">
            </div>
            <button type="submit" class="btn-register">Registrarse</button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>

</body>
</html>