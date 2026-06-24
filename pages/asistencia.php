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
    <title>Gestión de Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>src/css/styles.css?v=1.3">
</head>

<body class="bg-light">

    <?php include(BASE_PATH . "src/includes/Componentes/sidebar.php"); ?>

    <main class="container mt-4 mb-5">
        <?php include(BASE_PATH . "src/includes/Componentes/userbar.php"); ?>

        <h1 class="titulo-gestion-asistencias p-3 text-white text-center rounded mb-4">📋 LISTADO DE ASISTENCIAS</h1>

        <div class="text-end mb-3">
            <a href="<?php echo BASE_URL; ?>Formularios/Asistencia/AgregarAsistencia.php" class="btn btn-success px-3 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Registrar Asistencia
            </a>
        </div>

        <div class="table-container shadow-sm bg-white p-3 rounded-4">
            <table id="tabla" class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Rol</th>
                        <th>Fecha</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Total Horas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require(BASE_PATH . "Config/Conexion.php");

                    $sql = $conexion->query("SELECT asistencias.*,     
                                          usuarios.nombre,     
                                          usuarios.apellido,  
                                          roles.nombre as rol_nombre    
                                   FROM asistencias     
                                   INNER JOIN empleados ON asistencias.empleado_id = empleados.id  
                                   INNER JOIN usuarios ON empleados.empleado_id = usuarios.id  
                                   INNER JOIN roles ON empleados.rol_id = roles.id    
                                   ORDER BY asistencias.fecha DESC, asistencias.hora_entrada DESC");

                    while ($resultado = $sql->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <div class="nombre-apellido">
                                <span class="text-dark"><strong><?php echo $resultado['nombre']; ?></strong></span>
                                <span class="text-muted small"><?php echo $resultado['apellido']; ?></span>
                            </div>
                        </td>
                        <td class="text-secondary fw-semibold"><?php echo $resultado['rol_nombre']; ?></td>
                        <td class="fw-medium"><?php echo date('d/m/Y', strtotime($resultado['fecha'])); ?></td>
                        <td><span class="badge bg-light text-dark border"><i class="bi bi-box-arrow-in-right text-success"></i> <?php echo $resultado['hora_entrada']; ?></span></td>
                        <td>
                            <?php
                                if ($resultado['hora_salida']) {
                                    echo "<span class='badge bg-light text-dark border'><i class='bi bi-box-arrow-left text-danger'></i> " . $resultado['hora_salida'] . "</span>";
                                } else {
                                    echo "<span class='badge-sin-registrar'><i class='bi bi-exclamation-triangle'></i> Sin registrar</span>";
                                }
                                ?>
                        </td>
                        <td class="fw-bold">
                            <?php
                                if ($resultado['total_horas'] > 0) {
                                    echo number_format($resultado['total_horas'], 2) . " hrs";
                                } else {
                                    echo "<span class='text-muted'>-</span>";
                                }
                                ?>
                        </td>
                        <td class="acciones">
                            <div class="d-flex gap-2">
                                <a href="<?php echo BASE_URL; ?>Formularios/Asistencia/EditarAsistencia.php?Id=<?php echo $resultado['id']; ?>"
                                   class="btn btn-warning btn-sm rounded-3">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="<?php echo BASE_URL; ?>CRUD/Asistencia/eliminarAsistencia.php?Id=<?php echo $resultado['id']; ?>"
                                   class="btn btn-danger btn-sm rounded-3"
                                   onclick="event.preventDefault(); confirmarEliminacion(this.href)">
                                    <i class="bi bi-trash3"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include(BASE_PATH . "src/includes/Dependencias/datatables.php"); ?>

    <?php include(BASE_PATH . "src/includes/Dependencias/sweetalert.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>