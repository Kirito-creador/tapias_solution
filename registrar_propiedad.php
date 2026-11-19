<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibir y limpiar datos del formulario
    $tipo = mysqli_real_escape_string($conexion, $_POST['tipo']);
    $tipo_operacion = mysqli_real_escape_string($conexion, $_POST['tipo_operacion']);
    $precio = floatval($_POST['precio']);
    $moneda = mysqli_real_escape_string($conexion, $_POST['moneda']);
    $zona = mysqli_real_escape_string($conexion, $_POST['zona']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $habitaciones = intval($_POST['habitaciones']);
    $banos = intval($_POST['banos']);
    $superficie = floatval($_POST['superficie_m2']);
    $id_agente = $_SESSION['id_usuario']; 

    // 2. Insertar la propiedad
    $sql = "INSERT INTO propiedad (tipo, tipo_operacion, precio, moneda, zona, direccion, descripcion, habitaciones, banos, superficie_m2, id_agente) 
            VALUES ('$tipo', '$tipo_operacion', $precio, '$moneda', '$zona', '$direccion', '$descripcion', $habitaciones, $banos, $superficie, $id_agente)";
    
    if(mysqli_query($conexion, $sql)){
        $id_propiedad = mysqli_insert_id($conexion); // Obtenemos el ID de la propiedad recién creada

        // 3. Procesar las imágenes subidas
        if (isset($_FILES['imagenes']) && $_FILES['imagenes']['error'][0] != UPLOAD_ERR_NO_FILE) {
            $total_imagenes = count($_FILES['imagenes']['name']);
            $carpeta_destino = "uploads/";

            // Si la carpeta no existe, la creamos
            if (!is_dir($carpeta_destino)) {
                mkdir($carpeta_destino, 0777, true);
            }

            for ($i = 0; $i < $total_imagenes; $i++) {
                $nombre_archivo = $_FILES['imagenes']['name'][$i];
                $ruta_temporal = $_FILES['imagenes']['tmp_name'][$i];
                $error = $_FILES['imagenes']['error'][$i];
                
                // Validar que no haya errores y que el archivo sea una imagen
                if ($error === UPLOAD_ERR_OK) {
                    $info_archivo = getimagesize($ruta_temporal);
                    if ($info_archivo !== false) {
                        // Generar un nombre único para evitar sobreescribir imágenes con el mismo nombre
                        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
                        $nuevo_nombre = uniqid('prop_') . '.' . $extension;
                        $ruta_final = $carpeta_destino . $nuevo_nombre;

                        // Mover el archivo de la carpeta temporal a la carpeta uploads
                        if (move_uploaded_file($ruta_temporal, $ruta_final)) {
                            // Guardar la ruta en la base de datos (tabla imagen_propiedad)
                            $es_principal = ($i === 0) ? 1 : 0; // La primera imagen será la principal
                            $sql_img = "INSERT INTO imagen_propiedad (url_imagen, descripcion, es_principal, id_propiedad) 
                                        VALUES ('$ruta_final', NULL, $es_principal, $id_propiedad)";
                            
                            // Ejecutar y verificar si hubo un error en la base de datos
                            if (!mysqli_query($conexion, $sql_img)) {
                                // Si falla, detenemos todo y mostramos el error de SQL
                                die("Error al guardar la imagen en la BD: " . mysqli_error($conexion) . "<br>Verifica la estructura de tu tabla imagen_propiedad.");
                            }
                        }
                    }
                }
            }
        }

        // Redirigir según el rol
        if($_SESSION['rol'] == 'Agente') {
            header("Location: propiedades_agente.php");
        } else {
            header("Location: propiedades.php");
        }
        exit();
    } else {
        $error = "Error al registrar la propiedad: " . mysqli_error($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Propiedad - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }

        .card-form { background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; }
        .form-label { font-weight: 600; color: #3E2A1E; font-size: 0.9rem; margin-bottom: 5px; }
        .form-control, .form-select { border: 1px solid #ccc; border-radius: 5px; padding: 10px; font-size: 0.95rem; }
        
        .input-group-text { background-color: transparent; border-right: none; color: #6B6B43; }
        .form-control.with-icon, .form-select.with-icon { border-left: none; }

        .btn-cancelar { background-color: #6B6B43; color: white; border: none; font-weight: 600; padding: 10px 20px; border-radius: 5px; text-decoration: none; }
        .btn-cancelar:hover { background-color: #555533; color: white; }
        
        .btn-registrar { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 10px 20px; border-radius: 5px; }
        .btn-registrar:hover { background-color: #a34225; color: white; }
        
        .btn-volver { background-color: transparent; color: #C1512F; border: 1px solid #C1512F; font-weight: 600; padding: 8px 20px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;}
        .btn-volver:hover { background-color: #C1512F; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: #3E2A1E;">Registrar Nueva Propiedad</h1>
            <a href="<?= ($_SESSION['rol'] == 'Agente') ? 'propiedades_agente.php' : 'propiedades.php' ?>" class="btn-volver"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-form">
                    <!-- IMPORTANTE: enctype para subir archivos -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-house"></i> Tipo de Propiedad</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="">-- Selecciona --</option>
                                    <option value="Casa">Casa</option>
                                    <option value="Departamento">Departamento</option>
                                    <option value="Terreno">Terreno</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-tag"></i> Tipo de Operación</label>
                                <select name="tipo_operacion" class="form-select" required>
                                    <option value="Venta">Venta</option>
                                    <option value="Alquiler">Alquiler</option>
                                    <option value="Anticretico">Anticrético</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-currency-dollar"></i> Precio</label>
                            <input type="number" name="precio" class="form-control" required step="0.01" min="0" placeholder="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-cash"></i> Moneda</label>
                            <select name="moneda" class="form-select">
                                <option value="USD">USD</option>
                                <option value="Bs">Bs.</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-geo-alt"></i> Zona / Barrio</label>
                            <input type="text" name="zona" class="form-control" required placeholder="Ej: Calacoto">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-pin-map"></i> Dirección</label>
                            <input type="text" name="direccion" class="form-control" required placeholder="Ej: Calle 15 #456">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-card-text"></i> Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Describe la propiedad..."></textarea>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-door-closed"></i> Habitaciones</label>
                                <input type="number" name="habitaciones" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-droplet"></i> Baños</label>
                                <input type="number" name="banos" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-bounding-box"></i> Superficie (m²)</label>
                                <input type="number" name="superficie_m2" step="0.01" class="form-control" placeholder="0">
                            </div>
                        </div>

                        <!-- Sección de Imágenes -->
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-images"></i> Imágenes de la Propiedad</label>
                            <input type="file" name="imagenes[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted" style="font-size: 0.8rem;">Puedes seleccionar varias imágenes a la vez (JPG, PNG). La primera será la principal.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                            <a href="<?= ($_SESSION['rol'] == 'Agente') ? 'propiedades_agente.php' : 'propiedades.php' ?>" class="btn-cancelar">Cancelar</a>
                            <button type="submit" class="btn-registrar"><i class="bi bi-check-circle"></i> Registrar Propiedad</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>