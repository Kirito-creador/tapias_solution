<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Cliente') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$sql_cliente = "SELECT id_cliente FROM cliente WHERE id_usuario = $id_usuario";
$res_cliente = mysqli_query($conexion, $sql_cliente);
$cliente = mysqli_fetch_assoc($res_cliente);

if (!$cliente) {
    die("No tienes un perfil de cliente asociado.");
}
$id_cliente = $cliente['id_cliente'];

$sql_stats = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN id_estado_cita = 1 THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN id_estado_cita = 2 THEN 1 ELSE 0 END) as confirmadas
    FROM cita WHERE id_cliente = $id_cliente";
$res_stats = mysqli_query($conexion, $sql_stats);
$stats = mysqli_fetch_assoc($res_stats);

$sql_citas = "SELECT c.fecha_cita, c.hora_inicio, p.tipo, p.zona, ec.nombre_estado AS estado_actual
              FROM cita c 
              INNER JOIN propiedad p ON c.id_propiedad = p.id_propiedad 
              INNER JOIN estado_cita ec ON c.id_estado_cita = ec.id_estado
              WHERE c.id_cliente = $id_cliente 
              ORDER BY c.fecha_cita DESC LIMIT 3";
$res_citas = mysqli_query($conexion, $sql_citas);

$sql_noti = "SELECT COUNT(*) as total FROM notificacion WHERE id_usuario = $id_usuario AND leida = FALSE";
$res_noti = mysqli_query($conexion, $sql_noti);
$total_notificaciones = mysqli_fetch_assoc($res_noti)['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Panel - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }

        .card-stat { background: white; border: none; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .card-stat h5 { color: #6B6B43; font-weight: 600; font-size: 0.9rem; margin-bottom: 10px; }
        .card-stat h2 { font-family: 'Playfair Display', serif; font-size: 2.5rem; margin: 0; color: #C1512F; }
        
        .table-custom { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .table-custom thead { background-color: #3E2A1E; color: white; }
        .table-custom thead th { padding: 15px; font-weight: 600; border: none; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #eee; }
        
        .badge-estado { padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 700; color: white; }
        .bg-pendiente { background-color: #6B6B43; }
        .bg-confirmada { background-color: #C1512F; }
        .bg-completada { background-color: #3E2A1E; }

        .btn-noti { position: relative; color: #F2EBDD; font-size: 1.2rem; text-decoration: none; margin-right: 15px; }
        .badge-noti-burbuja { position: absolute; top: -5px; right: -10px; background-color: #C1512F; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: 700; }
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
                <a href="notificaciones_cliente.php" class="btn-noti">
                    <i class="bi bi-bell-fill"></i>
                    <?php if ($total_notificaciones > 0): ?>
                        <span class="badge-noti-burbuja"><?= $total_notificaciones ?></span>
                    <?php endif; ?>
                </a>
                <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1 class="mb-0">Mi Panel</h1>
            <a href="propiedades_publicas.php" class="btn" style="background-color: #C1512F; color: white; font-weight: 600;"><i class="bi bi-book"></i> Ver Catálogo</a>
        </div>
        <p class="text-muted mb-4">Bienvenido, <strong><?= $_SESSION['nombre'] ?></strong>. Aquí puedes gestionar tus visitas agendadas.</p>

        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Visitas agendadas</h5>
                    <h2><?= $stats['total'] ?? 0 ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Pendientes</h5>
                    <h2 style="color: #6B6B43;"><?= $stats['pendientes'] ?? 0 ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Confirmadas</h5>
                    <h2><?= $stats['confirmadas'] ?? 0 ?></h2>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Tus últimas visitas</h3>
            <a href="mis_citas_cliente.php" class="btn btn-sm btn-outline-dark">Ver todas</a>
        </div>

        <div class="table-responsive table-custom">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Propiedad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res_citas) > 0): ?>
                        <?php while($c = mysqli_fetch_assoc($res_citas)): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($c['fecha_cita'])) ?></td>
                                <td><?= date('H:i', strtotime($c['hora_inicio'])) ?></td>
                                <td><?= $c['tipo'] ?> en <?= $c['zona'] ?></td>
                                <td>
                                    <?php
                                        $estado = strtolower($c['estado_actual']);
                                        $clase = 'bg-pendiente';
                                        if ($estado == 'confirmada') $clase = 'bg-confirmada';
                                        elseif ($estado == 'completada') $clase = 'bg-completada';
                                    ?>
                                    <span class="badge-estado <?= $clase ?>"><?= $c['estado_actual'] ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No tienes visitas recientes.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>