<?php
session_start();
include("conexion.php");

if (!isset($_GET['id'])) { header("Location: propiedades_publicas.php"); exit(); }
$id = intval($_GET['id']);

$sql = "SELECT p.*, u.nombre_completo, u.telefono 
        FROM propiedad p 
        INNER JOIN usuario u ON p.id_agente = u.id_usuario 
        WHERE p.id_propiedad = $id AND p.estado = 'Disponible'";
$res = mysqli_query($conexion, $sql);
$prop = mysqli_fetch_assoc($res);

if (!$prop) { die("Propiedad no encontrada o no disponible."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Propiedad - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; }
        .card-header { background-color: #3E2A1E; color: #F2EBDD; font-family: 'Playfair Display', serif; }
        .btn-primary { background-color: #C1512F; border: none; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 800px;">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Detalle de la Propiedad</h3>
                <a href="propiedades_publicas.php" class="btn btn-sm btn-light">Volver al catálogo</a>
            </div>
            <div class="card-body p-4">
                <h4 class="text-dark mb-3"><?= $prop['tipo'] ?> en <?= $prop['tipo_operacion'] ?></h4>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item"><strong>Zona:</strong> <?= $prop['zona'] ?></li>
                            <li class="list-group-item"><strong>Dirección:</strong> <?= $prop['direccion'] ?></li>
                            <li class="list-group-item"><strong>Precio:</strong> <?= $prop['moneda'] ?> <?= number_format($prop['precio'], 2, '.', ',') ?></li>
                            <li class="list-group-item"><strong>Superficie:</strong> <?= $prop['superficie_m2'] ?> m²</li>
                            <li class="list-group-item"><strong>Habitaciones:</strong> <?= $prop['habitaciones'] ?> | <strong>Baños:</strong> <?= $prop['banos'] ?></li>
                            <li class="list-group-item"><strong>Estado:</strong> <span class="badge bg-success"><?= $prop['estado'] ?></span></li>
                            <li class="list-group-item"><strong>Agente Responsable:</strong> <?= $prop['nombre_completo'] ?> (<?= $prop['telefono'] ?>)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Descripción:</strong></p>
                        <p><?= nl2br($prop['descripcion']) ?></p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="https://wa.me/591<?= $prop['telefono'] ?>?text=Hola,%20estoy%20interesado%20en%20la%20propiedad%20<?= $prop['tipo'] ?>%20en%20<?= $prop['zona'] ?>" target="_blank" class="btn btn-success btn-lg"><i class="bi bi-whatsapp"></i> Contactar por WhatsApp</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>