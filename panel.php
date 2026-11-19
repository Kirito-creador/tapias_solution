<?php
session_start();
include("conexion.php");

// Validar que el usuario esté logueado
if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}

// Redirigir al panel correspondiente si no es Administrador
if ($_SESSION['rol'] == 'Agente') {
    header("Location: panel_agente.php");
    exit();
} elseif ($_SESSION['rol'] == 'Cliente') {
    // Por ahora, como aún no existe panel_cliente.php, lo mandamos al inicio
    // Cuando tu compañero lo cree, puedes cambiarlo a header("Location: panel_cliente.php");
    header("Location: index.php");
    exit();
}

// --- CONSULTAS PARA LOS CONTADORES ---
// Contar propiedades
$res_prop = mysqli_query($conexion, "SELECT COUNT(*) as total FROM propiedad");
$total_propiedades = mysqli_fetch_assoc($res_prop)['total'];

// Contar agentes (usuarios con id_rol = 2)
$res_agentes = mysqli_query($conexion, "SELECT COUNT(*) as total FROM usuario WHERE id_rol = 2");
$total_agentes = mysqli_fetch_assoc($res_agentes)['total'];

// Contar clientes
$res_clientes = mysqli_query($conexion, "SELECT COUNT(*) as total FROM cliente");
$total_clientes = mysqli_fetch_assoc($res_clientes)['total'];

// Contar citas
$res_citas = mysqli_query($conexion, "SELECT COUNT(*) as total FROM cita");
$total_citas = mysqli_fetch_assoc($res_citas)['total'];

// --- CONSULTAS PARA PROPIEDADES POR ESTADO ---
$estados = ['Disponible', 'Reservado', 'Vendido', 'Alquilado', 'Anticretico'];
$conteo_estados = [];
foreach ($estados as $estado) {
    $res = mysqli_query($conexion, "SELECT COUNT(*) as total FROM propiedad WHERE estado = '$estado'");
    $conteo_estados[$estado] = mysqli_fetch_assoc($res)['total'];
}

// --- CONSULTAS PARA FINANZAS ---
// Sumar comisiones de la empresa de propiedades vendidas/alquiladas
$res_fin = mysqli_query($conexion, "SELECT 
    SUM((precio * comision_empresa_porcentaje) / 100) as comision_empresa,
    SUM((precio * comision_agente_porcentaje) / 100) as comision_agente
    FROM propiedad 
    WHERE estado IN ('Vendido', 'Alquilado')");
$finanzas = mysqli_fetch_assoc($res_fin);

$comision_empresa = $finanzas['comision_empresa'] ?? 0;
$comision_agente = $finanzas['comision_agente'] ?? 0;
$ganancia_neta = $comision_empresa; // En este caso la ganancia neta para la empresa es su comisión
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
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
        
        .card-stat {
            border: 1px solid #E0D4CD;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            background-color: #FCFAF8;
        }
        .card-stat h5 { color: #8C7A6B; font-family: 'Nunito Sans', sans-serif; font-weight: 600; margin-bottom: 5px; }
        .card-stat h2 { font-size: 2.5rem; margin: 0; font-weight: 700; }
        
        .card-panel {
            border: 1px solid #E0D4CD;
            border-radius: 8px;
            background-color: #FCFAF8;
            height: 100%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .card-panel .card-header {
            background-color: transparent;
            border-bottom: none;
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 600;
            padding-bottom: 0;
        }
        
        .btn-custom {
            background-color: var(--btn-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            padding: 12px;
            width: 100%;
            transition: 0.3s;
        }
        .btn-custom:hover { background-color: #a34225; color: white; }
        
        .text-ganancia { color: #2E7D32; font-weight: 700; }
        
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
                <!-- Puedes reemplazar esto con tu logo real -->
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Index.php"><i class="bi bi-house"></i> Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-speedometer2"></i> Panel Admin</a>
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
        <div class="row mb-4">
            <div class="col-12">
                <h1 style="color: var(--navbar-bg);">Panel de Administrador</h1>
            </div>
        </div>

        <!-- Tarjetas de Contadores -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stat p-3 text-center">
                    <h5>Propiedades</h5>
                    <h2><?= $total_propiedades ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat p-3 text-center">
                    <h5>Agentes</h5>
                    <h2><?= $total_agentes ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat p-3 text-center">
                    <h5>Clientes</h5>
                    <h2><?= $total_clientes ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat p-3 text-center">
                    <h5>Citas</h5>
                    <h2><?= $total_citas ?></h2>
                </div>
            </div>
        </div>

        <!-- Secciones de Estado y Finanzas -->
        <div class="row mb-4">
            <!-- Propiedades por Estado -->
            <div class="col-md-6 mb-3">
                <div class="card card-panel">
                    <div class="card-body p-4">
                        <h5 class="card-header mb-3">Propiedades por estado</h5>
                        <ul class="list-unstyled mb-0" style="line-height: 1.8;">
                            <li>Disponibles: <strong><?= $conteo_estados['Disponible'] ?></strong></li>
                            <li>Reservadas: <strong><?= $conteo_estados['Reservado'] ?></strong></li>
                            <li>Vendidas: <strong><?= $conteo_estados['Vendido'] ?></strong></li>
                            <li>Alquiladas: <strong><?= $conteo_estados['Alquilado'] ?></strong></li>
                            <li>Anticretico: <strong><?= $conteo_estados['Anticretico'] ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Finanzas -->
            <div class="col-md-6 mb-3">
                <div class="card card-panel">
                    <div class="card-body p-4">
                        <h5 class="card-header mb-3">Finanzas</h5>
                        <ul class="list-unstyled mb-0" style="line-height: 1.8;">
                            <li>Comisión empresa: <strong>$<?= number_format($comision_empresa, 2) ?></strong></li>
                            <li>Comisión agentes: <strong>$<?= number_format($comision_agente, 2) ?></strong></li>
                            <li>Ganancia neta: <strong class="text-ganancia">$<?= number_format($ganancia_neta, 2) ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción (Redirecciones) -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <a href="agentes.php" class="btn btn-custom">Ver Agentes</a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="propiedades.php" class="btn btn-custom">Ver Propiedades</a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="clientes.php" class="btn btn-custom">Ver Clientes</a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="citas.php" class="btn btn-custom">Ver Citas</a>
            </div>
            <div class="col-md-3 mb-3">
                <a href="comisiones.php" class="btn btn-custom">Ver Comisiones</a>
            </div>
        </div>
    </div>
    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>