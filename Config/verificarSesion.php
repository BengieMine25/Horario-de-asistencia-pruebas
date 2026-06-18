<?php  
// Iniciar sesión si no está iniciada  
if (session_status() === PHP_SESSION_NONE) {  
    session_start();  
}  
  
// Verificar si el usuario está logueado  
if (!isset($_SESSION['usuario_id'])) {  
    // No está logueado, redirigir al login  
    header("location: Formularios/Login/login.php?error=sesion");  
    exit();  
}  
  
// Función para verificar rol (opcional)  
function verificarRol($roles_permitidos) {  
    if (!in_array($_SESSION['usuario_rol'], $roles_permitidos)) {  
        header("location: Formularios/Login/login.php?error=permisos");  
        exit();  
    }  
}  

// Detectar la ruta base del proyecto automáticamente  
define('BASE_URL', '/Horario-de-asistencia-copia/');    

// Ruta del sistema de archivos (para require/include)  
define('BASE_PATH', dirname(__DIR__) . '/');  