<?php
require("../../Config/Conexion.php");

$id = $_POST['Id'];
$descripcion = $_POST['DescripcionSolicitud'];
$estado = $_POST['Estado'];

$stmt = $conexion->prepare("
    UPDATE solicitudes 
    SET descripcion = ?, estado = ? 
    WHERE id = ?
");

$stmt->bind_param("ssi", $descripcion, $estado, $id);

if ($stmt->execute()) {
    header("Location: ../../pages/solicitud.php?success=editado");
} else {
    header("Location: ../../pages/solicitud.php?error=db");
}
exit();