<?php
// Proteger la página    
require("../Config/verificarSesion.php");

// Solo empleados pueden ver esta página    
verificarRol(['Empleado', 'Oficina', 'Administrador']);

require(BASE_PATH . "Config/Conexion.php");

// Obtener el empleado_id del usuario actual  
$usuario_id = $_SESSION['usuario_id'];
$sql_empleado = $conexion->query("SELECT empleados.id,   
                                          usuarios.nombre,   
                                          usuarios.apellido,  
                                          roles.nombre as rol_nombre,  
                                          horarios.nombre as horario_nombre,  
                                          horarios.tipo as horario_tipo,  
                                          horarios.horas_minimas  
                                   FROM empleados  
                                   INNER JOIN usuarios ON empleados.empleado_id = usuarios.id  
                                   INNER JOIN roles ON empleados.rol_id = roles.id  
                                   LEFT JOIN horarios ON roles.horario_id = horarios.id  
                                   WHERE usuarios.id = $usuario_id");
$empleado = $sql_empleado->fetch_assoc();
$empleado_id = $empleado['id'];

// Estadísticas del empleado  
$fecha_actual = date('Y-m-d');
$totalAsistenciasHoy = $conexion->query("SELECT COUNT(*) as total FROM asistencias WHERE empleado_id = $empleado_id AND fecha = '$fecha_actual'")->fetch_assoc()['total'];
$totalHorasHoy = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE empleado_id = $empleado_id AND fecha = '$fecha_actual'")->fetch_assoc()['total'] ?? 0;

// Cálculo semanal  
$dia_semana = date('N');
$inicio_semana = date('Y-m-d', strtotime("-" . ($dia_semana - 1) . " days"));
$fin_semana = date('Y-m-d', strtotime($inicio_semana . " +6 days"));
$totalHorasSemana = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE empleado_id = $empleado_id AND fecha BETWEEN '$inicio_semana' AND '$fin_semana'")->fetch_assoc()['total'] ?? 0;
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Perfil - Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!--Dependencias-->
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- NUEVAS: DataTables Responsive -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>src/css/styles.css">
</head>

<body>

    <main class="container mt-4">
        <?php include(BASE_PATH . "src/includes/Componentes/userbarempledo.php"); ?>

        <h1 class="bg-primary p-3 text-white text-center rounded">👤 MI PERFIL</h1>

        <div class="text-end mb-3">
            <a href="<?php echo BASE_URL; ?>Formularios/Peticion/AgregarPeticion.php" class="btn btn-success">
                <i class="bi bi-send-fill"></i> Enviar Petición
            </a>
        </div>

        <!-- Información Personal -->
        <div class="row mt-4 g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-dark"><i class="bi bi-person-circle"></i> Información Personal</h5>
                        <p><strong>Nombre:</strong> <?php echo $empleado['nombre'] . ' ' . $empleado['apellido']; ?></p>
                        <p><strong>Rol Laboral:</strong> <?php echo $empleado['rol_nombre']; ?></p>
                        <p><strong>Horario:</strong>
                            <?php
                            if ($empleado['horario_nombre']) {
                                echo $empleado['horario_nombre'] . " (" . $empleado['horario_tipo'] . ")";
                                if ($empleado['horas_minimas']) {
                                    echo " - Mínimo: " . $empleado['horas_minimas'] . " hrs";
                                }
                            } else {
                                echo "<span class='text-muted'>Sin horario asignado</span>";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-dark"><i class="bi bi-bar-chart-fill"></i> Estadísticas</h5>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="bg-info text-white p-3 rounded">
                                    <h6>Horas Hoy</h6>
                                    <p class="display-6 mb-0"><?php echo number_format($totalHorasHoy, 1); ?></p>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="bg-success text-white p-3 rounded">
                                    <h6>Horas Semana</h6>
                                    <p class="display-6 mb-0"><?php echo number_format($totalHorasSemana, 1); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mis Asistencias -->
        <h3 class="mt-4"><i class="bi bi-calendar-check"></i> Mis Asistencias</h3>
        <div class="table-container">
            <table id="tabla-perfil" class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Total Horas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = $conexion->query("SELECT asistencias.*  
                                   FROM asistencias       
                                   WHERE asistencias.empleado_id = $empleado_id  
                                   ORDER BY asistencias.fecha DESC, asistencias.hora_entrada DESC");

                    while ($resultado = $sql->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($resultado['fecha'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($resultado['hora_entrada'])); ?></td>
                            <td>
                                <?php
                                if ($resultado['hora_salida']) {
                                    echo date('h:i A', strtotime($resultado['hora_salida']));  
                                } else {
                                    echo "<span class='badge bg-warning'>Sin registrar</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($resultado['total_horas'] > 0) {
                                    echo number_format($resultado['total_horas'], 2) . " hrs";
                                } else {
                                    echo "<span class='text-muted'>-</span>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Mis Vacaciones Próximas -->
        <h3 class="mt-4"><i class="bi bi-umbrella"></i> Mis Vacaciones Próximas</h3>
        <div class="table-container">
            <table id="tabla-vacaciones-empledo" class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Días</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $hoy = date('Y-m-d');
                    $sqlVacacionesEmpleado = $conexion->query("
                                                     SELECT * 
                                                     FROM dias_no_laborales 
                                                     WHERE motivo = 'Vacaciones' AND empleado_id = $empleado_id AND fecha_fin >= '$hoy'
                                                     ORDER BY fecha_inicio ASC");

                    if ($sqlVacacionesEmpleado->num_rows > 0):
                        while ($vacacion = $sqlVacacionesEmpleado->fetch_assoc()):
                            $dias = (strtotime($vacacion['fecha_fin']) - strtotime($vacacion['fecha_inicio'])) / 86400 + 1;
                    ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($vacacion['fecha_inicio'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($vacacion['fecha_fin'])); ?></td>
                                <td><span class="badge bg-primary"><?php echo $dias; ?> día(s)</span></td>
                                <td><?php echo $vacacion['descripcion'] ?: '<span class="text-muted">Sin descripción</span>'; ?></td>
                            </tr>
                    <?php
                        endwhile;
                    else:
                        echo "<tr><td colspan='4' class='text-center text-muted'>No tienes vacaciones próximas</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Próximos Feriados -->
        <h3 class="mt-4"><i class="bi bi-calendar-event"></i> Próximos Feriados</h3>
        <div class="table-container">
            <table id="tabla-feriados" class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Días</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sqlProxFeriados = $conexion->query("
                                               SELECT * 
                                               FROM dias_no_laborales 
                                               WHERE motivo = 'Feriado' AND fecha_inicio >= '$hoy'
                                               ORDER BY fecha_inicio ASC");

                    if ($sqlProxFeriados->num_rows > 0):
                        while ($feriado = $sqlProxFeriados->fetch_assoc()):
                            $dias = (strtotime($feriado['fecha_fin']) - strtotime($feriado['fecha_inicio'])) / 86400 + 1;
                    ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($feriado['fecha_inicio'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($feriado['fecha_fin'])); ?></td>
                                <td><span class="badge bg-info"><?php echo $dias; ?> día(s)</span></td>
                                <td><?php echo $feriado['descripcion'] ?: '<span class="text-muted">Sin descripción</span>'; ?></td>
                            </tr>
                    <?php
                        endwhile;
                    else:
                        echo "<tr><td colspan='4' class='text-center text-muted'>No hay feriados próximos</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>


        <!-- Mis Peticiones Enviadas -->
        <h3 class="mt-4"><i class="bi bi-send-fill"></i> Mis Peticiones Enviadas</h3>
        <div class="table-container">
            <table id="tabla-peticiones-empleado" class="table table-hover">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consultar solo las peticiones del empleado actual
                    $sqlPeticionesEmpleado = $conexion->query("
                                                     SELECT * 
                                                     FROM peticiones
                                                     WHERE empleado_id = $empleado_id");

                    if ($sqlPeticionesEmpleado->num_rows > 0):
                        while ($peticion = $sqlPeticionesEmpleado->fetch_assoc()):
                            $estado = $peticion['estado'];
                            $clase = '';
                            switch ($estado) {
                                case 'Pendiente':
                                    $clase = 'bg-warning text-dark';
                                    break;
                                case 'Aprobada':
                                    $clase = 'bg-success';
                                    break;
                                case 'Rechazada':
                                    $clase = 'bg-danger';
                                    break;
                            }
                    ?>
                            <tr>
                                <td><?php echo $peticion['descripcion']; ?></td>
                                <td><span class="badge <?php echo $clase; ?>"><?php echo ucfirst($estado); ?></span></td>
                            </tr>
                    <?php
                        endwhile;
                    else:
                        // Mensaje si no hay peticiones
                        echo "<tr><td colspan='3' class='text-center text-muted'>No tienes peticiones enviadas</td></tr>";
                    endif;
                    ?>
                </tbody>
            </table>
        </div>

    </main>


    <!-- Inicializar DataTables -->
    <?php include(BASE_PATH . "src/includes/Dependencias/datatables.php"); ?>

    <!-- Inicializar SweetAlert2 -->
    <?php include(BASE_PATH . "src/includes/Dependencias/sweetalert.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>