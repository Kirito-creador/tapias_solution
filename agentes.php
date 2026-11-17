<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: panel.php");
    exit();
}

$sql = "SELECT u.id_usuario, u.nombre_completo, u.email, u.telefono, 
               COUNT(p.id_propiedad) as total_propiedades,
               COUNT(CASE WHEN p.estado = 'Vendido' THEN 1 END) as total_vendidas
        FROM usuario u
        LEFT JOIN propiedad p ON u.id_usuario = p.id_agente
        WHERE u.id_rol = 2 
        GROUP BY u.id_usuario";
$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agentes - Tapia's Solution</title>
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
        
        .btn-primary { background-color: var(--btn-color); border: none; font-weight: 600; }
        .btn-primary:hover { background-color: #a34225; }
        
        .table-custom { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .table-custom thead { background-color: #3E2A1E; color: white; }
        .table-custom thead th { font-weight: 600; padding: 15px; border: none; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #E0D4CD; }
        .table-custom tbody tr:last-child td { border-bottom: none; }
        
        .card-custom { background-color: white; border-radius: 8px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); padding: 20px; }
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
        <?php if(isset($_GET["mensaje"])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Operación realizada con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--navbar-bg);">Agentes Inmobiliarios</h2>
            <a href="registrar_agente.php" class="btn btn-primary px-4 py-2"><i class="bi bi-plus-lg"></i> Crear Agente</a>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Propiedades</th>
                            <th>Ventas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultado) > 0): ?>
                            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?= htmlspecialchars($fila['nombre_completo']) ?></td>
                                    <td><?= htmlspecialchars($fila['email']) ?></td>
                                    <td><?= htmlspecialchars($fila['telefono']) ?></td>
                                    <td><span class="badge bg-secondary rounded-pill px-3 py-2"><?= $fila['total_propiedades'] ?></span></td>
                                    <td><span class="badge bg-secondary rounded-pill px-3 py-2"><?= $fila['total_vendidas'] ?></span></td>
                                    <td>
                                        <a href="editar_agente.php?id=<?= $fila['id_usuario'] ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
                                        <a href="eliminar_agente.php?id=<?= $fila['id_usuario'] ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar a este agente?');">🗑</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No hay agentes registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>