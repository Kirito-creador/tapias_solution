<?php
session_start();
include("conexion.php"); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Tapia's Solution</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-color: #F2EBDD;
            --navbar-bg: #3E2A1E;
            --btn-color: #C1512F;
            --text-color: #3E2A1E;
            --olive-bg: #6B6B43;
        }
        
        body { 
            background-color: var(--bg-color); 
            font-family: 'Nunito Sans', sans-serif; 
            color: var(--text-color); 
        }
        
        h1, h2, h3, h4 { 
            font-family: 'Playfair Display', serif; 
            font-weight: 600; 
        }

        /* Navbar */
        .navbar { background-color: var(--navbar-bg); }
        .navbar-brand, .nav-link { color: #F2EBDD !important; }

        /* ========================================== */
        /* CARRUSEL DE FONDO (HERO) */
        /* ========================================== */
        .hero-carousel {
            position: relative;
            height: 600px;
            overflow: hidden;
        }
        .hero-carousel .carousel-item {
            height: 600px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        /* Capa oscura semitransparente para que el texto se lea */
        .hero-carousel .carousel-item::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(62, 42, 30, 0.6);
            z-index: 1;
        }
        .carousel-caption {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            text-align: center;
            width: 100%;
            max-width: 800px;
            bottom: auto;
        }
        .hero-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: #F2EBDD;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            display: inline-block;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .hero-title {
            font-size: 3.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
            color: white;
        }
        .hero-subtitle {
            font-size: 1.1rem;
            margin: 0 auto 40px auto;
            color: #f0e6df;
        }
        .btn-hero-primary {
            background-color: var(--btn-color);
            color: white;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-hero-primary:hover { background-color: #a34225; color: white; }
        .btn-hero-secondary {
            background-color: transparent;
            color: white;
            border: 1px solid white;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s;
            margin-left: 10px;
        }
        .btn-hero-secondary:hover { background-color: white; color: var(--text-color); }

        /* Servicios */
        .section-title { text-align: center; margin-bottom: 40px; margin-top: 60px;}
        .section-title span { color: var(--btn-color); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 700; display: block; margin-bottom: 10px; }
        
        .service-card {
            background-color: white;
            border-radius: 10px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            height: 100%;
            transition: 0.3s;
        }
        .service-card:hover { transform: translateY(-5px); }
        
        .service-icon {
            width: 70px;
            height: 70px;
            background-color: var(--btn-color);
            color: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px auto;
        }
        
        .service-card h4 { font-size: 1.3rem; margin-bottom: 15px; }
        .service-card p { color: #666; font-size: 0.95rem; }

        /* Trayectoria */
        .stats-section {
            background-color: #2b1e16;
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-top: 60px;
        }
        .stats-section h2 { margin-bottom: 10px; }
        .stats-section .subtitle { color: var(--btn-color); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 700; margin-bottom: 50px; }
        
        .stat-item h3 { font-size: 3rem; color: var(--btn-color); margin-bottom: 5px; }
        .stat-item p { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #ccc; margin: 0; }

        /* CTA Final */
        .cta-section {
            background-color: var(--olive-bg);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .cta-section h2 { font-size: 2.5rem; margin-bottom: 15px; }
        .cta-section p { font-size: 1.1rem; margin-bottom: 30px; color: #e0e0c0; }
        
        .btn-cta {
            background-color: var(--btn-color);
            color: white;
            border: none;
            padding: 15px 40px;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }
        .btn-cta:hover { background-color: #a34225; color: white; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-house-door-fill" style="font-size: 1.5rem; color: #8FBC8F; margin-right: 10px;"></i>
                Tapia's Solution
            </a>
            <div class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['rol'])): ?>
                    <a class="nav-link me-3" href="panel.php"><i class="bi bi-speedometer2"></i> Ir al Panel</a>
                    <span class="nav-link me-3">Hola, <strong><?= $_SESSION['nombre'] ?></strong></span>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
                <?php else: ?>
                    <a class="nav-link me-3" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- ========================================== -->
    <!-- CARRUSEL DE FONDO (HERO) -->
    <!-- ========================================== -->
    <div id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <!-- Diapositiva 1: Cambia 'fondo1.jpg' por tu imagen -->
            <div class="carousel-item active" style="background-image: url('fondo1.jpg');">
                <div class="carousel-caption">
                    <div class="hero-badge">Bienvenido a Tapia's Solution</div>
                    <h1 class="hero-title">Encuentra tu hogar ideal en<br>Cochabamba</h1>
                    <p class="hero-subtitle">Casas, departamentos y terrenos con la mejor ubicación y asesoría personalizada.</p>
                    <div>
                        <a href="propiedades_publicas.php" class="btn-hero-primary"><i class="bi bi-house"></i> Ver Propiedades</a>
                        <a href="registro.php" class="btn-hero-secondary"><i class="bi bi-person-plus"></i> Registrarse</a>
                    </div>
                </div>
            </div>
            
            <!-- Diapositiva 2: Cambia 'fondo2.jpg' por tu imagen -->
            <div class="carousel-item" style="background-image: url('fondo2.jpg');">
                <div class="carousel-caption">
                    <div class="hero-badge">Inversión Inteligente</div>
                    <h1 class="hero-title">Los mejores terrenos y casas</h1>
                    <p class="hero-subtitle">Invierte en tu futuro con las propiedades más exclusivas de la ciudad.</p>
                    <div>
                        <a href="propiedades_publicas.php" class="btn-hero-primary"><i class="bi bi-house"></i> Ver Propiedades</a>
                        <a href="registro.php" class="btn-hero-secondary"><i class="bi bi-person-plus"></i> Registrarse</a>
                    </div>
                </div>
            </div>

            <!-- Diapositiva 3: Cambia 'fondo3.jpg' por tu imagen -->
            <div class="carousel-item" style="background-image: url('fondo3.jpg');">
                <div class="carousel-caption">
                    <div class="hero-badge">Asesoría Personalizada</div>
                    <h1 class="hero-title">Te acompañamos en cada paso</h1>
                    <p class="hero-subtitle">Nuestro equipo de expertos te guiará en todo el proceso legal y financiero.</p>
                    <div>
                        <a href="propiedades_publicas.php" class="btn-hero-primary"><i class="bi bi-house"></i> Ver Propiedades</a>
                        <a href="registro.php" class="btn-hero-secondary"><i class="bi bi-person-plus"></i> Registrarse</a>
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- Sección de Servicios -->
    <section class="container mb-5">
        <div class="section-title">
            <span>Servicios</span>
            <h2>Nuestros Servicios</h2>
            <p class="text-muted">Te acompañamos en cada paso de tu búsqueda</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="bi bi-house-door"></i></div>
                    <h4>Venta de Inmuebles</h4>
                    <p>Encuentra la propiedad perfecta para ti y tu familia con las mejores opciones del mercado.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="bi bi-key"></i></div>
                    <h4>Alquiler y Anticrético</h4>
                    <p>Opciones flexibles de alquiler y anticrético para que puedas mudarte sin complicaciones.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="service-card">
                    <div class="service-icon"><i class="bi bi-clipboard-check"></i></div>
                    <h4>Asesoría Personalizada</h4>
                    <p>Nuestro equipo de agentes expertos te guiará en todo el proceso legal y financiero.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Trayectoria -->
    <section class="stats-section">
        <div class="container">
            <h2>Nuestra Trayectoria</h2>
            <div class="subtitle">Resultados que nos respaldan</div>
            
            <div class="row mt-4">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <h3>100+</h3>
                        <p>Propiedades</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <h3>50+</h3>
                        <p>Clientes Satisfechos</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <h3>10+</h3>
                        <p>Años de Experiencia</p>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-item">
                        <h3>100%</h3>
                        <p>Compromiso</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección CTA Final -->
    <section class="cta-section">
        <div class="container">
            <h2>¿Listo para encontrar tu próximo hogar?</h2>
            <p>Contáctanos y uno de nuestros agentes te atenderá personalmente.</p>
            <a href="https://wa.me/59169172830" target="_blank" class="btn-cta">
                <i class="bi bi-whatsapp"></i> Comenzar Ahora
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php"); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>