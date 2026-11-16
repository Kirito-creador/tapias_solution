<?php
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
    <title>Agentes - Tapia's Solution</title>
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
        .badge-activo { background-color: #6B6B43; color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="panel_admin.php">Tapia's Solution</a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-link me-3">Hola, <?= $_SESSION['nombre'] ?></span>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if(isset($_GET["mensaje"])): ?>
            <div class="alert alert-success">Operación realizada con éxito.</div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Agentes Inmobiliarios</h2>
            <a href="registrar_agente.php" class="btn btn-primary">+ Crear Agente</a>
        </div>

        <div class="card shadow-sm p-3">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
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
                                <td><?= $fila['nombre_completo'] ?></td>
                                <td><?= $fila['email'] ?></td>
                                <td><?= $fila['telefono'] ?></td>
                                <td><span class="badge bg-secondary rounded-pill"><?= $fila['total_propiedades'] ?></span></td>
                                <td><span class="badge bg-secondary rounded-pill"><?= $fila['total_vendidas'] ?></span></td>
                                <td>
                                    <a href="editar_agente.php?id=<?= $fila['id_usuario'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                    <a href="eliminar_agente.php?id=<?= $fila['id_usuario'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar a este agente?');">🗑</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay agentes registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>