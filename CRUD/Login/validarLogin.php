<?php
session_start();
include("../../Config/Conexion.php");

$correo = $_POST['Correo'];
$password = $_POST['Password'];

// Usar prepared statements para prevenir inyección SQL  
$sql = "SELECT id, nombre, apellido, correo, rol_sistema, password     
        FROM usuarios     
        WHERE correo = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();

    // Verificar si el usuario tiene contraseña establecida    
    if ($usuario['password'] === NULL || $usuario['password'] === '') {
        // Usuario sin contraseña - redirigir a establecer contraseña    
        $_SESSION['temp_usuario_id'] = $usuario['id'];
        header("location: ../../Formularios/Login/establecerPassword.php");
        exit();
    }

    // Verificar contraseña    
    if (password_verify($password, $usuario['password'])) {
        // Contraseña correcta - Crear sesión    
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_apellido'] = $usuario['apellido'];
        $_SESSION['usuario_correo'] = $usuario['correo'];
        $_SESSION['usuario_rol'] = $usuario['rol_sistema'];

        if ($usuario['rol_sistema'] == 'Administrador') {
            header("location:../../index.php");
        } elseif ($usuario['rol_sistema'] == 'Oficina') {
            header("location: ../../pages/empleado.php");
        } else {  // Empleado    
            // Empleado  
            // 1. Obtener el id del empleado en la tabla empleados  
            $usuario_id = $usuario['id'];
            $sqlEmpleado = "SELECT id FROM empleados WHERE empleado_id = ? AND activo = 1 LIMIT 1";
            $stmtEmp = $conexion->prepare($sqlEmpleado);
            $stmtEmp->bind_param("i", $usuario_id);
            $stmtEmp->execute();
            $resEmp = $stmtEmp->get_result();

            if ($resEmp->num_rows > 0) {
                $empleado = $resEmp->fetch_assoc();
                $empleado_id = $empleado['id'];

                // 2. Registrar asistencia con fecha y hora actual  
                $sqlAsistencia = "INSERT INTO asistencias (empleado_id, fecha, hora_entrada, hora_salida, total_horas)  
                          VALUES (?, CURDATE(), CURTIME(), NULL, 0.00)";
                $stmtAsis = $conexion->prepare($sqlAsistencia);
                $stmtAsis->bind_param("i", $empleado_id);
                $stmtAsis->execute();
                $stmtAsis->close();
            }
            $stmtEmp->close();
            header("location: ../../pages/perfil_empleado.php");
        }
        exit();
    } else {
        header("location:../../Formularios/Login/login.php?error=credenciales");
        exit();
    }
} else {
    header("location:../../Formularios/Login/login.php?error=credenciales");
    exit();
}

// Cerrar el statement  
$stmt->close();
