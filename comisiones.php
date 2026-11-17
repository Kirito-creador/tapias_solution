<?php
session_start();
include("conexion.php");

// Validar que sea Administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: panel.php");
    exit();
}

// Consulta para traer las citas completadas (asumiendo id_estado = 3 para completadas)
$sql = "SELECT c.id_cita, c.fecha_cita,
               p.direccion, p.zona, p.tipo AS prop_tipo, p.tipo_operacion, p.precio,
               a.nombre_completo AS agente_nombre
        FROM cita c
        INNER JOIN propiedad p ON c.id_propiedad = p.id_propiedad
        INNER JOIN usuario a ON c.id_agente = a.id_usuario
        WHERE c.id_estado_cita = 3
        ORDER BY c.fecha_cita DESC";

$resultado = mysqli_query($conexion, $sql);

// Variables para acumular los totales de las tarjetas
$total_comision_empresa = 0;
$total_comision_agentes = 0;
$total_ganancia_neta = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Comisiones - Tapia's Solution</title>
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
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        
        /* Estilos de Tarjetas */
        .card-stat { border-radius: 8px; border: 1px solid #E0D4CD; background-color: white; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .card-stat h5 { color: #7A6B63; font-size: 1rem; margin-bottom: 10px; font-family: 'Nunito Sans', sans-serif; font-weight: 700;}
        .card-stat h2 { margin: 0; font-family: 'Playfair Display', serif; font-weight: 600; }
        .text-ganancia { color: #2e7d32; }

        /* Estilos de la tabla */
        .table-custom { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .table-custom thead { background-color: #3E2A1E; color: white; }
        .table-custom thead th { font-weight: 600; padding: 15px; border: none; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #E0D4CD; }
        .table-custom tbody tr:last-child td { border-bottom: none; }

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

    <!-- Navbar -->
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

    <!-- Contenido Principal -->
    <div class="container mt-4">
        <h1 class="mb-4" style="color: var(--navbar-bg);">Reporte de Comisiones</h1>

        <!-- Calculamos los totales primero para mostrarlos en las tarjetas -->
        <?php
        // Guardamos los datos en un array temporal para poder recorrerlo dos veces (una para totales y otra para la tabla)
        $citas = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $precio = $fila['precio'];
            $tipo_op = strtolower(trim($fila['tipo_operacion'])); // 'venta', 'anticretico', 'alquiler'
            
            $porcentaje_empresa = 0;
            $porcentaje_agente = 0;

            // Lógica de porcentajes
            if ($tipo_op == 'venta') {
                $porcentaje_empresa = 5.00;
                $porcentaje_agente = 1.00;
            } elseif ($tipo_op == 'anticretico' || $tipo_op == 'anticrético') {
                $porcentaje_empresa = 4.00;
                $porcentaje_agente = 0.75;
            } elseif ($tipo_op == 'alquiler') {
                $porcentaje_empresa = 3.00;
                $porcentaje_agente = 0.50;
            }

            // Cálculos
            $comision_empresa = $precio * ($porcentaje_empresa / 100);
            $comision_agente = $precio * ($porcentaje_agente / 100);
            $ganancia_neta = $comision_empresa; // Ganancia Neta para la empresa

            // Acumular totales
            $total_comision_empresa += $comision_empresa;
            $total_comision_agentes += $comision_agente;
            $total_ganancia_neta += $ganancia_neta;

            // Guardamos los datos calculados para la tabla
            $fila['porcentaje_empresa'] = $porcentaje_empresa;
            $fila['porcentaje_agente'] = $porcentaje_agente;
            $fila['comision_empresa'] = $comision_empresa;
            $fila['comision_agente'] = $comision_agente;
            $fila['ganancia_neta'] = $ganancia_neta;
            $citas[] = $fila;
        }
        ?>

        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Comisión Empresa</h5>
                    <h2>$<?= number_format($total_comision_empresa, 2, ',', '.') ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Comisión Agentes</h5>
                    <h2>$<?= number_format($total_comision_agentes, 2, ',', '.') ?></h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card-stat">
                    <h5>Ganancia Neta</h5>
                    <h2 class="text-ganancia">$<?= number_format($total_ganancia_neta, 2, ',', '.') ?></h2>
                </div>
            </div>
        </div>

        <!-- Tabla de Datos -->
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Propiedad</th>
                        <th>Tipo Op.</th>
                        <th>Precio</th>
                        <th>Agente</th>
                        <th>Fecha</th>
                        <th>% Empresa</th>
                        <th>$ Empresa</th>
                        <th>% Agente</th>
                        <th>$ Agente</th>
                        <th>Ganancia Neta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($citas) > 0): ?>
                        <?php foreach ($citas as $fila): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['prop_tipo']) ?> en <?= htmlspecialchars($fila['zona']) ?></td>
                                <td><?= ucfirst(htmlspecialchars($fila['tipo_operacion'])) ?></td>
                                <td>USD <?= number_format($fila['precio'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($fila['agente_nombre']) ?></td>
                                <td><?= date('d/m/Y', strtotime($fila['fecha_cita'])) ?></td>
                                <td><?= number_format($fila['porcentaje_empresa'], 2) ?>%</td>
                                <td>$<?= number_format($fila['comision_empresa'], 2, ',', '.') ?></td>
                                <td><?= number_format($fila['porcentaje_agente'], 2) ?>%</td>
                                <td>$<?= number_format($fila['comision_agente'], 2, ',', '.') ?></td>
                                <td class="text-ganancia">$<?= number_format($fila['ganancia_neta'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-4">No hay comisiones registradas en el sistema.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
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