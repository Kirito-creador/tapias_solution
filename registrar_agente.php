<?php
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: panel.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    $password = $_POST['password'];
    
    // El rol 2 es Agente
    $id_rol = 2; 
    
    // Validar que el correo no exista ya
    $check_email = mysqli_query($conexion, "SELECT id_usuario FROM usuario WHERE email = '$email'");
    if(mysqli_num_rows($check_email) > 0) {
        $error = "El correo electrónico ya está registrado.";
    } else {
        $sql = "INSERT INTO usuario (nombre_completo, email, telefono, password_hash, id_rol) 
                VALUES ('$nombre', '$email', '$telefono', '$password', $id_rol)";
        
        if(mysqli_query($conexion, $sql)){
            header("Location: agentes.php?mensaje=registrado");
            exit();
        } else {
            $error = "Error al registrar: " . mysqli_error($conexion);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Agente - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; }
        h2 { font-family: 'Playfair Display', serif; color: #3E2A1E; }
        .btn-primary { background-color: #C1512F; border: none; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <h2 class="mb-4">Crear Nuevo Agente</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre Completo:</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono:</label>
                    <input type="text" name="telefono" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña:</label>
                    <input type="text" name="password" class="form-control" required>
                    <small class="text-muted">Proporciona una contraseña al agente para que pueda iniciar sesión.</small>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Agente</button>
                <a href="agentes.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>