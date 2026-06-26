<?php

include ("../../Config/Conexion.php");

$Id = $_GET['Id'];
$sql = "DELETE FROM solicitudes WHERE id=".$Id."";

$query = mysqli_query($conexion,$sql);

if (mysqli_query($conexion, $sql)) {  
    header("location:../../pages/solicitud.php?success=eliminado");  
} else {  
    header("location:../../pages/solicitud.php?error=db");  
}