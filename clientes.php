<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: panel.php");
    exit();
}

$sql = "SELECT c.id_cliente, c.nombre_completo, c.email, c.telefono, 
               c.fecha_registro,
               ep.nombre AS estado_prospecto,
               u.nombre_completo AS agente
        FROM cliente c
        LEFT JOIN estado_prospecto ep ON c.id_estado_prospecto = ep.id_estado_prospecto
        LEFT JOIN usuario u ON c.id_usuario = u.id_usuario
        ORDER BY c.fecha_registro DESC";

$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        h1, h2 { font-family: 'Playfair Display', serif; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        .btn-primary { background-color: #C1512F; border: none; }
        .btn-primary:hover { background-color: #a34225; }
        .table-dark { background-color: #3E2A1E; color: #F2EBDD; }
        .badge-activo { background-color: #6B6B43; color: white; }
    </style>
</head>
<body>

    <!-- NAVBAR ACTUALIZADA -->
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
        <?php if(isset($_GET["mensaje"])): ?>
            <div class="alert alert-success">Operación realizada con éxito.</div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Clientes Registrados</h2>
            <a href="registrar_cliente.php" class="btn btn-primary">Crear Cliente</a>
        </div>

        <div class="card shadow-sm p-3">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Agente</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['nombre_completo']) ?></td>
                                <td><?= htmlspecialchars($fila['email']) ?></td>
                                <td><?= htmlspecialchars($fila['telefono']) ?></td>
                                <td>
                                    <span class="badge badge-activo rounded-pill">
                                        <?= htmlspecialchars($fila['estado_prospecto'] ?? 'Sin estado') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($fila['agente'] ?? '—') ?></td>
                                <td><?= date('d/m/Y', strtotime($fila['fecha_registro'])) ?></td>
                                <td>
                                    <a href="editar_cliente.php?id=<?= $fila['id_cliente'] ?>" 
                                       class="btn btn-sm btn-warning">✏️</a>
                                    <a href="eliminar_cliente.php?id=<?= $fila['id_cliente'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Seguro que deseas eliminar a este cliente?');">🗑️</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay clientes registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("footer.php"); ?>