<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Cliente') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT * FROM notificacion WHERE id_usuario = $id_usuario ORDER BY fecha_envio DESC";
$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificaciones - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        .page-title { color: #3E2A1E; font-size: 2.5rem; margin-bottom: 25px; }
        .btn-volver { background-color: transparent; color: #C1512F; border: 1px solid #C1512F; font-weight: 600; padding: 8px 20px; border-radius: 5px; text-decoration: none; }
        .btn-volver:hover { background-color: #C1512F; color: white; }
        
        .noti-card { background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .noti-card.leida { background-color: #f4efe9; opacity: 0.8; }
        .noti-badge { display: inline-block; background-color: #6B6B43; color: white; padding: 4px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-right: 10px; }
        .noti-fecha { font-size: 0.85rem; color: #888; }
        .noti-mensaje { margin-top: 10px; margin-bottom: 0; font-size: 1rem; color: #3E2A1E; }
        .btn-marcar-leida { background-color: transparent; border: 1px solid #C1512F; color: #C1512F; padding: 5px 15px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; text-decoration: none; }
        .btn-marcar-leida:hover { background-color: #C1512F; color: white; }
        .btn-marcar-todas { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 12px 30px; border-radius: 5px; margin-top: 20px; margin-bottom: 50px; display: inline-block; text-decoration: none; }
        .btn-marcar-todas:hover { background-color: #a34225; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel_cliente.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Notificaciones</h1>
            <a href="panel_cliente.php" class="btn-volver"><i class="bi bi-arrow-left"></i> Volver al panel</a>
        </div>

        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <?php while($noti = mysqli_fetch_assoc($resultado)): ?>
                <div class="noti-card <?= $noti['leida'] ? 'leida' : 'no-leida' ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <?php if (!$noti['leida']): ?>
                                <span class="noti-badge">Nueva</span>
                            <?php endif; ?>
                            <span class="noti-fecha"><?= date('d/m/Y H:i', strtotime($noti['fecha_envio'])) ?></span>
                            <p class="noti-mensaje"><?= htmlspecialchars($noti['mensaje']) ?></p>
                        </div>
                        <?php if (!$noti['leida']): ?>
                        <a href="marcar_notificaciones.php?id=<?= $noti['id_notificacion'] ?>" class="btn-marcar-leida">Marcar leída</a>    
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted fs-4">No tienes notificaciones.</p>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <a href="marcar_notificaciones.php" class="btn-marcar-todas">Marcar todas como leídas</a>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>