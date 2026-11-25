<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) { header("Location: propiedades.php"); exit(); }
$id_propiedad = intval($_GET['id']);

$sql = "SELECT * FROM propiedad WHERE id_propiedad = $id_propiedad";
$res = mysqli_query($conexion, $sql);
$prop = mysqli_fetch_assoc($res);

if (!$prop) { die("Propiedad no encontrada."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cerrar Operación - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #F2EBDD; font-family: 'Nunito Sans', sans-serif; color: #3E2A1E; }
        .navbar { background-color: #3E2A1E; }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        
        .card-form { background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; }
        .form-label { font-weight: 600; color: #3E2A1E; font-size: 0.95rem; }
        .form-control, .form-select { border: 1px solid #ccc; border-radius: 5px; padding: 10px; }
        
        .info-prop { font-size: 1rem; color: #555; margin-bottom: 5px; }
        .info-precio { font-weight: 700; color: #3E2A1E; font-size: 1.1rem; margin-bottom: 20px; }

        .btn-cancelar { background-color: #6B6B43; color: white; border: none; font-weight: 600; padding: 10px 20px; border-radius: 5px; text-decoration: none; }
        .btn-cancelar:hover { background-color: #555533; color: white; }
        
        .btn-confirmar { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 10px 20px; border-radius: 5px; }
        .btn-confirmar:hover { background-color: #a34225; color: white; }
        
        .btn-volver { background-color: transparent; color: #C1512F; border: 1px solid #C1512F; font-weight: 600; padding: 8px 20px; border-radius: 5px; text-decoration: none; }
        .btn-volver:hover { background-color: #C1512F; color: white; }

        .panel-comisiones { background-color: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 5px; padding: 15px; margin-top: 15px; display: none; }
        .panel-comisiones h6 { font-weight: 700; color: #3E2A1E; margin-bottom: 10px; }
        .panel-comisiones p { margin-bottom: 5px; font-size: 0.95rem; }
        .text-empresa { color: #3E2A1E; font-weight: 600; }
        .text-agente { color: #6B6B43; font-weight: 600; }
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
                <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: #3E2A1E;">Cerrar Operación</h1>
            <a href="propiedades.php" class="btn-volver"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-form">
                    
                    <div class="info-prop">Propiedad: <strong><?= $prop['tipo'] ?> en <?= $prop['zona'] ?></strong></div>
                    <div class="info-precio">Precio publicado: <?= $prop['moneda'] ?> <?= number_format($prop['precio'], 2, '.', ',') ?></div>

                    <form action="procesar_cierre.php" method="POST">
                        <input type="hidden" name="id_propiedad" value="<?= $id_propiedad ?>">
                        <input type="hidden" name="precio_publicado" value="<?= $prop['precio'] ?>">

                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-tag"></i> Tipo de Operación</label>
                            <select name="tipo_operacion_cierre" id="tipo_operacion" class="form-select" required onchange="calcularComisiones()">
                                <option value="">-- Selecciona --</option>
                                <option value="Venta">Venta (5% empresa, 1% agente)</option>
                                <option value="Alquiler">Alquiler (5% empresa, 1% agente)</option>
                                <option value="Anticretico">Anticrético (3% empresa, 0.5% agente)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-currency-dollar"></i> Precio Final</label>
                            <input type="number" name="precio_final" id="precio_final" class="form-control" required min="1" step="0.01" value="<?= $prop['precio'] ?>" oninput="calcularComisiones()">
                        </div>

                        <div id="panel_comisiones" class="panel-comisiones">
                            <h6>Cálculo de Comisiones:</h6>
                            <p>Comisión Empresa: <span class="text-empresa" id="txt_empresa">$0.00</span></p>
                            <p>Comisión Agente: <span class="text-agente" id="txt_agente">$0.00</span></p>
                        </div>

                        <div class="mb-4 mt-4">
                            <label class="form-label"><i class="bi bi-chat-left-text"></i> Observaciones (opcional)</label>
                            <textarea name="observaciones" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                            <a href="propiedades.php" class="btn-cancelar">Cancelar</a>
                            <button type="submit" class="btn-confirmar"><i class="bi bi-check-circle"></i> Confirmar Cierre</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function calcularComisiones() {
            const tipoOp = document.getElementById('tipo_operacion').value;
            const precioFinal = parseFloat(document.getElementById('precio_final').value) || 0;
            const panel = document.getElementById('panel_comisiones');
            const txtEmpresa = document.getElementById('txt_empresa');
            const txtAgente = document.getElementById('txt_agente');

            if (tipoOp && precioFinal > 0) {
                panel.style.display = 'block';
                let porcEmpresa = 0;
                let porcAgente = 0;

                if (tipoOp === 'Venta' || tipoOp === 'Alquiler') {
                    porcEmpresa = 0.05; // 5%
                    porcAgente = 0.01;  // 1%
                } else if (tipoOp === 'Anticretico') {
                    porcEmpresa = 0.03; // 3%
                    porcAgente = 0.005; // 0.5%
                }

                const comEmpresa = precioFinal * porcEmpresa;
                const comAgente = precioFinal * porcAgente;

                txtEmpresa.textContent = '$' + comEmpresa.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                txtAgente.textContent = '$' + comAgente.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                panel.style.display = 'none';
            }
        }
        calcularComisiones();
    </script>
</body>
</html>