<?php
session_start();
if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1 { font-family: 'Playfair Display', serif; }
        .panel-card { border: none; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-5">
        <div class="container">
            <a class="navbar-brand" href="#">Tapia's Solution</a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container text-center">
        <?php 
        if ($_SESSION['rol'] == 'Administrador') {
            echo "<h1 class='mb-3' style='color: #3E2A1E;'>Panel de Administrador</h1>";
            echo "<p class='lead mb-4'>Bienvenido/a. Tienes acceso total al sistema.</p>";
            echo "<div class='alert alert-dark panel-card mb-4'>Rol: <strong>Administrador</strong></div>";
            echo "<div class='row justify-content-center gap-3'>
                    <div class='col-md-3'><a href='index.php' class='btn btn-primary w-100 p-3'>Gestionar Propiedades</a></div>
                    <div class='col-md-3'><a href='#' class='btn btn-secondary w-100 p-3'>Gestionar Agentes</a></div>
                  </div>";
        } elseif ($_SESSION['rol'] == 'Agente') {
            echo "<h1 class='mb-3' style='color: #C1512F;'>Panel de Agente</h1>";
            echo "<p class='lead mb-4'>Bienvenido/a. Gestiona tus inmuebles y revisa tus citas.</p>";
            echo "<div class='alert alert-warning panel-card mb-4'>Rol: <strong>Agente Inmobiliario</strong></div>";
            echo "<div class='row justify-content-center gap-3'>
                    <div class='col-md-3'><a href='propiedades.php' class='btn btn-primary w-100 p-3'>Mis Propiedades</a></div>
                    <div class='col-md-3'><a href='#' class='btn btn-success w-100 p-3'>Mis Citas Agendadas</a></div>
                  </div>";
        } elseif ($_SESSION['rol'] == 'Cliente') {
            echo "<h1 class='mb-3' style='color: #6B6B43;'>Panel de Cliente</h1>";
            echo "<p class='lead mb-4'>Bienvenido/a. Encuentra tu próximo hogar.</p>";
            echo "<div class='alert alert-success panel-card mb-4'>Rol: <strong>Cliente</strong></div>";
            echo "<div class='row justify-content-center gap-3'>
                    <div class='col-md-3'><a href='#' class='btn btn-primary w-100 p-3'>Ver Catálogo Público</a></div>
                    <div class='col-md-3'><a href='#' class='btn btn-secondary w-100 p-3'>Mis Visitas</a></div>
                  </div>";
        }
        ?>
    </div>
</body>
</html>