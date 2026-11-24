<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'Administrador' && $_SESSION['rol'] != 'Agente')) {
    header("Location: login.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = mysqli_real_escape_string($conexion, $_POST['tipo']);
    $tipo_operacion = mysqli_real_escape_string($conexion, $_POST['tipo_operacion']);
    $precio = floatval($_POST['precio']);
    $moneda = mysqli_real_escape_string($conexion, $_POST['moneda']);
    $zona = mysqli_real_escape_string($conexion, $_POST['zona']);
    $direccion = mysqli_real_escape_string($conexion, $_POST['direccion']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    
    $habitaciones = $_POST['habitaciones'] !== '' ? intval($_POST['habitaciones']) : 0;
    $banos = $_POST['banos'] !== '' ? intval($_POST['banos']) : 0;
    $superficie = floatval($_POST['superficie_m2']);
    $id_agente = $_SESSION['id_usuario']; 

    if ($tipo != 'Terreno') {
        if (trim($_POST['habitaciones']) === '' || trim($_POST['banos']) === '') {
            $error .= "Para Casas o Departamentos, debes indicar la cantidad de Habitaciones y Baños.<br>";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO propiedad (tipo, tipo_operacion, precio, moneda, zona, direccion, descripcion, habitaciones, banos, superficie_m2, id_agente) 
                VALUES ('$tipo', '$tipo_operacion', $precio, '$moneda', '$zona', '$direccion', '$descripcion', $habitaciones, $banos, $superficie, $id_agente)";
        
        if(mysqli_query($conexion, $sql)){
            $id_propiedad = mysqli_insert_id($conexion);

            if (isset($_FILES['imagenes']) && $_FILES['imagenes']['error'][0] != UPLOAD_ERR_NO_FILE) {
                $total_imagenes = count($_FILES['imagenes']['name']);
                $formatos_permitidos = ['image/jpeg', 'image/jpg', 'image/png'];
                $peso_maximo = 5 * 1024 * 1024;

                for ($i = 0; $i < $total_imagenes; $i++) {
                    if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                        $tipo_mime = $_FILES['imagenes']['type'][$i];
                        $peso_archivo = $_FILES['imagenes']['size'][$i];
                        $nombre_archivo = $_FILES['imagenes']['name'][$i];

                        if (!in_array($tipo_mime, $formatos_permitidos)) {
                            $error .= "El archivo '$nombre_archivo' no es un formato válido (Solo JPG, JPEG, PNG).<br>";
                            continue;
                        }
                        if ($peso_archivo > $peso_maximo) {
                            $error .= "El archivo '$nombre_archivo' supera el límite de 5MB.<br>";
                            continue;
                        }

                        $contenido = file_get_contents($_FILES['imagenes']['tmp_name'][$i]);
                        $base64 = base64_encode($contenido);
                        $url_imagen = "data:$tipo_mime;base64,$base64";
                        $es_principal = ($i === 0) ? 1 : 0;
                        
                        $sql_img = "INSERT INTO imagen_propiedad (url_imagen, es_principal, id_propiedad) 
                                    VALUES ('$url_imagen', $es_principal, $id_propiedad)";
                        if (!mysqli_query($conexion, $sql_img)) {
                            $error .= "Error al guardar imagen: " . mysqli_error($conexion) . "<br>";
                        }
                    }
                }
            }

            if (empty($error)) {
                if($_SESSION['rol'] == 'Agente') {
                    header("Location: propiedades_agente.php");
                } else {
                    header("Location: propiedades.php");
                }
                exit();
            }
        } else {
            $error = "Error al registrar propiedad: " . mysqli_error($conexion);
        }
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
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        .card-form { background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; }
        .form-label { font-weight: 600; }
        .btn-registrar { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 10px 20px; border-radius: 5px; }
        .btn-cancelar { background-color: #6B6B43; color: white; border: none; font-weight: 600; padding: 10px 20px; border-radius: 5px; }
        .btn-volver { background-color: transparent; color: #C1512F; border: 1px solid #C1512F; font-weight: 600; padding: 8px 20px; border-radius: 5px; text-decoration: none; }
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

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-form">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-house"></i> Tipo de Propiedad</label>
                                <select name="tipo" id="tipoPropiedad" class="form-select" required onchange="toggleCampos()">
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
                                <input type="number" name="habitaciones" id="inputHabitaciones" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-droplet"></i> Baños</label>
                                <input type="number" name="banos" id="inputBanos" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-bounding-box"></i> Superficie (m²)</label>
                                <input type="number" name="superficie_m2" step="0.01" class="form-control" placeholder="0" required min="0">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-images"></i> Imágenes de la Propiedad</label>
                            <input type="file" name="imagenes[]" class="form-control" multiple accept="image/jpeg, image/jpg, image/png">
                            <small class="text-muted" style="font-size: 0.8rem;">Puedes seleccionar varias imágenes a la vez (JPG, JPEG, PNG. Máximo 5MB por foto). La primera será la principal.</small>
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
    
    <script>
        function toggleCampos() {
            const tipo = document.getElementById('tipoPropiedad').value;
            const inputHabitaciones = document.getElementById('inputHabitaciones');
            const inputBanos = document.getElementById('inputBanos');

            if (tipo === 'Terreno') {
                inputHabitaciones.removeAttribute('required');
                inputBanos.removeAttribute('required');
                inputHabitaciones.removeAttribute('readonly');
                inputBanos.removeAttribute('readonly');
                if (inputHabitaciones.value === '') inputHabitaciones.value = 0;
                if (inputBanos.value === '') inputBanos.value = 0;
            } else {
                inputHabitaciones.setAttribute('required', 'true');
                inputBanos.setAttribute('required', 'true');
                if (inputHabitaciones.value === '0') inputHabitaciones.value = '';
                if (inputBanos.value === '0') inputBanos.value = '';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>