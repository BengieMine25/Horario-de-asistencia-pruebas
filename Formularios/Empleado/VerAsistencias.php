<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ver Asistencias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../src/css/styles.css?v=1.2">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body class="bg-light">

    <main class="container mt-4 mb-5" id="contenido-pdf">
        <?php
        require("../../Config/Conexion.php");

        // Obtener ID del empleado    
        $empleado_id = $_GET['Id'];

        // Obtener información del empleado    
        $sqlEmpleado = $conexion->query("SELECT usuarios.nombre,     
                                              usuarios.apellido,    
                                              usuarios.correo,    
                                              roles.nombre as rol_nombre,    
                                              empleados.activo    
                                       FROM empleados    
                                       INNER JOIN usuarios ON empleados.empleado_id = usuarios.id       
                                       INNER JOIN roles ON empleados.rol_id = roles.id       
                                       WHERE empleados.id = $empleado_id");
        $empleado = $sqlEmpleado->fetch_assoc();
        ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../../pages/empleado.php" class="btn btn-primary px-3">
                <i class="bi bi-arrow-left"></i> Volver a Empleados
            </a>
            <button
                onclick="generarPDF('<?php echo $empleado['nombre'] . '_' . $empleado['apellido']; ?>', '<?php echo date('Y-m-d'); ?>')"
                class="btn btn-danger px-3">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </button>
        </div>

        <h1 class="titulo-asistencias p-3 text-white text-center rounded mb-4">
            📋 ASISTENCIAS DE <?php echo strtoupper($empleado['nombre'] . " " . $empleado['apellido']); ?>
        </h1>

        <div class="profile-info-banner mb-4">
            <div class="row align-items-center">
                <div class="col-sm-8">
                    <span class="fs-5 text-dark"><strong>Rol Laboral:</strong> <?php echo $empleado['rol_nombre']; ?></span><br>
                    <span class="text-muted"><strong>Correo Electrónico:</strong> <?php echo $empleado['correo']; ?></span>
                </div>
                <div class="col-sm-4 text-sm-end mt-2 mt-sm-0">
                    <strong>Estado:</strong>
                    <?php echo $empleado['activo'] ? '<span class="badge bg-success px-3 py-2">Activo</span>' : '<span class="badge bg-secondary px-3 py-2">Inactivo</span>'; ?>
                </div>
            </div>
        </div>

        <div class="row mb-4 g-3">
            <?php
            $fecha_actual = date('Y-m-d');
            $fecha_hora_actual = date('Y-m-d H:i:s');

            // SEMANA ACTUAL
            $dia_semana = date('N');
            $inicio_semana = date('Y-m-d', strtotime("-" . ($dia_semana - 1) . " days"));
            $fin_semana = date('Y-m-d', strtotime($inicio_semana . " +6 days"));

            $sqlSemana = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE empleado_id = $empleado_id AND fecha >= '$inicio_semana' AND fecha <= '$fin_semana'");
            $totalSemana = $sqlSemana->fetch_assoc()['total'] ?? 0;

            // QUINCENA ACTUAL
            $dia_mes = (int) date('d');
            $mes_actual = date('m');
            $anio_actual = date('Y');

            if ($dia_mes <= 15) {
                $inicio_quincena = "$anio_actual-$mes_actual-01";
                $fin_quincena = "$anio_actual-$mes_actual-15";
            } else {
                $inicio_quincena = "$anio_actual-$mes_actual-16";
                $ultimo_dia_mes = date('t', strtotime($fecha_actual));
                $fin_quincena = "$anio_actual-$mes_actual-$ultimo_dia_mes";
            }

            $sqlQuincena = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE empleado_id = $empleado_id AND fecha >= '$inicio_quincena' AND fecha <= '$fin_quincena'");
            $totalQuincena = $sqlQuincena->fetch_assoc()['total'] ?? 0;

            // MES ACTUAL
            $inicio_mes = date('Y-m-01');
            $fin_mes = date('Y-m-t');

            $sqlMes = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE empleado_id = $empleado_id AND fecha >= '$inicio_mes' AND fecha <= '$fin_mes'");
            $totalMes = $sqlMes->fetch_assoc()['total'] ?? 0;

            // AÑO ACTUAL
            $inicio_anio = date('Y-01-01');
            $fin_anio = date('Y-12-31');

            $sqlAnio = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE empleado_id = $empleado_id AND fecha >= '$inicio_anio' AND fecha <= '$fin_anio'");
            $totalAnio = $sqlAnio->fetch_assoc()['total'] ?? 0;
            ?>

            <div class="col-md-3">
                <div class="card text-center text-white card-metrics metric-semana">
                    <div class="card-body">
                        <h5 class="card-title opacity-75 fs-6 fw-bold text-uppercase">Semana Actual</h5>
                        <p class="card-text display-6 fw-semibold my-2"><?php echo number_format($totalSemana, 2); ?> <span class="fs-4">hrs</span></p>
                        <small class="opacity-75"><?php echo date('d/m', strtotime($inicio_semana)) . " - " . date('d/m', strtotime($fin_semana)); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center text-white card-metrics metric-quincena">
                    <div class="card-body">
                        <h5 class="card-title opacity-75 fs-6 fw-bold text-uppercase">Quincena Actual</h5>
                        <p class="card-text display-6 fw-semibold my-2"><?php echo number_format($totalQuincena, 2); ?> <span class="fs-4">hrs</span></p>
                        <small class="opacity-75"><?php echo date('d/m', strtotime($inicio_quincena)) . " - " . date('d/m', strtotime($fin_quincena)); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center text-white card-metrics metric-mes">
                    <div class="card-body">
                        <h5 class="card-title opacity-75 fs-6 fw-bold text-uppercase">Mes Actual</h5>
                        <p class="card-text display-6 fw-semibold my-2"><?php echo number_format($totalMes, 2); ?> <span class="fs-4">hrs</span></p>
                        <small class="opacity-75 text-capitalize"><?php echo date('F Y'); ?></small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center text-white card-metrics metric-anio">
                    <div class="card-body">
                        <h5 class="card-title opacity-75 fs-6 fw-bold text-uppercase">Año Actual</h5>
                        <p class="card-text display-6 fw-semibold my-2"><?php echo number_format($totalAnio, 2); ?> <span class="fs-4">hrs</span></p>
                        <small class="opacity-75"><?php echo date('Y'); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 chart-card-custom">
            <div class="card-body p-4">
                <canvas id="horasChart"></canvas>
            </div>
        </div>

        <div class="tabla-asistencias-container">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Total Horas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = $conexion->query("SELECT * FROM asistencias WHERE empleado_id = $empleado_id ORDER BY fecha DESC, hora_entrada DESC");

                    $total_horas_acumuladas = 0;
                    while ($resultado = $sql->fetch_assoc()) {
                        $total_horas_acumuladas += $resultado['total_horas'];
                    ?>
                    <tr>
                        <td class="fw-semibold text-secondary"><?php echo date('d/m/Y', strtotime($resultado['fecha'])); ?></td>
                        <td><span class="badge bg-light text-dark border"><i class="bi bi-box-arrow-in-right text-success"></i> <?php echo $resultado['hora_entrada']; ?></span></td>
                        <td>
                            <?php
                                if ($resultado['hora_salida']) {
                                    echo "<span class='badge bg-light text-dark border'><i class='bi bi-box-arrow-left text-danger'></i> " . $resultado['hora_salida'] . "</span>";
                                } else {
                                    echo "<span class='badge bg-warning text-white'><i class='bi bi-exclamation-triangle'></i> Sin registrar</span>";
                                }
                                ?>
                        </td>
                        <td class="fw-bold text-dark">
                            <?php
                                if ($resultado['total_horas'] > 0) {
                                    echo number_format($resultado['total_horas'], 2) . " hrs";
                                } else {
                                    echo "<span class='text-muted'>-</span>";
                                }
                                ?>
                        </td>
                        <td class="acciones">
                            <a href="../../CRUD/Empledo/eliminarAsistencia.php?Id=<?php echo $resultado['id']; ?>&EmpleadoId=<?php echo $empleado_id; ?>"
                                class="btn btn-danger btn-sm rounded-3"
                                onclick="event.preventDefault(); confirmarEliminacion(this.href)">
                                <i class="bi bi-trash3"></i> Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr class="table-light border-top">
                        <td colspan="3" class="py-3"><strong>Total Horas Registradas (Histórico):</strong></td>
                        <td colspan="2" class="py-3 text-primary fs-5"><strong><?php echo number_format($total_horas_acumuladas, 2); ?> hrs</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </main>

    <?php include('../../src/includes/Dependencias/Chart.php'); ?>

    <?php include('../../src/includes/Dependencias/sweetalert.php') ?>

    <script src="../../src/includes/Dependencias/html2pdf.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>