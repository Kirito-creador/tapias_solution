<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

$id_agente = $_SESSION['id_usuario'];

$sql = "SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.hora_fin,
               cli.nombre_completo AS cliente_nombre, cli.telefono AS cliente_telefono, cli.email AS cliente_email,
               p.tipo AS prop_tipo, p.zona AS prop_zona,
               ec.nombre_estado AS estado_actual, c.id_estado_cita
        FROM cita c
        INNER JOIN cliente cli ON c.id_cliente = cli.id_cliente
        INNER JOIN propiedad p ON c.id_propiedad = p.id_propiedad
        INNER JOIN estado_cita ec ON c.id_estado_cita = ec.id_estado
        WHERE c.id_agente = $id_agente
        ORDER BY c.fecha_cita DESC, c.hora_inicio DESC";

$resultado = mysqli_query($conexion, $sql);

$pendientes = 0;
$confirmadas = 0;
$canceladas = 0;
$citas = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $citas[] = $fila;
    $estado = strtolower($fila['estado_actual']);
    if ($estado == 'pendiente') $pendientes++;
    elseif ($estado == 'confirmada') $confirmadas++;
    elseif ($estado == 'cancelada') $canceladas++;
}
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
        :root {
            --bg-color: #F5EFEB;
            --navbar-bg: #3E2A1E;
            --btn-color: #C1512F;
            --text-color: #3E2A1E;
        }
        body { background-color: var(--bg-color); font-family: 'Nunito Sans', sans-serif; color: var(--text-color); }
        .navbar { background-color: var(--navbar-bg); }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        
        .card-stat { 
            background-color: #ffffff; border: 1px solid #E0D4CD; border-radius: 8px; 
            padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); 
        }
        .card-stat h5 { font-weight: 700; color: #3E2A1E; font-size: 1.1rem; }
        .card-stat h2 { font-family: 'Playfair Display', serif; font-size: 2rem; margin: 0; color: #C1512F; }

        .table-container { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .table-custom thead { background-color: #3E2A1E; color: white; }
        .table-custom thead th { font-weight: 600; padding: 15px; border: none; font-size: 0.95rem; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #E0D4CD; }
        
        .cliente-info { font-weight: 700; color: #3E2A1E; }
        .cliente-sub { font-size: 0.85rem; color: #7A6B63; }
        
        .badge-estado { padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 700; color: white; }
        .bg-pendiente { background-color: #6B6B43; } /* Verde Oliva */
        .bg-confirmada { background-color: #C1512F; } /* Terracota */
        .bg-completada { background-color: #6B6B43; } /* Verde Oliva */
        .bg-cancelada { background-color: #555555; } /* Gris */

        .btn-accion { 
            background-color: transparent; border: 1px solid #ccc; border-radius: 4px; 
            padding: 5px 8px; color: #555; transition: 0.3s; display: inline-flex; 
            align-items: center; justify-content: center; text-decoration: none;
        }
        .btn-accion:hover { background-color: #f0f0f0; color: #3E2A1E; border-color: #999; }
        .btn-accion.confirmar:hover { color: #C1512F; border-color: #C1512F; }
        .btn-accion.cancelar:hover { color: #dc3545; border-color: #dc3545; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel_agente.php">
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
            <h1 style="color: var(--navbar-bg);">Mis Citas</h1>
            <a href="agendar_cita.php" class="btn" style="background-color: #C1512F; color: white; font-weight: 600;"><i class="bi bi-plus-circle"></i> Nueva Cita</a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Pendientes</h5>
                    <h2 class="text-pendiente"><?= $pendientes ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Confirmadas</h5>
                    <h2 class="text-confirmada"><?= $confirmadas ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Canceladas</h5>
                    <h2 class="text-cancelada"><?= $canceladas ?></h2>
                </div>
            </div>
        </div>

        <p class="text-muted mb-3"><i class="bi bi-info-circle"></i> Recordá que cada visita bloquea 2 horas del agente (traslado, mostrar el inmueble y explicar).</p>

        <div class="table-responsive table-container">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Propiedad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($citas) > 0): ?>
                        <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($cita['fecha_cita'])) ?></td>
                                <td><?= date('H:i', strtotime($cita['hora_inicio'])) ?> - <?= date('H:i', strtotime($cita['hora_fin'])) ?></td>
                                <td>
                                    <div class="cliente-info"><?= htmlspecialchars($cita['cliente_nombre']) ?></div>
                                    <div class="cliente-sub"><?= htmlspecialchars($cita['cliente_email']) ?></div>
                                </td>
                                <td><?= htmlspecialchars($cita['cliente_telefono']) ?></td>
                                <td><?= htmlspecialchars($cita['prop_tipo']) ?> en <?= htmlspecialchars($cita['prop_zona']) ?></td>
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
                                        <a href="cambiar_estado_cita.php?id=<?= $cita['id_cita'] ?>&estado=2" class="btn-accion confirmar" title="Confirmar Cita"><i class="bi bi-check-circle"></i></a>
                                    <?php endif; ?>
                                    
                                    <?php if ($estado == 'confirmada' || $estado == 'pendiente'): ?>
                                        <a href="cambiar_estado_cita.php?id=<?= $cita['id_cita'] ?>&estado=3" class="btn-accion" title="Completar Visita"><i class="bi bi-check2-square"></i></a>
                                    <?php endif; ?>

                                    <?php if ($estado != 'cancelada' && $estado != 'completada'): ?>
                                        <a href="cambiar_estado_cita.php?id=<?= $cita['id_cita'] ?>&estado=4" class="btn-accion cancelar" title="Cancelar Cita" onclick="return confirm('¿Seguro que deseas cancelar esta cita?');"><i class="bi bi-x-circle"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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