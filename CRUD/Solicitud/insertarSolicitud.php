<?php  
include("../../Config/Conexion.php");  
  
// Recibir datos del formulario  
$empleado_id = $_POST['EmpleadoId'];
$descripcion = $_POST['DescripcionSolicitud'];

$stmt = $conexion->prepare("
    INSERT INTO solicitudes (empleado_id, descripcion, estado) 
    VALUES (?, ?, 'pendiente')
");

$stmt->bind_param("is", $empleado_id, $descripcion);

if ($stmt->execute()) {
    header("Location: ../../pages/perfil_empleado.php?success=solicitud_enviada");
} else {
    header("Location: ../../pages/perfil_empleado.php?error=db");
}
exit();