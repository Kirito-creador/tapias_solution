<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: panel.php");
    exit();
}

if (!isset($_GET['id'])) { header("Location: agentes.php"); exit(); }
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
    
    $sql = "UPDATE usuario SET nombre_completo='$nombre', telefono='$telefono' WHERE id_usuario=$id";
    if(mysqli_query($conexion, $sql)){
        header("Location: agentes.php?mensaje=editado");
    }
}

$res = mysqli_query($conexion, "SELECT * FROM usuario WHERE id_usuario = $id");
$agente = mysqli_fetch_assoc($res);
if (!$agente) { die("Agente no encontrado."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Agente - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #F2EBDD; }
        .btn-primary { background-color: #C1512F; border: none; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <h2>Editar Agente</h2>
            <form method="POST">
                <div class="mb-3">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" class="form-control" value="<?= $agente['nombre_completo'] ?>" required>
                </div>
                <div class="mb-3">
                    <label>Correo (No modificable):</label>
                    <input type="email" class="form-control" value="<?= $agente['email'] ?>" disabled>
                </div>
                <div class="mb-3">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" class="form-control" value="<?= $agente['telefono'] ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="agentes.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>