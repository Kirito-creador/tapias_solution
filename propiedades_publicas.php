<?php
session_start();
include("conexion.php");


$sql = "SELECT p.*, u.nombre_completo AS agente_nombre,
               (SELECT url_imagen FROM imagen_propiedad WHERE id_propiedad = p.id_propiedad LIMIT 1) AS imagen 
        FROM propiedad p 
        INNER JOIN usuario u ON p.id_agente = u.id_usuario 
        WHERE p.estado = 'Disponible'";


$where = [];
if (!empty($_GET['tipo'])) {
    $tipo = mysqli_real_escape_string($conexion, $_GET['tipo']);
    $where[] = "p.tipo = '$tipo'";
}
if (!empty($_GET['zona'])) {
    $zona = mysqli_real_escape_string($conexion, $_GET['zona']);
    $where[] = "p.zona LIKE '%$zona%'";
}
if (!empty($_GET['precio_min'])) {
    $pmin = floatval($_GET['precio_min']);
    $where[] = "p.precio >= $pmin";
}
if (!empty($_GET['precio_max'])) {
    $pmax = floatval($_GET['precio_max']);
    $where[] = "p.precio <= $pmax";
}

if (count($where) > 0) {
    $sql .= " AND " . implode(" AND ", $where);
}


$orden = $_GET['orden'] ?? 'defecto';
if ($orden == 'precio_asc') $sql .= " ORDER BY p.precio ASC";
elseif ($orden == 'precio_desc') $sql .= " ORDER BY p.precio DESC";
else $sql .= " ORDER BY p.id_propiedad DESC";

$resultado = mysqli_query($conexion, $sql);
$total_propiedades = mysqli_num_rows($resultado);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Propiedades - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }

  
        .filter-bar { background-color: white; border-radius: 8px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); margin-bottom: 30px; }
        .filter-bar .form-label { font-size: 0.85rem; font-weight: 600; color: #6B6B43; }
        .filter-bar .form-control, .filter-bar .form-select { border: 1px solid #ddd; font-size: 0.9rem; }
        

        .prop-card { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s; height: 100%; display: flex; flex-direction: column; }
        .prop-card:hover { transform: translateY(-5px); }
        
        .prop-img-container { height: 200px; overflow: hidden; position: relative; }
        .prop-img-container img { width: 100%; height: 100%; object-fit: cover; }
        .badge-estado { position: absolute; top: 15px; right: 15px; background-color: #6B6B43; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; }
        
        .prop-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .prop-title { font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; margin-bottom: 5px; color: #3E2A1E; }
        .prop-location { font-size: 0.85rem; color: #888; margin-bottom: 10px; }
        .prop-details { font-size: 0.85rem; color: #555; margin-bottom: 15px; display: flex; gap: 15px; flex-wrap: wrap; }
        .prop-price { font-size: 1.2rem; font-weight: 700; color: #3E2A1E; margin-bottom: 15px; }
        
        .tag-operacion { background-color: #3E2A1E; color: white; font-size: 0.75rem; padding: 3px 10px; border-radius: 3px; display: inline-block; align-self: flex-start; margin-bottom: 15px; }
        
        .btn-detalle { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 10px; border-radius: 5px; width: 100%; margin-top: auto; text-decoration: none; text-align: center; transition: 0.3s; }
        .btn-detalle:hover { background-color: #a34225; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['rol'])): ?>
                    <a class="nav-link" href="<?= ($_SESSION['rol'] == 'Agente') ? 'panel_agente.php' : 'panel.php' ?>"><i class="bi bi-speedometer2"></i> Mi Panel</a>
                    <span class="nav-link me-3">Hola, <?= $_SESSION['nombre'] ?></span>
                    <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h1 style="color: var(--navbar-bg); margin: 0;">Catálogo de Propiedades</h1>
            <span class="text-muted" style="font-size: 0.9rem;"><?= $total_propiedades ?> propiedades en todas las zonas</span>
        </div>

        <!-- Barra de Filtros -->
        <div class="filter-bar">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="Casa" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Casa') ? 'selected' : '' ?>>Casa</option>
                        <option value="Departamento" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Departamento') ? 'selected' : '' ?>>Departamento</option>
                        <option value="Terreno" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'Terreno') ? 'selected' : '' ?>>Terreno</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Zona</label>
                    <input type="text" name="zona" class="form-control" placeholder="Todas" value="<?= $_GET['zona'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Precio mín.</label>
                    <input type="number" name="precio_min" class="form-control" placeholder="0" value="<?= $_GET['precio_min'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Precio máx.</label>
                    <input type="number" name="precio_max" class="form-control" placeholder="Max" value="<?= $_GET['precio_max'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Orden</label>
                    <select name="orden" class="form-select">
                        <option value="defecto">Defecto</option>
                        <option value="precio_asc" <?= (isset($_GET['orden']) && $_GET['orden'] == 'precio_asc') ? 'selected' : '' ?>>Menor a Mayor</option>
                        <option value="precio_desc" <?= (isset($_GET['orden']) && $_GET['orden'] == 'precio_desc') ? 'selected' : '' ?>>Mayor a Menor</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn" style="background-color: #C1512F; color: white; font-weight: 600; flex-grow: 1;"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="propiedades_publicas.php" class="btn" style="background-color: transparent; border: 1px solid #C1512F; color: #C1512F; font-weight: 600;">Limpiar</a>
                </div>
            </form>
        </div>

        <!-- Grid de Propiedades -->
        <div class="row g-4">
            <?php if ($total_propiedades > 0): ?>
                <?php while ($prop = mysqli_fetch_assoc($resultado)): ?>
                    <div class="col-md-4">
                        <div class="prop-card">
                            <div class="prop-img-container">
                                <!-- Si la propiedad tiene imagen, la muestra, si no, una por defecto -->
                                <?php $imagen = $prop['imagen'] ? $prop['imagen'] : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'; ?>
                                <img src="<?= $imagen ?>" alt="<?= $prop['tipo'] ?>">
                                <span class="badge-estado"><?= $prop['estado'] ?></span>
                            </div>
                            <div class="prop-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h3 class="prop-title"><?= $prop['tipo'] ?></h3>
                                </div>
                                <div class="prop-location"><i class="bi bi-geo-alt"></i> <?= $prop['zona'] ?></div>
                                <div class="prop-details">
                                    <span><i class="bi bi-aspect-ratio"></i> <?= number_format($prop['superficie_m2'], 0) ?> m²</span>
                                    <span><i class="bi bi-door-open"></i> <?= $prop['habitaciones'] ?></span>
                                    <span><i class="bi bi-droplet"></i> <?= $prop['banos'] ?></span>
                                </div>
                                <div class="prop-price"><?= $prop['moneda'] ?> <?= number_format($prop['precio'], 2, '.', ',') ?></div>
                                <div class="tag-operacion"><?= $prop['tipo_operacion'] ?></div>
                                <a href="detalle_propiedad_publica.php?id=<?= $prop['id_propiedad'] ?>" class="btn-detalle"><i class="bi bi-eye"></i> Ver Detalle</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h3 class="text-muted">No se encontraron propiedades que coincidan con tu búsqueda.</h3>
                    <a href="propiedades_publicas.php" class="btn btn-outline-dark mt-3">Ver todas las propiedades</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>