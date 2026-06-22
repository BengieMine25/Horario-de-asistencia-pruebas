<?php  
session_start();  
  
// Verificar que existe una sesión temporal con el ID del usuario  
if (!isset($_SESSION['temp_usuario_id'])) {  
    // Si no hay sesión temporal, redirigir al login  
    header("location: ../../Formularios/Login/login.php");  
    exit();  
}  
  
include("../../Config/Conexion.php");  
  
// Obtener información del usuario  
$usuario_id = $_SESSION['temp_usuario_id'];  
$sql = "SELECT nombre, apellido, correo FROM usuarios WHERE id = $usuario_id";  
$resultado = $conexion->query($sql);  
$usuario = $resultado->fetch_assoc();  
?>  
<!DOCTYPE html>  
<html lang="es">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>Establecer Contraseña</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">  
    <style>
        body {
            background: #1d439c;
            background: radial-gradient(circle at 50% 100%, #3b6ec5 0%, #153482 70%, #0f2561 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ffffff;
        }

        .login-page {
            max-width: 480px;
            margin: 30px auto;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 24px;
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .brand-logo img {
            max-width: 130px;
            height: auto;
            display: inline-block;
        }

        .page-title {
            color: #ffffff;
            font-size: 1.9rem;
            font-weight: 700;
        }

        .page-subtitle {
            color: #cbd5e1;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        .alert-custom {
            background-color: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.18);
            color: #f8fafc;
        }

        .form-label {
            color: #e2e8f0;
            font-weight: 500;
        }

        .input-group-text {
            background-color: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.92);
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
            transform: translateY(-1px);
        }

        @media (max-width: 576px) {
            .login-page {
                margin: 20px 16px;
            }

            .brand-logo img {
                max-width: 100px;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>  
<body>  
    <div class="container">  
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">  
            <div class="col-12 login-page">  
                <div class="card shadow login-card">  
                    <div class="card-body p-5">  
                        <div class="brand-logo">
                            <img src="../../src/images/logo-uml.png" alt="Logo UML">
                        </div>
                        <h2 class="text-center page-title mb-2">  
                            <i class="bi bi-key"></i> Establecer Contraseña  
                        </h2>  
                        <p class="text-center page-subtitle">Crea tu nueva contraseña para acceder al portal de la Universidad Martín Lutero.</p>  
                          
                        <div class="alert alert-custom" role="alert">  
                            <i class="bi bi-info-circle"></i>   
                            Bienvenido/a, <strong><?php echo $usuario['nombre'] . ' ' . $usuario['apellido']; ?></strong>.   
                            Por favor, establece una contraseña para tu cuenta.  
                        </div>  
                          
                        <?php  
                        // Mostrar mensaje de error si existe  
                        if (isset($_GET['error'])) {  
                            echo '<div class="alert alert-danger" role="alert">';  
                            if ($_GET['error'] == 'no_coinciden') {  
                                echo 'Las contraseñas no coinciden';  
                            } elseif ($_GET['error'] == 'muy_corta') {  
                                echo 'La contraseña debe tener al menos 6 caracteres';  
                            } elseif ($_GET['error'] == 'db') {  
                                echo 'Error al guardar la contraseña. Intenta nuevamente.';  
                            }  
                            echo '</div>';  
                        }  
                        ?>  
                          
                        <form action="../../CRUD/Login/guardarPassword.php" method="post" onsubmit="return validarPassword()">  
                            <input type="hidden" name="UsuarioId" value="<?php echo $usuario_id; ?>">  
                              
                            <div class="mb-3">  
                                <label class="form-label">Correo Electrónico</label>  
                                <input type="email" class="form-control" value="<?php echo $usuario['correo']; ?>" disabled>  
                            </div>  
                              
                            <div class="mb-3">  
                                <label class="form-label">Nueva Contraseña</label>  
                                <div class="input-group">  
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>  
                                    <input type="password" class="form-control" id="password" name="Password" placeholder="Mínimo 6 caracteres" required minlength="6">  
                                </div>  
                            </div>  
                              
                            <div class="mb-3">  
                                <label class="form-label">Confirmar Contraseña</label>  
                                <div class="input-group">  
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>  
                                    <input type="password" class="form-control" id="confirmar_password" name="ConfirmarPassword" placeholder="Repite la contraseña" required minlength="6">  
                                </div>  
                            </div>  
                              
                            <div class="d-grid">  
                                <button type="submit" class="btn btn-login btn-lg">  
                                    <i class="bi bi-check-circle"></i> Establecer Contraseña  
                                </button>  
                            </div>  
                        </form>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  
      
    <script>  
        function validarPassword() {  
            const password = document.getElementById('password').value;  
            const confirmar = document.getElementById('confirmar_password').value;  
              
            if (password !== confirmar) {  
                alert('Las contraseñas no coinciden');  
                return false;  
            }  
              
            if (password.length < 6) {  
                alert('La contraseña debe tener al menos 6 caracteres');  
                return false;  
            }  
              
            return true;  
        }  
    </script>  
      
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>
