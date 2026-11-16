<?php
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $precio = $_POST['precio'];
    $moneda = $_POST['moneda'];
    $zona = mysqli_real_escape_string($conexion, $_POST['zona']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $habitaciones = intval($_POST['habitaciones']);
    $banos = intval($_POST['banos']);
    $superficie = floatval($_POST['superficie_m2']);
    $tipo_operacion = $_POST['tipo_operacion'];
    $id_agente = $_SESSION['id_usuario']; 

    $sql = "INSERT INTO propiedad (tipo, precio, moneda, zona, direccion, descripcion, habitaciones, banos, superficie_m2, id_agente, tipo_operacion) 
            VALUES ('$tipo', $precio, '$moneda', '$zona', '$direccion', '$descripcion', $habitaciones, $banos, $superficie, $id_agente, '$tipo_operacion')";
    
    if(mysqli_query($conexion, $sql)){
        if($_SESSION['rol'] == 'Agente') {
            header("Location: propiedades_agente.php");
        } else {
            header("Location: propiedades.php");
        }
        exit();
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Propiedad - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #F2EBDD; }
        h2 { font-family: 'Playfair Display', serif; color: #3E2A1E; }
        .btn-primary { background-color: #C1512F; border: none; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 800px;">
            <h2 class="mb-4">Registrar Nueva Propiedad</h2>
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Tipo:</label>
                        <select name="tipo" class="form-select" required>
                            <option value="Casa">Casa</option>
                            <option value="Departamento">Departamento</option>
                            <option value="Terreno">Terreno</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Operación:</label>
                        <select name="tipo_operacion" class="form-select" required>
                            <option value="Venta">Venta</option>
                            <option value="Alquiler">Alquiler</option>
                            <option value="Anticretico">Anticrético</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Zona:</label>
                        <input type="text" name="zona" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Dirección:</label>
                    <input type="text" name="direccion" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Precio:</label>
                        <input type="number" name="precio" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Moneda:</label>
                        <select name="moneda" class="form-select">
                            <option value="USD">USD</option>
                            <option value="Bs">Bs.</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Habitaciones:</label>
                        <input type="number" name="habitaciones" class="form-control" value="0">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Baños:</label>
                        <input type="number" name="banos" class="form-control" value="0">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Superficie (m²):</label>
                        <input type="number" name="superficie_m2" step="0.01" class="form-control">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label>Descripción:</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Propiedad</button>
                <a href="<?= ($_SESSION['rol'] == 'Agente') ? 'propiedades_agente.php' : 'propiedades.php' ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>