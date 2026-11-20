<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) { header("Location: panel.php"); exit(); }
$id = intval($_GET['id']);

$sql = "SELECT p.*, u.nombre_completo FROM propiedad p INNER JOIN usuario u ON p.id_agente = u.id_usuario WHERE p.id_propiedad = $id";
$res = mysqli_query($conexion, $sql);
$prop = mysqli_fetch_assoc($res);

if (!$prop) { die("Propiedad no encontrada."); }

$sql_img = "SELECT url_imagen FROM imagen_propiedad WHERE id_propiedad = $id ORDER BY es_principal DESC, id_imagen ASC LIMIT 1";
$res_img = mysqli_query($conexion, $sql_img);
$img = mysqli_fetch_assoc($res_img);

$ruta_imagen = null;
if ($img && !empty($img['url_imagen'])) {
    $ruta_imagen = $img['url_imagen'];
    // Si la ruta no empieza con http ni con data:, la construimos con la carpeta del proyecto
    if (strpos($ruta_imagen, 'http') !== 0 && strpos($ruta_imagen, 'data:') !== 0) {
        // Ajusta si tu carpeta base es distinta a 'tapias_solution'
        $ruta_imagen = "/tapias_solution/" . $ruta_imagen;
    }
}
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
        .card-header { background-color: #3E2A1E; color: #F2EBDD; font-family: 'Playfair Display', serif;} 
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow mx-auto" style="max-width: 700px;">
            
            <div class="card-header py-3">
                <h3 class="mb-0">Detalle de la Propiedad</h3>
            </div>
            
            <div class="card-body p-4">
                <h4 class="text-dark mb-3"><?= $prop['tipo'] ?> en <?= $prop['tipo_operacion'] ?></h4>
                
                <?php if($ruta_imagen): ?>
                    <div class="text-center mb-4">
                        <img src="<?= htmlspecialchars($ruta_imagen) ?>" class="img-fluid rounded shadow-sm" alt="Foto de la propiedad" style="max-height: 350px; width: 100%; object-fit: cover;">
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary text-center mb-4">Esta propiedad no tiene imágenes subidas.</div>
                <?php endif; ?>
                
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Zona:</strong> <?= htmlspecialchars($prop['zona']) ?></li>
                    <li class="list-group-item"><strong>Dirección:</strong> <?= htmlspecialchars($prop['direccion']) ?></li>
                    <li class="list-group-item"><strong>Precio:</strong> <?= $prop['precio'] ?> <?= $prop['moneda'] ?></li>
                    <li class="list-group-item"><strong>Superficie:</strong> <?= $prop['superficie_m2'] ?> m²</li>
                    <li class="list-group-item"><strong>Habitaciones:</strong> <?= $prop['habitaciones'] ?> | <strong>Baños:</strong> <?= $prop['banos'] ?></li>
                    <li class="list-group-item"><strong>Estado:</strong> <span class="badge bg-success"><?= $prop['estado'] ?></span></li>
                    <li class="list-group-item"><strong>Agente Responsable:</strong> <?= htmlspecialchars($prop['nombre_completo']) ?></li>
                    <li class="list-group-item"><strong>Descripción:</strong><br> <?= nl2br(htmlspecialchars($prop['descripcion'])) ?></li>
                </ul>
                
                <a href="<?= ($_SESSION['rol'] == 'Agente') ? 'propiedades_agente.php' : 'propiedades.php' ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver a la lista</a>
            </div>
        </div>
    </div>
</body>
</html>