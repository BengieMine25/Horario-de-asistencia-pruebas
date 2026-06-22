<?php  
session_start();  
  
// Si ya está logueado, redirigir según rol  
if (isset($_SESSION['usuario_id'])) {  
    if ($_SESSION['usuario_rol'] == 'Administrador') {  
        header("location:../../index.php");  
    } elseif ($_SESSION['usuario_rol'] == 'Oficina') {  
        header("location:../../pages/empleado.php");  
    } else {  // Empleado  
        header("location:../../pages/perfil_empleado.php");  
    }  
    exit();  
}
?>  
<!DOCTYPE html>  
<html lang="es">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>Inicio de Sesión</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">  
</head>
    <style>
        body {
            background: #1d439c;
            background: radial-gradient(circle at 50% 100%, #3b6ec5 0%, #153482 70%, #0f2561 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ffffff;
        }



        .login-container {
            max-width: 450px;
            width: 100%;
            margin: 50px auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
        }

        .brand-title {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #ffffff;
        }

        .brand-subtitle {
            font-size: 0.9rem;
            color: #b0c4de;
            font-weight: 500;
        }

        .portal-title {
            font-size: 2.4rem;
            font-weight: 800;
            color: #ffffff;
        }

        .portal-desc {
            font-size: 1rem;
            color: #cbd5e1;
            font-weight: 300;
            line-height: 1.5;
        }

            .brand-logo {
                margin-bottom: 1rem;
            }

            .brand-logo img {
                max-width: 140px;
                height: auto;
                display: block;
                margin: 0 auto 0.5rem auto;
            }
        .form-label {
            color: #e2e8f0;
            font-weight: 500;
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid transparent;
            color: #1a1a1a;
        }

        .form-control:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
        }

        .btn-login {
            background-color: #ffffff;
            color: #153482;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #e2e8f0;
            color: #0f2561;
            transform: translateY(-2px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-container {
                max-width: 360px;
                margin: 20px auto;
                padding: 0 12px;
            }

            .brand-logo img {
                max-width: 100px;
            }

            .brand-title {
                font-size: 1.1rem;
            }

            .portal-title {
                font-size: 1.6rem;
            }

            .portal-desc {
                font-size: 0.95rem;
            }

            .card-body {
                padding: 1rem;
            }

            .btn-login {
                font-size: 0.95rem;
                padding: 0.6rem 1rem;
            }
        }

        @media (min-width: 992px) {
            .brand-logo img {
                max-width: 160px;
            }

            .login-container {
                max-width: 520px;
            }

            .portal-title {
                font-size: 2.6rem;
            }
        }
    </style>

<body>  
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">  
        <div class="login-container text-center px-3">  
            
            <div class="mb-4">
                <div class="brand-logo">
                    <img src="../../src/images/logo-uml.png" alt="Logo UML">
                </div>
                <h1 class="brand-title mb-0">Universidad Martín</h1>
                <h1 class="brand-title mb-1" style="color: #ffffff; font-weight: 900;">Lutero</h1>
                <p class="brand-subtitle">Portal de Asistencia y Personal</p>
            </div>

            <h2 class="portal-title mb-3">Portal de Asistencia</h2>
            <p class="portal-desc mb-5">Acceso exclusivo para personal docente y administrativo de la Universidad Martín Lutero.</p>

            <div class="card shadow-lg text-start">  
                <div class="card-body p-4 p-md-5">  
                    <h3 class="text-center mb-4 text-white fs-4">  
                        <i class="bi bi-person-circle"></i> Iniciar Sesión  
                    </h3>  
                      
                    <?php  
                    // Mostrar mensaje de error si existe  
                    if (isset($_GET['error'])) {  
                        echo '<div class="alert alert-danger" role="alert">';  
                        if ($_GET['error'] == 'credenciales') {  
                            echo 'Correo o contraseña incorrectos';  
                        } elseif ($_GET['error'] == 'sesion') {  
                            echo 'Debe iniciar sesión para acceder';  
                        }  
                        echo '</div>';  
                    }  
                    ?>  
                      
                    <form action="../../CRUD/Login/validarLogin.php" method="post">  
                        <div class="mb-3">  
                            <label class="form-label">Correo Electrónico</label>  
                            <div class="input-group">  
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>  
                                <input type="email" class="form-control" name="Correo" placeholder="usuario@ejemplo.com" required autofocus>  
                            </div>  
                        </div>  
                          
                        <div class="mb-3">  
                            <label class="form-label">Contraseña</label>  
                            <div class="input-group">  
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>  
                                <input type="password" class="form-control" name="Password" placeholder="••••••••" required>  
                            </div>  
                        </div>  
                          
                        <div class="d-grid mt-4">  
                            <button type="submit" class="btn btn-login btn-lg">  
                                <i class="bi bi-box-arrow-in-right"></i> Ingresar  
                            </button>  
                        </div>  
                    </form>  
                </div>  
            </div>  
        </div>  
    </div>  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>