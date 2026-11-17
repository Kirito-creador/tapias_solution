<?php
session_start();
include("conexion.php");

// Validar que sea Administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: panel.php");
    exit();
}

// Consulta para traer las propiedades vendidas/completadas
// NOTA: Ajusta el nombre de las columnas (tipo_operacion, precio, etc.) si en tu BD se llaman distinto.
$sql = "SELECT c.id_cita, c.fecha_cita,
               p.tipo AS prop_tipo, p.zona, p.tipo_operacion, p.precio,
               a.nombre_completo AS agente_nombre
        FROM cita c
        INNER JOIN propiedad p ON c.id_propiedad = p.id_propiedad
        INNER JOIN usuario a ON c.id_agente = a.id_usuario
        WHERE c.id_estado_cita = 3
        ORDER BY c.fecha_cita DESC";

$resultado = mysqli_query($conexion, $sql);

$total_comision_empresa = 0;
$total_comision_agentes = 0;
$total_ganancia_neta = 0;
$citas = [];

// Procesar los datos primero para calcular los totales de las tarjetas
while ($fila = mysqli_fetch_assoc($resultado)) {
    $precio = $fila['precio'];
    $tipo_op = strtolower(trim($fila['tipo_operacion'])); 
    
    $porcentaje_empresa = 0;
    $porcentaje_agente = 0;

    // Lógica de porcentajes exacta solicitada
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
    
    // Ganancia Neta (Asumida como lo que le queda a la empresa)
    $ganancia_neta = $comision_empresa; 

    $total_comision_empresa += $comision_empresa;
    $total_comision_agentes += $comision_agente;
    $total_ganancia_neta += $ganancia_neta;

    // Guardar datos para la tabla
    $fila['porcentaje_empresa'] = $porcentaje_empresa;
    $fila['porcentaje_agente'] = $porcentaje_agente;
    $fila['comision_empresa'] = $comision_empresa;
    $fila['comision_agente'] = $comision_agente;
    $fila['ganancia_neta'] = $ganancia_neta;
    $citas[] = $fila;
}
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
            --border-color: #d3c8be;
        }
        body { 
            background-color: var(--bg-color); 
            font-family: 'Nunito Sans', sans-serif; 
            color: var(--text-color); 
        }
        
        /* Navbar */
        .navbar { background-color: var(--navbar-bg); border-bottom: 2px solid var(--navbar-bg); }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }

        /* Título principal */
        .page-title {
            font-size: 2.5rem;
            color: var(--navbar-bg);
            margin-bottom: 25px;
        }

        /* Estilos de Tarjetas Superiores */
        .card-stat { 
            background-color: #ffffff; 
            border: 1px solid var(--border-color); 
            border-radius: 10px; 
            padding: 20px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); 
            height: 100%;
        }
        .card-stat h5 { 
            color: #6c5c54; 
            font-size: 1.1rem; 
            margin-bottom: 15px; 
            font-family: 'Nunito Sans', sans-serif; 
            font-weight: 700;
        }
        .card-stat h2 { 
            margin: 0; 
            font-family: 'Playfair Display', serif; 
            font-weight: 700;
            font-size: 1.8rem;
        }
        .text-ganancia { color: #1e6b42; } /* Verde oscuro de la imagen */

        /* Estilos de la Tabla */
        .table-custom { 
            background-color: transparent; 
            margin-bottom: 0;
        }
        .table-custom thead { 
            background-color: #3E2A1E; 
            color: white; 
        }
        .table-custom thead th { 
            font-weight: 600; 
            padding: 15px; 
            border: none; 
            font-size: 0.95rem;
        }
        .table-custom tbody tr {
            background-color: #ffffff;
        }
        .table-custom tbody td { 
            padding: 15px; 
            vertical-align: middle; 
            border: 1px solid #f0ebe6;
            border-top: none;
            color: #4a4a4a;
        }
        .table-custom tbody tr:last-child td { 
            border-bottom: 1px solid #f0ebe6; 
        }
        
        /* Contenedor de la tabla con sombra */
        .table-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        /* Footer */
        footer {
            background-color: var(--navbar-bg);
            color: #F2EBDD;
            padding: 30px 0;
            margin-top: 50px;
        }
        footer a { color: #F2EBDD; text-decoration: none; }
        footer h6 { font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; font-size: 0.9rem;}
        footer .social-icons a { 
            display: inline-flex; align-items: center; justify-content: center;
            width: 35px; height: 35px; border-radius: 50%; 
            background-color: rgba(255,255,255,0.1); 
            color: #e09378;
            margin: 0 5px;
        }
        footer .social-icons a:hover { background-color: rgba(255,255,255,0.2); }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel.php">
                <!-- Icono de casita simulado con Bootstrap Icons -->
                <i class="bi bi-house-door-fill" style="font-size: 1.8rem; color: #7d9f7d; margin-right: 10px;"></i>
                <span style="font-weight: 700; font-size: 1.2rem;">Tapia's Solution</span>
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
                    <span class="nav-link me-3"><i class="bi bi-person-circle"></i> Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container mt-5">
        <h1 class="page-title">Reporte de Comisiones</h1>

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
        <div class="table-container table-responsive">
            <table class="table table-custom mb-0">
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
                                <td>USD <?= number_format($fila['precio'], 2, '.', ',') ?></td>
                                <td><?= htmlspecialchars($fila['agente_nombre']) ?></td>
                                <td><?= date('d/m/Y', strtotime($fila['fecha_cita'])) ?></td>
                                <td><?= number_format($fila['porcentaje_empresa'], 2) ?>%</td>
                                <td>$<?= number_format($fila['comision_empresa'], 2, '.', ',') ?></td>
                                <td><?= number_format($fila['porcentaje_agente'], 2) ?>%</td>
                                <td>$<?= number_format($fila['comision_agente'], 2, '.', ',') ?></td>
                                <td class="text-ganancia">$<?= number_format($fila['ganancia_neta'], 2, '.', ',') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">No hay comisiones registradas en el sistema.</td>
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
                    <a href="https://maps.app.goo.gl/jb2g7p2X9hqTL6o17" target="_blank"><i class="bi bi-map"></i> Ver en Google Maps</a>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Contacto</h6>
                    <div class="d-flex justify-content-center gap-3 mt-2">
                        <!-- WhatsApp -->
                        <a href="https://wa.me/59169172830" target="_blank" title="WhatsApp">
                            <i class="bi bi-whatsapp fs-4"></i>
                        </a>
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/share/1HmqoGDtrb/" target="_blank" title="Facebook">
                            <i class="bi bi-facebook fs-4"></i>
                        </a>
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/tapiasolution?stkn=NXRxem8xNHoxazJ1" target="_blank" title="Instagram">
                            <i class="bi bi-instagram fs-4"></i>
                        </a>
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