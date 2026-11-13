<?php
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

$condicion = ($_SESSION['rol'] == 'Agente') ? "WHERE p.id_agente = " . $_SESSION['id_usuario'] : "";
$consulta = "SELECT p.*, u.nombre_completo FROM propiedad p 
             INNER JOIN usuario u ON p.id_agente = u.id_usuario 
             $condicion ORDER BY p.id_propiedad DESC";
$resultado = mysqli_query($conexion, $consulta);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Propiedades - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        h1, h2 { font-family: 'Playfair Display', serif; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        .btn-primary { background-color: #C1512F; border: none; }
        .btn-primary:hover { background-color: #a34225; }
        .table-dark { background-color: #3E2A1E; color: #F2EBDD; }
        .badge-disponible { background-color: #6B6B43; color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="panel.php">Tapia's Solution</a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-link me-3">Hola, <?= $_SESSION['nombre'] ?> (<?= $_SESSION['rol'] ?>)</span>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if(isset($_GET["mensaje"])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Operación realizada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestión de Propiedades</h2>
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
                                    <?php if($_SESSION['rol'] == 'Administrador'): ?>
                                        <a href="eliminar_propiedad.php?id=<?= $fila['id_propiedad'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta propiedad?');">Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay propiedades registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>