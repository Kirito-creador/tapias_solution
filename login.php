<?php
session_start();
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] == 'Cliente') { header("Location: panel_cliente.php"); }
    elseif ($_SESSION['rol'] == 'Agente') { header("Location: panel_agente.php"); }
    else { header("Location: panel.php"); }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #5a2e1f 0%, #a74e2c 50%, #3d1e12 100%);
            font-family: 'Nunito Sans', sans-serif; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .card-login {
            background-color: #f0e8d8;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-icon {
            font-size: 3rem;
            color: #3E2A1E;
            line-height: 1;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: #3E2A1E;
            margin-top: 5px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 600;
            color: #3E2A1E;
            font-size: 0.9rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px 15px;
            background-color: #fcfaf7;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(193, 81, 47, 0.25);
            border-color: #C1512F;
        }
        .btn-login {
            background-color: #C1512F;
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: #a34225;
            color: white;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #3E2A1E;
        }
        .register-link a {
            color: #C1512F;
            font-weight: 700;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="card-login">
        <div class="logo-container">
            <div class="logo-icon"><i class="bi bi-house-door-fill"></i></div>
            <div class="logo-text">Tapia's Solution</div>
            <div class="subtitle">Portal Inmobiliario</div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger p-2 text-center" style="font-size: 0.9rem;">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="validar_login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required placeholder="ejemplo@tapiasolution.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="********">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember" style="font-size: 0.85rem;">Recordarme</label>
            </div>
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <div class="register-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>

</body>
</html>