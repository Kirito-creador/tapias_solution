<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) { header("Location: panel.php"); exit(); }
$id = intval($_GET['id']);

$sql = "SELECT p.*, u.nombre_completo FROM propiedad p 
        INNER JOIN usuario u ON p.id_agente = u.id_usuario 
        WHERE p.id_propiedad = $id";
$res = mysqli_query($conexion, $sql);
$prop = mysqli_fetch_assoc($res);

if (!$prop) { die("Propiedad no encontrada."); }

// Buscar imagen
$sql_img = "SELECT url_imagen FROM imagen_propiedad WHERE id_propiedad = $id ORDER BY es_principal DESC, id_imagen ASC LIMIT 1";
$res_img = mysqli_query($conexion, $sql_img);
$img = mysqli_fetch_assoc($res_img);

$ruta_imagen = null;
if ($img && !empty($img['url_imagen'])) {
    $ruta_imagen = $img['url_imagen'];
    if (strpos($ruta_imagen, 'http') !== 0 && strpos($ruta_imagen, 'data:') !== 0) {
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
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        
        .breadcrumb-custom { font-size: 0.85rem; color: #888; margin-bottom: 20px; }
        .breadcrumb-custom a { color: #C1512F; text-decoration: none; }

        .card-img-container { 
            background-color: white; 
            border-radius: 10px; 
            padding: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
        }
        .main-image { width: 100%; height: 400px; object-fit: cover; border-radius: 8px; }
        
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0,0,0,0.5);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
        }
        .nav-arrow:hover { background-color: rgba(0,0,0,0.8); }
        .nav-arrow.left { left: 20px; }
        .nav-arrow.right { right: 20px; }

        .card-details { 
            background-color: white; 
            border-radius: 10px; 
            padding: 25px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            height: 100%;
        }
        .badge-estado { background-color: #6B6B43; color: white; padding: 6px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; }
        .title-prop { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 2rem; margin: 0; }
        .price-prop { font-size: 1.5rem; font-weight: 700; color: #3E2A1E; margin: 10px 0; }
        .location-prop { font-size: 0.9rem; color: #777; margin-bottom: 20px; }
        .badge-operacion { background-color: #3E2A1E; color: white; font-size: 0.75rem; padding: 4px 12px; border-radius: 4px; display: inline-block; margin-bottom: 20px; }

        .detail-list { font-size: 0.9rem; }
        .detail-list li { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .detail-list li:last-child { border-bottom: none; }
        .detail-list li strong { font-weight: 600; color: #3E2A1E; }

        .desc-box { font-size: 0.9rem; color: #666; line-height: 1.6; margin-top: 15px; }

        .btn-reservar { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 12px; border-radius: 5px; width: 100%; margin-top: 20px; transition: 0.3s; }
        .btn-reservar:hover { background-color: #a34225; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <!-- Breadcrumbs -->
        <div class="breadcrumb-custom">
            <a href="panel.php">Inicio</a> / <a href="<?= ($_SESSION['rol'] == 'Agente') ? 'propiedades_agente.php' : 'propiedades.php' ?>">Catálogo</a> / <?= $prop['tipo'] ?> en <?= $prop['zona'] ?>
        </div>

        <div class="row">
            <!-- Columna Izquierda: Imagen y Flechas -->
            <div class="col-md-7 mb-4">
                <div class="card-img-container">
                    <?php if($ruta_imagen): ?>
                        <img src="<?= htmlspecialchars($ruta_imagen) ?>" alt="Foto de la propiedad" class="main-image" id="imagenPrincipal">
                        <!-- Flechas de navegación (solo decorativas si hay una sola imagen) -->
                        <button class="nav-arrow left"><i class="bi bi-chevron-left"></i></button>
                        <button class="nav-arrow right"><i class="bi bi-chevron-right"></i></button>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center">Esta propiedad no tiene imágenes subidas.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Columna Derecha: Detalles -->
            <div class="col-md-5">
                <div class="card-details">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h1 class="title-prop"><?= $prop['tipo'] ?></h1>
                        <span class="badge-estado"><?= $prop['estado'] ?></span>
                    </div>
                    
                    <div class="location-prop"><i class="bi bi-geo-alt"></i> <?= $prop['zona'] ?> — <?= $prop['direccion'] ?></div>
                    <div class="price-prop"><?= $prop['moneda'] ?> <?= number_format($prop['precio'], 2, '.', ',') ?></div>
                    
                    <div class="badge-operacion"><?= $prop['tipo_operacion'] ?></div>

                    <ul class="list-group detail-list mb-3">
                        <li class="list-group-item"><span><i class="bi bi-bounding-box"></i> Superficie</span> <strong><?= $prop['superficie_m2'] ?> m²</strong></li>
                        <li class="list-group-item"><span><i class="bi bi-door-open"></i> Habitaciones</span> <strong><?= $prop['habitaciones'] ?></strong></li>
                        <li class="list-group-item"><span><i class="bi bi-droplet"></i> Baños</span> <strong><?= $prop['banos'] ?></strong></li>
                        <li class="list-group-item"><span><i class="bi bi-person-badge"></i> Agente responsable</span> <strong><?= $prop['nombre_completo'] ?></strong></li>
                        <li class="list-group-item"><span><i class="bi bi-calendar"></i> Publicado</span> <strong><?= date('d/m/Y', strtotime($prop['fecha_publicacion'])) ?></strong></li>
                    </ul>

                    <div class="desc-box">
                        <?= nl2br(htmlspecialchars($prop['descripcion'])) ?>
                    </div>

                    <a href="agendar_cita.php?propiedad=<?= $id ?>" class="btn btn-reservar"><i class="bi bi-calendar-check"></i> Reservar Visita</a>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>