<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: login.php");
    exit();
}

$consulta = "SELECT p.*, u.nombre_completo FROM propiedad p 
             INNER JOIN usuario u ON p.id_agente = u.id_usuario 
             ORDER BY p.id_propiedad DESC";
$resultado = mysqli_query($conexion, $consulta);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Propiedades - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; }
        h2 { font-family: 'Playfair Display', serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        .btn-primary { background-color: #C1512F; border: none; }
        .table-dark { background-color: #3E2A1E; color: #F2EBDD; }
        .badge-disponible { background-color: #6B6B43; color: white; }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-link me-3"><i class="bi bi-person-circle"></i> Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Todas las Propiedades</h2>
            <a href="registrar_propiedad.php" class="btn btn-primary">+ Nueva Propiedad</a>
        </div>

        <div class="card shadow-sm p-3">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tipo</th>
                        <th>Zona</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Agente</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= $fila['tipo'] ?></td>
                                <td><?= $fila['zona'] ?></td>
                                <td><?= $fila['precio'] ?> <?= $fila['moneda'] ?></td>
                                <td><span class="badge badge-disponible"><?= $fila['estado'] ?></span></td>
                                <td><?= $fila['nombre_completo'] ?></td>
                                <td>
                                    <a href="detalle_propiedad.php?id=<?= $fila['id_propiedad'] ?>" class="btn btn-sm btn-info text-white">Ver</a>
                                    <a href="editar_propiedad.php?id=<?= $fila['id_propiedad'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="eliminar_propiedad.php?id=<?= $fila['id_propiedad'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta propiedad?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay propiedades registradas en el sistema.</td>
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