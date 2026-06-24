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
    <title>Gestión de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <link rel="stylesheet" href="../src/css/styles.css">

    <style>
        /* Estilizado unificado del título principal */
        .titulo-modulo {
            background: linear-gradient(135deg, #1d439c 0%, #153482 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Ajuste de color para los textos informativos o secundarios en la tabla */
        .texto-secundario {
            color: #555555 !important;
            font-style: italic;
        }
    </style>
</head>

<body>

    <?php include(BASE_PATH . "src/includes/Componentes/sidebar.php"); ?>

    <main class="container mt-4 content-wrapper">
        <?php include(BASE_PATH . "src/includes/Componentes/userbar.php"); ?>

        <h1 class="titulo-modulo p-3 text-white text-center rounded mb-4">👷 GESTIÓN DE EMPLEADOS</h1>

        <div class="text-end mb-3 gap-2 d-flex justify-content-end">
            <a href="<?php echo BASE_URL; ?>Formularios/Empleado/AgregarEmpleado.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Agregar Empleado
            </a>
            <a href="<?php echo BASE_URL; ?>Formularios/Asistencia/AgregarAsistencia.php" class="btn btn-primary">
                <i class="bi bi-clock"></i> Registrar Asistencia
            </a>
        </div>

        <div class="table-container mb-5">
            <table id="tabla" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Rol Laboral</th>
                        <th>Horario Asignado</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require(BASE_PATH . "Config/Conexion.php");

                    $sql = $conexion->query("SELECT empleados.id,  
                                          usuarios.nombre,  
                                          usuarios.apellido,  
                                          roles.nombre as rol_nombre,  
                                          horarios.nombre as horario_nombre,  
                                          horarios.tipo as horario_tipo,  
                                          empleados.activo  
                                   FROM empleados  
                                   INNER JOIN usuarios ON empleados.empleado_id = usuarios.id  
                                   INNER JOIN roles ON empleados.rol_id = roles.id  
                                   LEFT JOIN horarios ON roles.horario_id = horarios.id  
                                   ORDER BY usuarios.nombre ASC");

                    while ($resultado = $sql->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <div class="nombre-apellido">
                                <span><strong><?php echo $resultado['nombre']; ?></strong></span>
                                <span class="text-muted-perfil"><?php echo $resultado['apellido']; ?></span>
                            </div>
                        </td>
                        <td><?php echo $resultado['rol_nombre']; ?></td>
                        <td>
                            <?php
                                if ($resultado['horario_nombre']) {
                                    echo $resultado['horario_nombre'] . " (" . $resultado['horario_tipo'] . ")";
                                } else {
                                    echo "<span class='texto-secundario'>Sin horario asignado</span>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($resultado['activo'] == 1) {
                                    echo "<span class='badge bg-success'>Activo</span>";
                                } else {
                                    echo "<span class='badge bg-secondary text-white'>Inactivo</span>";
                                }
                                ?>
                        </td>
                        <td class="acciones">
                            <a href="<?php echo BASE_URL; ?>Formularios/Empleado/EditarEmpleado.php?Id=<?php echo $resultado['id']; ?>"
                                class="btn btn-warning btn-sm text-white">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="<?php echo BASE_URL; ?>CRUD/Empleado/eliminarEmpleado.php?Id=<?php echo $resultado['id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="event.preventDefault(); confirmarEliminacion(this.href)">
                                <i class="bi bi-trash3"></i> Eliminar
                            </a>
                            <a href="<?php echo BASE_URL; ?>Formularios/Empleado/VerAsistencias.php?Id=<?php echo $resultado['id']; ?>"
                                class="btn btn-primary btn-sm">
                                <i class="bi bi-calendar-check"></i> Asistencias
                            </a>
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