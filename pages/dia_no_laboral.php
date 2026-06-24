<?php
// Proteger la página  
require("../Config/verificarSesion.php");

// Solo administradores pueden ver esta página  
verificarRol(['Administrador', 'Oficina']);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Días No Laborales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>src/css/styles.css?v=2.1">
</head>

<body class="bg-light">

    <?php include(BASE_PATH . "src/includes/Componentes/sidebar.php"); ?>

    <main class="container mt-4 mb-5">
        <?php include(BASE_PATH . "src/includes/Componentes/userbar.php"); ?>

        <h1 class="titulo-modulo-calendario p-3 text-white text-center rounded mb-4">📅 GESTIÓN DE DÍAS NO LABORALES</h1>

        <div class="text-end mb-3">
            <a href="<?php echo BASE_URL; ?>Formularios/DiaNoLaboral/AgregarDiaNoLaboral.php" class="btn btn-success px-3 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Agregar Día No Laboral
            </a>
        </div>

        <ul class="nav nav-tabs mb-0 border-bottom-0" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="feriados-tab" data-bs-toggle="tab" data-bs-target="#feriados" type="button" role="tab">
                    🏛️ Feriados Institucionales
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vacaciones-tab" data-bs-toggle="tab" data-bs-target="#vacaciones" type="button" role="tab">
                    🏖️ Vacaciones de Empleados
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="feriados" role="tabpanel" aria-labelledby="feriados-tab">
                <div class="table-container shadow-sm bg-white p-3 rounded-bottom-4 rounded-end-4">
                    <table id="tabla" class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Días</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require(BASE_PATH . "Config/Conexion.php");
                            $sqlFeriados = $conexion->query("SELECT * FROM dias_no_laborales   
                                                              WHERE motivo = 'Feriado'   
                                                              ORDER BY fecha_inicio ASC");
                            while ($feriado = $sqlFeriados->fetch_assoc()) {
                                $dias = (strtotime($feriado['fecha_fin']) - strtotime($feriado['fecha_inicio'])) / 86400 + 1;
                            ?>
                            <tr>
                                <td class="fw-medium text-dark"><i class="bi bi-calendar-event text-primary me-2"></i><?php echo date('d/m/Y', strtotime($feriado['fecha_inicio'])); ?></td>
                                <td class="fw-medium text-dark"><?php echo date('d/m/Y', strtotime($feriado['fecha_fin'])); ?></td>
                                <td><span class="badge badge-contador-feriados"><?php echo $dias; ?> día(s)</span></td>
                                <td class="text-secondary"><?php echo $feriado['descripcion'] ? $feriado['descripcion'] : '<span class="text-muted italic small">Sin descripción</span>'; ?></td>
                                <td class="acciones">
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo BASE_URL; ?>Formularios/DiaNoLaboral/EditarDiaNoLaboral.php?Id=<?php echo $feriado['id']; ?>" class="btn btn-warning btn-sm rounded-3">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>CRUD/eliminarDiaNoLaboral.php?Id=<?php echo $feriado['id']; ?>" class="btn btn-danger btn-sm rounded-3" onclick="event.preventDefault(); confirmarEliminacion(this.href)">
                                            <i class="bi bi-trash3"></i> Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="vacaciones" role="tabpanel" aria-labelledby="vacaciones-tab">
                <div class="table-container shadow-sm bg-white p-3 rounded-bottom-4 rounded-end-4">
                    <table id="tabla-vacaciones" class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Rol asignado</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Días</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlVacaciones = $conexion->query("SELECT dias_no_laborales.*,   
                                                                     usuarios.nombre,   
                                                                     usuarios.apellido,  
                                                                     roles.nombre as rol_nombre  
                                                             FROM dias_no_laborales  
                                                             INNER JOIN empleados ON dias_no_laborales.empleado_id = empleados.id  
                                                             INNER JOIN usuarios ON empleados.empleado_id = usuarios.id  
                                                             INNER JOIN roles ON empleados.rol_id = roles.id  
                                                             WHERE dias_no_laborales.motivo = 'Vacaciones'  
                                                             ORDER BY dias_no_laborales.fecha_inicio DESC");
                            while ($vacacion = $sqlVacaciones->fetch_assoc()) {
                                $dias = (strtotime($vacacion['fecha_fin']) - strtotime($vacacion['fecha_inicio'])) / 86400 + 1;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bold"><?php echo $vacacion['nombre']; ?></span>
                                        <span class="text-muted small"><?php echo $vacacion['apellido']; ?></span>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border fw-medium"><?php echo $vacacion['rol_nombre']; ?></span></td>
                                <td class="fw-medium text-dark"><i class="bi bi-calendar-range text-success me-2"></i><?php echo date('d/m/Y', strtotime($vacacion['fecha_inicio'])); ?></td>
                                <td class="fw-medium text-dark"><?php echo date('d/m/Y', strtotime($vacacion['fecha_fin'])); ?></td>
                                <td><span class="badge badge-contador-vacaciones"><?php echo $dias; ?> día(s)</span></td>
                                <td class="text-secondary"><?php echo $vacacion['descripcion'] ? $vacacion['descripcion'] : '<span class="text-muted italic small">Sin descripción</span>'; ?></td>
                                <td class="acciones">
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo BASE_URL; ?>Formularios/DiaNoLaboral/EditarDiaNoLaboral.php?Id=<?php echo $vacacion['id']; ?>" class="btn btn-warning btn-sm rounded-3">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>CRUD/DiaNoLaboral/eliminarDiaNoLaboral.php?Id=<?php echo $vacacion['id']; ?>" class="btn btn-danger btn-sm rounded-3" onclick="event.preventDefault(); confirmarEliminacion(this.href)">
                                            <i class="bi bi-trash3"></i> Eliminar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </main>

    <?php include(BASE_PATH . "src/includes/Dependencias/datatables.php"); ?>

    <?php include(BASE_PATH . "src/includes/Dependencias/sweetalert.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>