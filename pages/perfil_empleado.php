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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link class="styles-sheet" rel="stylesheet" href="<?php echo BASE_URL; ?>src/css/styles.css?v=2.8">
</head>

<body class="bg-light">

    <main class="container mt-4" style="max-width: 1200px;">
        
        <?php include(BASE_PATH . "src/includes/Componentes/userbarempledo.php"); ?>

        <div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-2">
            <h2 class="text-white fw-bold mb-0"><i class="bi bi-person-badge-fill text-primary"></i> Panel de Perfil Laboral</h2>
            <a href="<?php echo BASE_URL; ?>Formularios/Peticion/AgregarPeticion.php" class="btn-submit-custom text-decoration-none d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-send-fill me-2"></i> Enviar Nueva Petición
            </a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="form-card-container h-100 shadow-sm p-4" style="margin-bottom:0;">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person-circle"></i> Información del Empleado</h5>
                    <hr class="text-muted opacity-25">
                    <div class="mb-2">
                        <span class="text-muted small d-block">Nombre Completo:</span>
                        <strong class="text-dark fs-5"><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted small d-block">Cargo / Rol Asignado:</span>
                        <span class="badge bg-secondary px-2 py-1 mt-1"><?php echo htmlspecialchars($empleado['rol_nombre'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="mb-0">
                        <span class="text-muted small d-block">Esquema de Horarios:</span>
                        <span class="text-dark fw-semibold">
                            <?php
                            if ($empleado['horario_nombre']) {
                                echo htmlspecialchars($empleado['horario_nombre'] . " (" . $empleado['horario_tipo'] . ")", ENT_QUOTES, 'UTF-8');
                                if ($empleado['horas_minimas']) {
                                    echo " — Mínimo requerido: " . $empleado['horas_minimas'] . " hrs";
                                }
                            } else {
                                echo "<span class='text-muted italic small'>Sin horario asignado</span>";
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-card-container h-100 shadow-sm p-4" style="margin-bottom:0;">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill"></i> Resumen de Horas Registradas</h5>
                    <hr class="text-muted opacity-25">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-light border border-info border-start-3" style="border-left-width: 4px !important;">
                                <span class="text-muted small d-block uppercase fw-bold" style="font-size:11px;">Horas de Hoy</span>
                                <span class="display-6 fw-bold text-info"><?php echo number_format($totalHorasHoy, 1); ?></span>
                                <span class="text-muted small d-block">horas</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-light border border-success border-start-3" style="border-left-width: 4px !important;">
                                <span class="text-muted small d-block uppercase fw-bold" style="font-size:11px;">Total de la Semana</span>
                                <span class="display-6 fw-bold text-success"><?php echo number_format($totalHorasSemana, 1); ?></span>
                                <span class="text-muted small d-block">horas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-12 col-xl-7">
                <div class="form-card-container shadow-sm p-4">
                    <h4 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-check text-primary"></i> Registro de Asistencias</h4>
                    <div class="table-responsive">
                        <table id="tabla-perfil" class="table table-hover align-middle">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Total Horas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = $conexion->query("SELECT asistencias.* FROM asistencias       
                                                         WHERE asistencias.empleado_id = $empleado_id  
                                                         ORDER BY asistencias.fecha DESC, asistencias.hora_entrada DESC");

                                while ($resultado = $sql->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo date('d/m/Y', strtotime($resultado['fecha'])); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo $resultado['hora_entrada']; ?></span></td>
                                        <td>
                                            <?php
                                            if ($resultado['hora_salida']) {
                                                echo "<span class='badge bg-light text-dark border'>" . $resultado['hora_salida'] . "</span>";
                                            } else {
                                                echo "<span class='badge bg-warning-subtle text-warning border border-warning'>Sin registrar</span>";
                                            }
                                            ?>
                                        </td>
                                        <td class="fw-semibold text-primary">
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
                </div>
            </div>

            <div class="col-12 col-xl-5">
                
                <div class="form-card-container shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-chat-left-text text-primary"></i> Estado de mis Peticiones</h5>
                    <div class="table-responsive">
                        <table id="tabla-peticiones-empleado" class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Motivo / Descripción</th>
                                    <th class="text-end">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // CORRECCIÓN: Filtrado exclusivo por el empleado actual
                                $sqlPeticionesEmpleado = $conexion->query("SELECT * FROM peticiones WHERE empleado_id = $empleado_id ORDER BY id DESC");

                                if ($sqlPeticionesEmpleado->num_rows > 0):
                                    while ($peticion = $sqlPeticionesEmpleado->fetch_assoc()):
                                        $estado = strtolower($peticion['estado']);
                                        $clase = 'bg-secondary';
                                        if($estado == 'pendiente') $clase = 'bg-warning text-dark border border-warning';
                                        if($estado == 'aprobada') $clase = 'bg-success-subtle text-success border border-success';
                                        if($estado == 'rechazada') $clase = 'bg-danger-subtle text-danger border border-danger';
                                ?>
                                        <tr>
                                            <td class="small text-truncate" style="max-width: 220px;"><?php echo htmlspecialchars($peticion['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-end"><span class="badge <?php echo $clase; ?> text-uppercase" style="font-size: 10px;"><?php echo $estado; ?></span></td>
                                        </tr>
                                <?php
                                    endwhile;
                                else:
                                    echo "<tr><td colspan='2' class='text-center text-muted small py-3'>No has enviado peticiones</td></tr>";
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-card-container shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-umbrella text-info"></i> Vacaciones Planificadas</h5>
                    <div class="table-responsive">
                        <table id="tabla-vacaciones-empledo" class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Período</th>
                                    <th class="text-end">Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $hoy = date('Y-m-d');
                                // CORRECCIÓN: Filtrado estricto por vacaciones pertenecientes únicamente a este empleado_id
                                $sqlVacacionesEmpleado = $conexion->query("SELECT * FROM dias_no_laborales WHERE motivo = 'Vacaciones' AND empleado_id = $empleado_id AND fecha_fin >= '$hoy' ORDER BY fecha_inicio ASC");

                                if ($sqlVacacionesEmpleado->num_rows > 0):
                                    while ($vacacion = $sqlVacacionesEmpleado->fetch_assoc()):
                                        $dias = (strtotime($vacacion['fecha_fin']) - strtotime($vacacion['fecha_inicio'])) / 86400 + 1;
                                ?>
                                        <tr>
                                            <td class="small">
                                                <span class="d-block fw-semibold"><?php echo date('d/m/Y', strtotime($vacacion['fecha_inicio'])); ?> al <?php echo date('d/m/Y', strtotime($vacacion['fecha_fin'])); ?></span>
                                                <span class="text-muted text-truncate d-block" style="max-width: 200px; font-size:11px;"><?php echo htmlspecialchars($vacacion['descripcion'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            </td>
                                            <td class="text-end"><span class="badge bg-info text-white"><?php echo $dias; ?> d</span></td>
                                        </tr>
                                <?php
                                    endwhile;
                                else:
                                    echo "<tr><td colspan='2' class='text-center text-muted small py-3'>No registras vacaciones programadas</td></tr>";
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-card-container shadow-sm p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-event text-danger"></i> Próximos Feriados Nacionales</h5>
                    <div class="table-responsive">
                        <table id="tabla-feriados" class="table table-sm table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Festividad</th>
                                    <th class="text-end">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // CORRECCIÓN: Filtra feriados generales (empleado_id IS NULL) o feriados específicos de este empleado si los hubiera, evitando ver las vacaciones privadas de otros.
                                $sqlProxFeriados = $conexion->query("SELECT * FROM dias_no_laborales WHERE motivo = 'Feriado' AND (empleado_id = $empleado_id OR empleado_id IS NULL) AND fecha_inicio >= '$hoy' ORDER BY fecha_inicio ASC LIMIT 4");

                                if ($sqlProxFeriados->num_rows > 0):
                                    while ($feriado = $sqlProxFeriados->fetch_assoc()):
                                ?>
                                        <tr>
                                            <td class="small fw-semibold"><?php echo htmlspecialchars($feriado['descripcion'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-end small text-muted text-nowrap"><?php echo date('d/m/Y', strtotime($feriado['fecha_inicio'])); ?></td>
                                        </tr>
                                <?php
                                    endwhile;
                                else:
                                    echo "<tr><td colspan='2' class='text-center text-muted small py-3'>No hay feriados en el calendario próximo</td></tr>";
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <?php include(BASE_PATH . "src/includes/Dependencias/datatables.php"); ?>
    <?php include(BASE_PATH . "src/includes/Dependencias/sweetalert.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>