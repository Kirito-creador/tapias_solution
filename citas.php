<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: panel.php");
    exit();
}

$sql = "SELECT c.id_cita, c.fecha_cita, c.hora_inicio, c.hora_fin,
               cli.nombre_completo AS cliente_nombre, cli.telefono AS cliente_telefono,
               p.tipo AS prop_tipo, p.zona AS prop_zona,
               a.nombre_completo AS agente_nombre,
               ec.nombre_estado AS estado_actual
        FROM cita c
        INNER JOIN cliente cli ON c.id_cliente = cli.id_cliente
        INNER JOIN propiedad p ON c.id_propiedad = p.id_propiedad
        INNER JOIN usuario a ON c.id_agente = a.id_usuario
        INNER JOIN estado_cita ec ON c.id_estado_cita = ec.id_estado
        ORDER BY c.fecha_cita DESC, c.hora_inicio DESC";

$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las Citas - Tapia's Solution</title>
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
        .navbar { background-color: var(--navbar-bg); border-bottom: 2px solid var(--navbar-bg); }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        .navbar-brand img { height: 40px; margin-right: 10px; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }

        .table-custom { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .table-custom thead { background-color: #3E2A1E; color: white; }
        .table-custom thead th { font-weight: 600; padding: 15px; border: none; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #E0D4CD; }
        .table-custom tbody tr:last-child td { border-bottom: none; }

        .cliente-tel { font-size: 0.85rem; color: #7A6B63; margin-top: 3px; }

        .btn-volver { background-color: transparent; color: var(--btn-color); border: 1px solid var(--btn-color); font-weight: 600; }
        .btn-volver:hover { background-color: var(--btn-color); color: white; }

        .badge-estado { padding: 8px 15px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; color: white; }
        .estado-pendiente { background-color: #737A45; }
        .estado-confirmada { background-color: #C1512F; }
        .estado-completada { background-color: #737A45; }
        .estado-cancelada { background-color: #555555; }

        footer {
            background-color: var(--navbar-bg);
            color: #F2EBDD;
            padding: 30px 0;
            margin-top: 50px;
        }
        footer a { color: #F2EBDD; text-decoration: none; }
        footer h6 { font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="panel.php"><i class="bi bi-house"></i> Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="panel.php"><i class="bi bi-speedometer2"></i> Panel Admin</a>
                    </li>
                </ul>
                <div class="navbar-nav align-items-center">
                    <span class="nav-link me-3"><i class="bi bi-person-circle"></i> Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: var(--navbar-bg);">Todas las Citas</h1>
            <a href="panel.php" class="btn btn-volver px-4 py-2"><i class="bi bi-arrow-left"></i> Volver al panel</a>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Propiedad</th>
                        <th>Agente</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($fila['fecha_cita'])) ?></td>
                                <td><?= date('H:i', strtotime($fila['hora_inicio'])) ?> - <?= date('H:i', strtotime($fila['hora_fin'])) ?></td>

                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($fila['cliente_nombre']) ?></div>
                                    <div class="cliente-tel"><?= htmlspecialchars($fila['cliente_telefono']) ?></div>
                                </td>

                                <td><?= htmlspecialchars($fila['prop_tipo']) ?> en <?= htmlspecialchars($fila['prop_zona']) ?></td>

                                <td><?= htmlspecialchars($fila['agente_nombre']) ?></td>

                                <td>
                                    <?php
                                        $estado = strtolower($fila['estado_actual']);
                                        $clase_badge = 'estado-pendiente';

                                        if ($estado == 'confirmada') {
                                            $clase_badge = 'estado-confirmada';
                                        } elseif ($estado == 'completada') {
                                            $clase_badge = 'estado-completada';
                                        } elseif ($estado == 'cancelada') {
                                            $clase_badge = 'estado-cancelada';
                                        }
                                    ?>
                                    <span class="badge-estado <?= $clase_badge ?>">
                                        <?= htmlspecialchars($fila['estado_actual']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay citas registradas en el sistema.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <h6>Horarios</h6>
                    <p class="mb-1"><i class="bi bi-clock"></i> Lun a Dom</p>
                    <p>9:00 AM a 6:00 PM</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Oficina</h6>
                    <p class="mb-1"><i class="bi bi-geo-alt"></i> Cochabamba, Bolivia</p>
                    <a href="#"><i class="bi bi-map"></i> Ver en Google Maps</a>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Contacto</h6>
                    <div class="d-flex justify-content-center gap-3 mt-2">
                        <a href="#"><i class="bi bi-whatsapp fs-4"></i></a>
                        <a href="#"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#"><i class="bi bi-instagram fs-4"></i></a>
                    </div>
                </div>
            </div>
            <hr style="border-color: #5a4030;">
            <div class="text-center mt-3">
                <small>&copy; <?= date('Y') ?> - Tapia's Solution | Inmobiliaria en Cochabamba, Bolivia</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>