<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Cliente') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: propiedades_publicas.php");
    exit();
}

$id_propiedad = intval($_GET['id']);

$sql = "SELECT p.*, 
               (SELECT url_imagen FROM imagen_propiedad WHERE id_propiedad = p.id_propiedad LIMIT 1) AS imagen 
        FROM propiedad p 
        WHERE p.id_propiedad = $id_propiedad AND p.estado = 'Disponible'";
$res = mysqli_query($conexion, $sql);
$prop = mysqli_fetch_assoc($res);

if (!$prop) {
    die("Propiedad no encontrada o no disponible.");
}

$ruta_imagen = "https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80";
if (!empty($prop['imagen'])) {
    $ruta_imagen = $prop['imagen'];
    if (strpos($ruta_imagen, 'http') !== 0 && strpos($ruta_imagen, 'data:') !== 0) {
        $ruta_imagen = "/tapias_solution/" . $ruta_imagen;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Visita - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        
        .card-prop { background: white; border-radius: 10px; border: 1px solid #ddd; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .card-prop img { border-radius: 10px 0 0 10px; object-fit: cover; height: 100%; width: 100%; min-height: 200px; }
        .card-prop-body { padding: 20px; }
        
        .form-label { font-weight: 600; font-size: 0.95rem; }
        .form-control { border: 1px solid #ccc; border-radius: 5px; padding: 10px; }
        
        .btn-confirmar { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 10px 25px; border-radius: 5px; }
        .btn-confirmar:hover { background-color: #a34225; color: white; }
        .btn-cancelar { background-color: transparent; border: 1px solid #C1512F; color: #C1512F; font-weight: 600; padding: 10px 25px; border-radius: 5px; text-decoration: none; }
        .btn-cancelar:hover { background-color: #C1512F; color: white; }
        
        .btn-volver { background-color: transparent; border: 1px solid #C1512F; color: #C1512F; font-weight: 600; padding: 5px 15px; border-radius: 5px; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .btn-volver:hover { background-color: #C1512F; color: white; }
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
                <a class="nav-link me-3" href="panel_cliente.php"><i class="bi bi-grid"></i> Mi Panel</a>
                <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <a href="detalle_propiedad_publica.php?id=<?= $id_propiedad ?>" class="btn-volver"><i class="bi bi-arrow-left"></i></a>
        <h1 class="mb-4">Reservar Visita</h1>

        <div class="card-prop mb-4">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($ruta_imagen) ?>" alt="Propiedad">
                </div>
                <div class="col-md-8">
                    <div class="card-prop-body">
                        <h4><?= htmlspecialchars($prop['tipo']) ?> en <?= htmlspecialchars($prop['zona']) ?></h4>
                        <p class="text-muted mb-1"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($prop['direccion']) ?></p>
                        <p class="fw-bold fs-5"><?= htmlspecialchars($prop['moneda']) ?> <?= number_format($prop['precio'], 2, '.', ',') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mb-3">Elige fecha y hora</h3>
        <p class="text-muted mb-4" style="font-size: 0.9rem;">
            <i class="bi bi-info-circle"></i> Las visitas se agendan entre 9:00 y 18:00. Si elige una hora que el agente ya tiene ocupada (cada visita ocupa 2 horas del agente), se le asignará automáticamente el siguiente horario libre del mismo día.
        </p>

        <form action="procesar_reserva_cliente.php" method="POST">
            <input type="hidden" name="id_propiedad" value="<?= $id_propiedad ?>">
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha de la visita</label>
                    <input type="date" name="fecha_cita" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hora de la visita</label>
                    <input type="time" name="hora_inicio" class="form-control" required min="09:00" max="16:00" value="09:00">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Observaciones (opcional)</label>
                <textarea name="observaciones" class="form-control" rows="3" placeholder="Cuéntale al agente qué te interesa de la propiedad"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-confirmar"><i class="bi bi-calendar-check"></i> Confirmar Reserva</button>
                <a href="detalle_propiedad_publica.php?id=<?= $id_propiedad ?>" class="btn-cancelar">Cancelar</a>
            </div>
        </form>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>