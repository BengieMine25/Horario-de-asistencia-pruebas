<?php
session_start();
include("../../Config/Conexion.php");

if (!isset($_SESSION['temp_usuario_id'])) {
    header("location: ../../Formularios/Login/login.php");
    exit();
}

$usuario_id = intval($_POST['UsuarioId']);
$password = $_POST['Password'];
$confirmar_password = $_POST['ConfirmarPassword'];

if ($password !== $confirmar_password) {
    header("location: ../../Formularios/Login/establecerPassword.php?error=no_coinciden");
    exit();
}

if (strlen($password) < 6) {
    header("location: ../../Formularios/Login/establecerPassword.php?error=muy_corta");
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// UPDATE con prepared statement
$stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
if (!$stmt) {
    die("Error en prepare UPDATE: " . $conexion->error);
}
$stmt->bind_param("si", $password_hash, $usuario_id);
if (!$stmt->execute()) {
    die("Error en execute UPDATE: " . $stmt->error);
}
$stmt->close();

// Obtener datos del usuario
$sqlUsuario = "SELECT id, nombre, apellido, correo, rol_sistema FROM usuarios WHERE id = ?";
$stmtUser = $conexion->prepare($sqlUsuario);
$stmtUser->bind_param("i", $usuario_id);
$stmtUser->execute();
$resultado = $stmtUser->get_result();
$usuario = $resultado->fetch_assoc();
$stmtUser->close();

unset($_SESSION['temp_usuario_id']);
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];
$_SESSION['usuario_apellido'] = $usuario['apellido'];
$_SESSION['usuario_correo'] = $usuario['correo'];
$_SESSION['usuario_rol'] = $usuario['rol_sistema'];

if ($usuario['rol_sistema'] == 'Administrador') {
    header("location: ../../index.php");
} elseif ($usuario['rol_sistema'] == 'Oficina') {
    header("location: ../../pages/empleado.php");
} else {
    date_default_timezone_set('America/Mexico_City');

    $sqlEmpleado = "SELECT id FROM empleados WHERE empleado_id = ? AND activo = 1 LIMIT 1";
    $stmtEmp = $conexion->prepare($sqlEmpleado);
    if ($stmtEmp) {
        $stmtEmp->bind_param("i", $usuario_id);
        $stmtEmp->execute();
        $resEmp = $stmtEmp->get_result();

        if ($resEmp->num_rows > 0) {
            $empleado = $resEmp->fetch_assoc();
            $empleado_id = $empleado['id'];

            $fechaHoy = date('Y-m-d');
            $sqlCheck = "SELECT id FROM asistencias WHERE empleado_id = ? AND fecha = ? LIMIT 1";
            $stmtCheck = $conexion->prepare($sqlCheck);
            $stmtCheck->bind_param("is", $empleado_id, $fechaHoy);
            $stmtCheck->execute();
            $resCheck = $stmtCheck->get_result();

            if ($resCheck->num_rows == 0) {
                $horaEntrada = date('H:i:s');
                $sqlInsert = "INSERT INTO asistencias (empleado_id, fecha, hora_entrada) VALUES (?, ?, ?)";
                $stmtInsert = $conexion->prepare($sqlInsert);
                $stmtInsert->bind_param("iss", $empleado_id, $fechaHoy, $horaEntrada);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
            $stmtCheck->close();
        }
        $stmtEmp->close();
    }

    header("location: ../../pages/perfil_empleado.php");
}
exit();
