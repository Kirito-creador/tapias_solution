<?php
session_start();
if (isset($_SESSION['rol'])) {
    header("Location: panel.php");
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
    <style>
        body { 
            background-color: #F2EBDD; 
            font-family: 'Nunito Sans', sans-serif; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }
        .card-header {
            background-color: #3E2A1E;
            color: #F2EBDD;
            text-align: center;
            padding: 20px;
        }
        .card-header h3 {
            font-family: 'Playfair Display', serif;
            margin: 0;
        }
        .btn-login {
            background-color: #C1512F;
            color: white;
            border: none;
            font-weight: 600;
        }
        .btn-login:hover {
            background-color: #a34225;
            color: white;
        }
    </style>
</head>
<body>

    <div class="card-login">
        <div class="card-header">
            <h3>Tapia's Solution</h3>
            <small>Acceso al Sistema</small>
        </div>
        <div class="card-body p-4">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger p-2 text-center">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="validar_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold" style="color: #6B6B43;">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="ejemplo@tapiasolution.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold" style="color: #6B6B43;">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-login w-100 py-2">Ingresar al Sistema</button>
            </form>
        </div>
    </div>

</body>
</html>