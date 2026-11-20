<?php
session_start();
include("conexion.php");

if (!isset($_GET['id'])) { header("Location: propiedades_publicas.php"); exit(); }
$id = intval($_GET['id']);

$sql = "SELECT p.*, u.nombre_completo, u.telefono 
        FROM propiedad p 
        INNER JOIN usuario u ON p.id_agente = u.id_usuario 
        WHERE p.id_propiedad = $id";
$res = mysqli_query($conexion, $sql);
$prop = mysqli_fetch_assoc($res);

if (!$prop) { die("Propiedad no encontrada."); }

$sql_img = "SELECT * FROM imagen_propiedad WHERE id_propiedad = $id ORDER BY es_principal DESC, id_imagen ASC";
$res_img = mysqli_query($conexion, $sql_img);
$imagenes = [];
while ($img = mysqli_fetch_assoc($res_img)) {
    $imagenes[] = $img;
}

$imagen_principal = "https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80";
if (count($imagenes) > 0) {
    $imagen_principal = $imagenes[0]['url_imagen'];
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
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        
        .main-image { width: 100%; height: 450px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .thumbnail { width: 100%; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer; opacity: 0.6; transition: 0.3s; border: 2px solid transparent; }
        .thumbnail:hover, .thumbnail.active { opacity: 1; border-color: #C1512F; }
        
        .card-detail { background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; }
        .list-group-item { border: none; border-bottom: 1px solid #eee; padding: 12px 0; }
        .list-group-item:last-child { border-bottom: none; }
        .badge-estado { background-color: #6B6B43; color: white; padding: 6px 12px; border-radius: 4px; font-weight: 600; }
        
        .btn-whatsapp { background-color: #25D366; color: white; border: none; font-weight: 600; padding: 12px 25px; border-radius: 5px; text-decoration: none; display: inline-block; transition: 0.3s; }
        .btn-whatsapp:hover { background-color: #1da851; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link" href="propiedades_publicas.php"><i class="bi bi-arrow-left"></i> Volver al Catálogo</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row">
            <div class="col-md-7 mb-4">
                <img src="<?= $imagen_principal ?>" alt="Imagen principal" class="main-image mb-3" id="imagenPrincipal">
                
                <?php if (count($imagenes) > 0): ?>
                    <div class="row g-2">
                        <?php foreach ($imagenes as $index => $img): ?>
                            <div class="col-3">
                                <img src="<?= $img['url_imagen'] ?>" class="thumbnail <?= $index == 0 ? 'active' : '' ?>" onclick="cambiarImagen(this, '<?= $img['url_imagen'] ?>')">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> Esta propiedad no tiene imágenes adicionales.
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-5">
                <div class="card-detail">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0"><?= $prop['tipo'] ?> en <?= $prop['tipo_operacion'] ?></h2>
                        <span class="badge-estado"><?= $prop['estado'] ?></span>
                    </div>
                    
                    <h3 style="color: #C1512F;"><?= $prop['moneda'] ?> <?= number_format($prop['precio'], 2, '.', ',') ?></h3>
                    <p class="text-muted mb-4"><i class="bi bi-geo-alt-fill"></i> <?= $prop['zona'] ?> - <?= $prop['direccion'] ?></p>

                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-aspect-ratio"></i> Superficie</span>
                            <strong><?= $prop['superficie_m2'] ?> m²</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-door-open"></i> Habitaciones</span>
                            <strong><?= $prop['habitaciones'] ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-droplet"></i> Baños</span>
                            <strong><?= $prop['banos'] ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-person-badge"></i> Agente</span>
                            <strong><?= $prop['nombre_completo'] ?></strong>
                        </li>
                    </ul>

                    <h5 class="mb-2">Descripción:</h5>
                    <p class="text-secondary"><?= nl2br($prop['descripcion']) ?></p>

                    <div class="text-center mt-4">
                        <a href="https://wa.me/591<?= $prop['telefono'] ?>?text=Hola,%20estoy%20interesado%20en%20la%20propiedad%20<?= $prop['tipo'] ?>%20en%20<?= $prop['zona'] ?>" target="_blank" class="btn-whatsapp w-100">
                            <i class="bi bi-whatsapp"></i> Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function cambiarImagen(elemento, url) {
            document.getElementById('imagenPrincipal').src = url;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            elemento.classList.add('active');
        }
    </script>
</body>
</html>