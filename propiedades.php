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
        h1, h2 { font-family: 'Playfair Display', serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        .btn-primary { background-color: #C1512F; border: none; }
        
        .table-container { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .table-custom thead { background-color: #3E2A1E; color: white; }
        .table-custom thead th { font-weight: 600; padding: 15px; border: none; font-size: 0.95rem; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #E0D4CD; font-size: 0.95rem; }
        
        .badge-estado { padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 700; color: white; }
        .bg-disponible { background-color: #6B6B43; }
        .bg-vendido { background-color: #3E2A1E; }
        .bg-alquilado { background-color: #3E2A1E; }

        /* Botones de acción */
        .btn-accion { 
            background-color: transparent; border: 1px solid #ccc; border-radius: 4px; 
            padding: 5px 8px; color: #555; transition: 0.3s; display: inline-flex; 
            align-items: center; justify-content: center; text-decoration: none; font-size: 1rem;
        }
        .btn-accion:hover { background-color: #f0f0f0; color: #3E2A1E; border-color: #999; }
        .btn-accion.cerrar { color: #C1512F; border-color: #C1512F; background-color: #fff0ed; }
        .btn-accion.cerrar:hover { background-color: #C1512F; color: white; }
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

    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Todas las Propiedades</h1>
            <a href="registrar_propiedad.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nueva Propiedad</a>
        </div>

        <div class="table-responsive table-container">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Zona</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Agente</th>
                        <th>Operación</th>
                        <th>Fecha Cierre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td><?= $fila['tipo'] ?></td>
                                <td><?= $fila['zona'] ?></td>
                                <td><?= $fila['moneda'] ?> <?= number_format($fila['precio'], 2, '.', ',') ?></td>
                                <td>
                                    <?php
                                        $estado = strtolower($fila['estado']);
                                        $clase = 'bg-disponible';
                                        if ($estado == 'vendido') $clase = 'bg-vendido';
                                        elseif ($estado == 'alquilado') $clase = 'bg-alquilado';
                                    ?>
                                    <span class="badge-estado <?= $clase ?>"><?= $fila['estado'] ?></span>
                                </td>
                                <td><?= $fila['nombre_completo'] ?></td>
                                <td><?= $fila['tipo_operacion'] ?></td>
                                <td><?= $fila['fecha_cierre'] ? date('d/m/Y', strtotime($fila['fecha_cierre'])) : '-' ?></td>
                                <td>
                                    <a href="detalle_propiedad.php?id=<?= $fila['id_propiedad'] ?>" class="btn-accion" title="Ver"><i class="bi bi-eye"></i></a>
                                    
                                    <?php if ($estado == 'disponible'): ?>
                                        <a href="cerrar_operacion.php?id=<?= $fila['id_propiedad'] ?>" class="btn-accion cerrar" title="Cerrar Operación" style="color: #28a745; border-color: #28a745; background-color: #f0fff4;"><i class="bi bi-check2-square"></i></a>    
                                        <?php endif; ?>

                                    <a href="editar_propiedad.php?id=<?= $fila['id_propiedad'] ?>" class="btn-accion" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <a href="eliminar_propiedad.php?id=<?= $fila['id_propiedad'] ?>" class="btn-accion" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar esta propiedad?');"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-4">No hay propiedades registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>