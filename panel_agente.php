<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

$id_agente = $_SESSION['id_usuario'];

// --- NOTIFICACIONES ---
// Contar notificaciones no leídas
$sql_noti_count = "SELECT COUNT(*) as total FROM notificacion WHERE id_usuario = $id_agente AND leida = FALSE";
$res_noti_count = mysqli_query($conexion, $sql_noti_count);
$total_notificaciones = mysqli_fetch_assoc($res_noti_count)['total'];

// Obtener las últimas 5 notificaciones (leídas o no) para mostrar en el dropdown
$sql_notis = "SELECT * FROM notificacion WHERE id_usuario = $id_agente ORDER BY fecha_envio DESC LIMIT 5";
$res_notis = mysqli_query($conexion, $sql_notis);

// --- CONTADORES DE PROPIEDADES ---
$total_propiedades = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM propiedad WHERE id_agente = $id_agente"))['total'];
$disponibles = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM propiedad WHERE id_agente = $id_agente AND estado = 'Disponible'"))['total'];
$vendidas = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM propiedad WHERE id_agente = $id_agente AND estado = 'Vendido'"))['total'];
$alquiladas = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT COUNT(*) as total FROM propiedad WHERE id_agente = $id_agente AND estado = 'Alquilado'"))['total'];

// Cálculo de comisión (ejemplo: 1% del precio de las propiedades vendidas)
$comision_query = mysqli_query($conexion, "SELECT SUM(precio * 0.01) as comision FROM propiedad WHERE id_agente = $id_agente AND estado = 'Vendido'");
$comision = mysqli_fetch_assoc($comision_query)['comision'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Agente - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h2, h3 { font-family: 'Playfair Display', serif; }
        .card-stat { border: none; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; padding: 20px; background: white; }
        .card-stat h5 { color: #6B6B43; font-weight: 600; }
        .card-stat h1 { font-family: 'Playfair Display', serif; color: #3E2A1E; font-size: 3rem; margin: 0; }
        .btn-terracotta { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 15px; }
        .btn-terracotta:hover { background-color: #a34225; color: white; }
        .btn-olive { background-color: #6B6B43; color: white; border: none; font-weight: 600; padding: 15px; }
        .btn-olive:hover { background-color: #555533; color: white; }
        .comision-box { background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        
        /* Estilos del botón de notificaciones */
        .nav-noti { position: relative; display: inline-block; margin-right: 20px; }
        .btn-noti { background: transparent; border: none; color: #F2EBDD; font-size: 1.4rem; position: relative; padding: 5px 10px; transition: 0.3s; }
        .btn-noti:hover { color: #C1512F; }
        .badge-noti-burbuja { position: absolute; top: -2px; right: -5px; background-color: #C1512F; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: 700; border: 2px solid #3E2A1E; }
        
        /* Estilos del dropdown de notificaciones */
        .dropdown-menu-noti { width: 320px; padding: 0; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); border: none; }
        .dropdown-header { background-color: #3E2A1E; color: #F2EBDD; padding: 12px 15px; border-radius: 8px 8px 0 0; font-weight: 600; font-family: 'Playfair Display', serif; font-size: 1.1rem; }
        .noti-item { padding: 12px 15px; border-bottom: 1px solid #eee; display: block; text-decoration: none; color: #3E2A1E; transition: 0.2s; }
        .noti-item:hover { background-color: #f8f9fa; color: #C1512F; }
        .noti-item.no-leida { background-color: #fff8f0; border-left: 4px solid #C1512F; }
        .noti-item .noti-tipo { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #6B6B43; display: block; margin-bottom: 2px; }
        .noti-item .noti-mensaje { font-size: 0.9rem; line-height: 1.3; margin: 0; }
        .noti-item .noti-fecha { font-size: 0.7rem; color: #999; margin-top: 4px; display: block; }
        .dropdown-footer { padding: 10px; text-align: center; background-color: #f8f9fa; border-radius: 0 0 8px 8px; font-size: 0.85rem; }
        .dropdown-footer a { color: #C1512F; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel_agente.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            
            <!-- Contenedor derecho de la barra de navegación -->
            <div class="navbar-nav ms-auto align-items-center">
                
                <!-- BOTÓN DE NOTIFICACIONES (Dropdown) -->
                <div class="dropdown nav-noti">
                    <button class="btn-noti dropdown-toggle" type="button" id="dropdownNotis" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <?php if ($total_notificaciones > 0): ?>
                            <span class="badge-noti-burbuja"><?= $total_notificaciones ?></span>
                        <?php endif; ?>
                    </button>
                    
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-noti" aria-labelledby="dropdownNotis">
                        <div class="dropdown-header">
                            Notificaciones
                        </div>
                        
                        <?php if (mysqli_num_rows($res_notis) > 0): ?>
                            <?php while($noti = mysqli_fetch_assoc($res_notis)): ?>
                                <a href="#" class="noti-item <?= $noti['leida'] ? '' : 'no-leida' ?>">
                                    <span class="noti-tipo"><?= htmlspecialchars($noti['tipo']) ?></span>
                                    <p class="noti-mensaje"><?= htmlspecialchars($noti['mensaje']) ?></p>
                                    <span class="noti-fecha"><?= date('d/m/Y H:i', strtotime($noti['fecha_envio'])) ?></span>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted" style="font-size: 0.9rem;">No tienes notificaciones.</div>
                        <?php endif; ?>

                        <div class="dropdown-footer">
                            <a href="marcar_notificaciones.php">Marcar todas como leídas</a>
                        </div>
                    </div>
                </div>
                <!-- FIN BOTÓN NOTIFICACIONES -->

                <span class="nav-link me-3">Hola, <?= $_SESSION['nombre'] ?></span>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-1">Panel del Agente</h2>
        <p class="mb-4">Bienvenido, <strong><?= $_SESSION['nombre'] ?></strong></p>

        <!-- Tarjetas de Métricas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3"><div class="card-stat"><h5>Mis Propiedades</h5><h1><?= $total_propiedades ?></h1></div></div>
            <div class="col-md-3 mb-3"><div class="card-stat"><h5>Disponibles</h5><h1><?= $disponibles ?></h1></div></div>
            <div class="col-md-3 mb-3"><div class="card-stat"><h5>Vendidas</h5><h1><?= $vendidas ?></h1></div></div>
            <div class="col-md-3 mb-3"><div class="card-stat"><h5>Alquiladas</h5><h1><?= $alquiladas ?></h1></div></div>
        </div>

        <!-- Comisión -->
        <div class="comision-box mb-4">
            <h5 class="fw-bold">Mi Comisión Total</h5>
            <h1 style="color: #1e6b42; font-family: 'Playfair Display', serif;">$<?= number_format($comision, 2) ?></h1>
        </div>

        <!-- Botones de Acción -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="registrar_propiedad.php" class="btn btn-terracotta w-100 mb-3"><i class="bi bi-plus-circle"></i> Registrar Nueva Propiedad</a>
                <a href="citas_agente.php" class="btn btn-terracotta w-100"><i class="bi bi-calendar-check"></i> Ver Mis Citas</a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="propiedades_agente.php" class="btn btn-olive w-100 mb-3"><i class="bi bi-list-ul"></i> Ver Mis Propiedades</a>
                <a href="agendar_cita.php" class="btn btn-olive w-100"><i class="bi bi-calendar-plus"></i> Agendar Visita</a>
            </div>
        </div>
    </div>
    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>