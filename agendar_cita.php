<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Agente') {
    header("Location: login.php");
    exit();
}

$id_agente = $_SESSION['id_usuario'];

$sql_clientes = "SELECT id_cliente, nombre_completo, telefono FROM cliente ORDER BY nombre_completo ASC";
$res_clientes = mysqli_query($conexion, $sql_clientes);

$sql_propiedades = "SELECT id_propiedad, tipo, zona FROM propiedad WHERE id_agente = $id_agente AND estado = 'Disponible' ORDER BY tipo ASC";
$res_propiedades = mysqli_query($conexion, $sql_propiedades);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nueva Cita - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --bg-color: #F5EFEB; --navbar-bg: #3E2A1E; --btn-color: #C1512F; --text-color: #3E2A1E; }
        body { background-color: var(--bg-color); font-family: 'Nunito Sans', sans-serif; color: var(--text-color); }
        .navbar { background-color: var(--navbar-bg); }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 600; }
        
        .alert-custom { background-color: #6B6B43; color: white; border: none; border-radius: 5px; padding: 15px; font-size: 0.95rem; }
        
        .card-form { background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; }
        .form-label { font-weight: 600; color: #3E2A1E; font-size: 0.95rem; }
        .form-control, .form-select { border: 1px solid #ccc; border-radius: 5px; padding: 10px; }
        
        .btn-registrar { background-color: #C1512F; color: white; border: none; font-weight: 600; padding: 12px; border-radius: 5px; }
        .btn-registrar:hover { background-color: #a34225; color: white; }
        .btn-volver { background-color: transparent; color: #C1512F; border: 1px solid #C1512F; font-weight: 600; padding: 8px 20px; border-radius: 5px; text-decoration: none; }
        .btn-volver:hover { background-color: #C1512F; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="panel_agente.php">
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
            <h1 style="color: var(--navbar-bg);">Registrar Nueva Cita</h1>
            <a href="citas_agente.php" class="btn-volver"><i class="bi bi-arrow-left"></i> Volver a Mis Citas</a>
        </div>

        <div class="alert alert-custom mb-4">
            <i class="bi bi-info-circle"></i> Las visitas se agendan entre 9:00 y 18:00. Cada visita bloquea 2 horas del agente: tiempo de traslado, mostrar el inmueble y explicar los detalles.
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-form">
                    <form action="registrar_cita.php" method="POST">
                        
                        <!-- Selección de tipo de cliente -->
                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-person"></i> Cliente</label>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="tipo_cliente" id="cliente_reg" value="registrado" checked onchange="toggleFormulario()">
                                <label class="form-check-label" for="cliente_reg">Cliente registrado</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipo_cliente" id="cliente_whatsapp" value="whatsapp" onchange="toggleFormulario()">
                                <label class="form-check-label" for="cliente_whatsapp">Cliente sin cuenta (WhatsApp)</label>
                            </div>
                        </div>

                        <!-- FORMULARIO 1: Cliente Registrado -->
                        <div id="form_registrado">
                            <div class="mb-4">
                                <select name="id_cliente" class="form-select" id="select_cliente" required>
                                    <option value="">-- Selecciona un cliente --</option>
                                    <?php while($cli = mysqli_fetch_assoc($res_clientes)): ?>
                                        <option value="<?= $cli['id_cliente'] ?>"><?= htmlspecialchars($cli['nombre_completo']) ?> (<?= htmlspecialchars($cli['telefono']) ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div id="form_whatsapp" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <input type="text" name="nombre_manual" class="form-control" placeholder="Nombre del cliente">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="telefono_manual" class="form-control" placeholder="Teléfono (WhatsApp)">
                                </div>
                            </div>
                            <div class="mb-4">
                                <input type="email" name="email_manual" class="form-control" placeholder="Email (opcional)">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-house"></i> Propiedad</label>
                            <select name="id_propiedad" class="form-select" required>
                                <option value="">-- Selecciona una propiedad --</option>
                                <?php while($prop = mysqli_fetch_assoc($res_propiedades)): ?>
                                    <option value="<?= $prop['id_propiedad'] ?>"><?= htmlspecialchars($prop['tipo']) ?> en <?= htmlspecialchars($prop['zona']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-calendar"></i> Fecha de la visita</label>
                                <input type="date" name="fecha_cita" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-clock"></i> Hora de inicio</label>
                                <input type="time" name="hora_inicio" class="form-control" required min="09:00" max="16:00" value="09:00">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-chat-left-text"></i> Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Notas de la visita (opcional)"></textarea>
                        </div>

                        <button type="submit" class="btn-registrar"><i class="bi bi-calendar-check"></i> Registrar Cita</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleFormulario() {
            const formReg = document.getElementById('form_registrado');
            const formWpp = document.getElementById('form_whatsapp');
            const radioReg = document.getElementById('cliente_reg');
            
            const selectCli = document.getElementById('select_cliente');
            const nombreManual = document.querySelector('input[name="nombre_manual"]');
            const telefonoManual = document.querySelector('input[name="telefono_manual"]');

            if (radioReg.checked) {
                formReg.style.display = 'block';
                formWpp.style.display = 'none';
                selectCli.setAttribute('required', 'required');
                nombreManual.removeAttribute('required');
                telefonoManual.removeAttribute('required');
            } else {
                formReg.style.display = 'none';
                formWpp.style.display = 'block';
                selectCli.removeAttribute('required');
                nombreManual.setAttribute('required', 'required');
                telefonoManual.setAttribute('required', 'required');
            }
        }
        toggleFormulario();
    </script>
</body>
</html>