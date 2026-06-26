<?php
// Proteger la página    
require("../../Config/verificarSesion.php");
// Verificar que roles de gestión/oficina puedan editar peticiones
verificarRol(['Administrador', 'Oficina']);

require("../../Config/Conexion.php");

$id = $_GET['Id'];

// Traer la petición junto con el nombre completo del empleado
$sql = $conexion->query("
    SELECT p.*, u.nombre, u.apellido
    FROM peticiones p
    INNER JOIN empleados e ON p.empleado_id = e.id
    INNER JOIN usuarios u ON e.empleado_id = u.id
    WHERE p.id = $id
");
$peticion = $sql->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Petición</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../src/css/styles.css?v=3.0" />
</head>

<body class="bg-light">

    <div class="container mt-3" style="max-width: 1200px;">
        <?php 
        if (defined('BASE_PATH')) {
            include(BASE_PATH . "src/includes/Componentes/userbar.php");
        } else {
            include("../../src/includes/Componentes/userbar.php");
        }
        ?>
    </div>

    <div class="container" style="max-width: 650px;">
        
        <div class="form-card-container shadow-sm">
            <h1 class="form-title-custom text-center mb-4">✏️ Editar Petición</h1>
            
            <form action="../../CRUD/Peticion/editarPeticion.php" method="post">

                <input type="hidden" name="Id" value="<?php echo $peticion['id']; ?>">

                <div class="mb-3">
                    <label class="form-label-custom">Empleado Solicitante</label>
                    <input type="text" class="form-control form-control-custom"
                        value="<?php echo htmlspecialchars($peticion['nombre'] . ' ' . $peticion['apellido'], ENT_QUOTES, 'UTF-8'); ?>"
                        disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Descripción / Motivo</label>
                    <input type="text" class="form-control form-control-custom"
                        name="DescripcionPeticion"
                        value="<?php echo htmlspecialchars($peticion['descripcion'], ENT_QUOTES, 'UTF-8'); ?>"
                        required>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Estado Resolutivo</label>
                    <select name="Estado" class="form-select form-control-custom" required>
                        <option value="pendiente" <?php if (strtolower($peticion['estado']) == 'pendiente') echo 'selected'; ?>>⏳ Pendiente</option>
                        <option value="aprobada" <?php if (strtolower($peticion['estado']) == 'aprobada') echo 'selected'; ?>>✅ Aprobada</option>
                        <option value="rechazada" <?php if (strtolower($peticion['estado']) == 'rechazada') echo 'selected'; ?>>❌  Rechazada</option>
                    </select>
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" class="btn-submit-custom">
                        <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                    </button>
                    <a href="../../pages/peticion.php" class="btn-cancel-custom text-decoration-none d-flex align-items-center justify-content-center">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>