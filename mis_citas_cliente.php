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
$id_cliente = $cliente['id_cliente'];

$sql = "SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.hora_fin,
               p.tipo AS prop_tipo, p.zona AS prop_zona, p.direccion AS prop_direccion,
               u.nombre_completo AS agente_nombre,
               ec.nombre_estado AS estado_actual, c.id_estado_cita, c.observaciones
        FROM cita c
        INNER JOIN propiedad p ON c.id_propiedad = p.id_propiedad
        INNER JOIN usuario u ON c.id_agente = u.id_usuario
        INNER JOIN estado_cita ec ON c.id_estado_cita = ec.id_estado
        WHERE c.id_cliente = $id_cliente
        ORDER BY c.fecha_cita DESC, c.hora_inicio DESC";

$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Citas - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        
        .table-container { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .table-custom thead { background-color: #3E2A1E; color: white; }
        .table-custom thead th { font-weight: 600; padding: 15px; border: none; font-size: 0.9rem; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        
        .badge-estado { padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 700; color: white; }
        .bg-pendiente { background-color: #6B6B43; }
        .bg-confirmada { background-color: #C1512F; }
        .bg-completada { background-color: #3E2A1E; }
        .bg-cancelada { background-color: #555555; }

        .btn-reservar { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
        .btn-reservar:hover { background-color: #a34225; color: white; }
        .btn-volver { background-color: transparent; color: #C1512F; border: 1px solid #C1512F; font-weight: 600; padding: 8px 20px; border-radius: 5px; text-decoration: none; }
        .btn-volver:hover { background-color: #C1512F; color: white; }
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

    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: var(--navbar-bg);">Mis Citas</h1>
            <a href="propiedades_publicas.php" class="btn-reservar"><i class="bi bi-calendar-plus"></i> Reservar Visita</a>
        </div>

        <div class="table-responsive table-container">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Propiedad</th>
                        <th>Agente</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($cita = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                                <td><?= date('H:i', strtotime($cita['hora_inicio'])) ?> - <?= date('H:i', strtotime($cita['hora_fin'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($cita['prop_tipo']) ?> en <?= htmlspecialchars($cita['prop_zona']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($cita['prop_direccion']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($cita['agente_nombre']) ?></td>
                                <td><?= htmlspecialchars($cita['observaciones'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $estado = strtolower($cita['estado_actual']);
                                        $clase = 'bg-pendiente';
                                        if ($estado == 'confirmada') $clase = 'bg-confirmada';
                                        elseif ($estado == 'completada') $clase = 'bg-completada';
                                        elseif ($estado == 'cancelada') $clase = 'bg-cancelada';
                                    ?>
                                    <span class="badge-estado <?= $clase ?>"><?= htmlspecialchars($cita['estado_actual']) ?></span>
                                </td>
                                <td>
                                    <?php if ($estado == 'pendiente'): ?>
                                        <a href="cancelar_cita_cliente.php?id=<?= $cita['id_cita'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro que deseas cancelar esta cita?');"><i class="bi bi-x-circle"></i> Cancelar</a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No tienes citas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>