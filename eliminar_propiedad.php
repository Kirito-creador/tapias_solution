<?php
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }
$id = intval($_GET['id']);

$sql = "SELECT p.*, u.nombre_completo FROM propiedad p 
        INNER JOIN usuario u ON p.id_agente = u.id_usuario 
        WHERE p.id_propiedad = $id";
$res = mysqli_query($conexion, $sql);
$prop = mysqli_fetch_assoc($res);

if (!$prop) { die("Propiedad no encontrada."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Propiedad - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; }
        .card-header { background-color: #3E2A1E; color: #F2EBDD; font-family: 'Playfair Display', serif; }
        .btn-warning { background-color: #C1512F; border: none; color: white; }
        .btn-warning:hover { background-color: #a34225; color: white; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 700px;">
            <div class="card-header py-3">
                <h3 class="mb-0">Detalle de la Propiedad</h3>
            </div>
            <div class="card-body p-4">
                <h4 class="text-dark mb-3"><?= $prop['tipo'] ?> en <?= $prop['tipo_operacion'] ?></h4>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Zona:</strong> <?= $prop['zona'] ?></li>
                    <li class="list-group-item"><strong>Dirección:</strong> <?= $prop['direccion'] ?></li>
                    <li class="list-group-item"><strong>Precio:</strong> <?= $prop['precio'] ?> <?= $prop['moneda'] ?></li>
                    <li class="list-group-item"><strong>Superficie:</strong> <?= $prop['superficie_m2'] ?> m²</li>
                    <li class="list-group-item"><strong>Habitaciones:</strong> <?= $prop['habitaciones'] ?> | <strong>Baños:</strong> <?= $prop['banos'] ?></li>
                    <li class="list-group-item"><strong>Estado:</strong> <span class="badge bg-success"><?= $prop['estado'] ?></span></li>
                    <li class="list-group-item"><strong>Agente Responsable:</strong> <?= $prop['nombre_completo'] ?></li>
                    <li class="list-group-item"><strong>Descripción:</strong><br> <?= nl2br($prop['descripcion']) ?></li>
                </ul>
                <a href="index.php" class="btn btn-secondary">Volver a la lista</a>
                <a href="editar_propiedad.php?id=<?= $prop['id_propiedad'] ?>" class="btn btn-warning">Editar</a>
            </div>
        </div>
    </div>
</body>
</html>