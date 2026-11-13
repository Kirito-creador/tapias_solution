<?php
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) { 
    header("Location: index.php"); 
    exit(); 
}
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $tipo_operacion = $_POST['tipo_operacion'];
    $precio = $_POST['precio'];
    $moneda = $_POST['moneda'];
    $estado = $_POST['estado'];
    $zona = mysqli_real_escape_string($conexion, $_POST['zona']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $habitaciones = intval($_POST['habitaciones']);
    $banos = intval($_POST['banos']);
    $superficie = floatval($_POST['superficie_m2']);
    
    $sql = "UPDATE propiedad SET 
                tipo='$tipo', 
                tipo_operacion='$tipo_operacion', 
                precio=$precio, 
                moneda='$moneda', 
                estado='$estado', 
                zona='$zona', 
                direccion='$direccion', 
                descripcion='$descripcion', 
                habitaciones=$habitaciones, 
                banos=$banos, 
                superficie_m2=$superficie 
            WHERE id_propiedad=$id";
            
    if(mysqli_query($conexion, $sql)){
        header("Location: index.php?mensaje=editado");
        exit();
    } else {
        echo "<div class='alert alert-danger m-3'>Error: " . mysqli_error($conexion) . "</div>";
    }
}

$res = mysqli_query($conexion, "SELECT * FROM propiedad WHERE id_propiedad = $id");
$prop = mysqli_fetch_assoc($res);
if (!$prop) { die("Propiedad no encontrada."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Propiedad - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; }
        h2 { font-family: 'Playfair Display', serif; color: #3E2A1E; }
        .btn-primary { background-color: #C1512F; border: none; }
        .btn-primary:hover { background-color: #a34225; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 800px;">
            <h2 class="mb-4">Editar Propiedad #<?= $id ?></h2>
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipo de Inmueble:</label>
                        <select name="tipo" class="form-select" required>
                            <option value="Casa" <?= ($prop['tipo']=='Casa')?'selected':'' ?>>Casa</option>
                            <option value="Departamento" <?= ($prop['tipo']=='Departamento')?'selected':'' ?>>Departamento</option>
                            <option value="Terreno" <?= ($prop['tipo']=='Terreno')?'selected':'' ?>>Terreno</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Operación:</label>
                        <select name="tipo_operacion" class="form-select" required>
                            <option value="Venta" <?= ($prop['tipo_operacion']=='Venta')?'selected':'' ?>>Venta</option>
                            <option value="Alquiler" <?= ($prop['tipo_operacion']=='Alquiler')?'selected':'' ?>>Alquiler</option>
                            <option value="Anticretico" <?= ($prop['tipo_operacion']=='Anticretico')?'selected':'' ?>>Anticrético</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado:</label>
                        <select name="estado" class="form-select">
                            <option value="Disponible" <?= ($prop['estado']=='Disponible')?'selected':'' ?>>Disponible</option>
                            <option value="Reservado" <?= ($prop['estado']=='Reservado')?'selected':'' ?>>Reservado</option>
                            <option value="Vendido" <?= ($prop['estado']=='Vendido')?'selected':'' ?>>Vendido</option>
                            <option value="Alquilado" <?= ($prop['estado']=='Alquilado')?'selected':'' ?>>Alquilado</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Zona:</label>
                        <input type="text" name="zona" class="form-control" value="<?= $prop['zona'] ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dirección:</label>
                        <input type="text" name="direccion" class="form-control" value="<?= $prop['direccion'] ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Precio:</label>
                        <input type="number" name="precio" step="0.01" class="form-control" value="<?= $prop['precio'] ?>" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Moneda:</label>
                        <select name="moneda" class="form-select">
                            <option value="USD" <?= ($prop['moneda']=='USD')?'selected':'' ?>>USD</option>
                            <option value="Bs" <?= ($prop['moneda']=='Bs')?'selected':'' ?>>Bs.</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Habitaciones:</label>
                        <input type="number" name="habitaciones" class="form-control" value="<?= $prop['habitaciones'] ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Baños:</label>
                        <input type="number" name="banos" class="form-control" value="<?= $prop['banos'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Superficie (m²):</label>
                        <input type="number" name="superficie_m2" step="0.01" class="form-control" value="<?= $prop['superficie_m2'] ?>">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Descripción:</label>
                        <textarea name="descripcion" class="form-control" rows="3"><?= $prop['descripcion'] ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>