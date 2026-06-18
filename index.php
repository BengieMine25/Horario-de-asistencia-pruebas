<?php  
date_default_timezone_set('America/Mexico_City'); // Ajusta según tu zona horaria
// Proteger la página  
require("Config/verificarSesion.php");  
  
// Solo administradores pueden ver esta página  
verificarRol(['Administrador']);  

require("Config/Conexion.php");  

$fecha_actual = date('Y-m-d');

// CONSULTAS
$totalUsuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$totalEmpleadosActivos = $conexion->query("SELECT COUNT(*) as total FROM empleados WHERE activo = 1")->fetch_assoc()['total'];
$totalEmpleadosInactivos = $conexion->query("SELECT COUNT(*) as total FROM empleados WHERE activo = 0")->fetch_assoc()['total'];
$totalAsistenciasHoy = $conexion->query("SELECT COUNT(*) as total FROM asistencias WHERE fecha = '$fecha_actual'")->fetch_assoc()['total'];
$totalSinSalida = $conexion->query("SELECT COUNT(*) as total FROM asistencias WHERE fecha = '$fecha_actual' AND hora_salida IS NULL")->fetch_assoc()['total'];
$totalHorasHoy = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE fecha = '$fecha_actual'")->fetch_assoc()['total'] ?? 0;
$totalHorarios = $conexion->query("SELECT COUNT(*) as total FROM horarios")->fetch_assoc()['total'];
$totalRoles = $conexion->query("SELECT COUNT(*) as total FROM roles")->fetch_assoc()['total'];
$totalDiasNoLaborales = $conexion->query("SELECT COUNT(*) as total FROM dias_no_laborales WHERE fecha_inicio >= '$fecha_actual' AND motivo = 'Feriado'")->fetch_assoc()['total'];

// Cálculo semanal
$dia_semana = date('N');
$inicio_semana = date('Y-m-d', strtotime("-" . ($dia_semana - 1) . " days"));
$fin_semana = date('Y-m-d', strtotime($inicio_semana . " +6 days"));
$totalHorasSemana = $conexion->query("SELECT SUM(total_horas) as total FROM asistencias WHERE fecha BETWEEN '$inicio_semana' AND '$fin_semana'")->fetch_assoc()['total'] ?? 0;
$totalAsistenciasSemana = $conexion->query("SELECT COUNT(*) as total FROM asistencias WHERE fecha BETWEEN '$inicio_semana' AND '$fin_semana'")->fetch_assoc()['total'];
?>  

<!doctype html>  
<html lang="es">  
<head>  
  <meta charset="UTF-8">  
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>Dashboard</title>  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">  
  <link rel="stylesheet" href="src/css/styles.css">
</head>  

<body>  

  <?php include('src/includes/Componentes/sidebar.php'); ?>  

  <main class="container py-4">  
    <?php include('src/includes/Componentes/userbar.php'); ?> 
  
    <h1 class="bg-primary p-3 text-white text-center rounded">📊 DASHBOARD DEL SISTEMA</h1>        

    <!-- RESUMEN GENERAL -->
    <h3 class="mt-5"><i class="bi bi-bar-chart-fill"></i> Resumen General</h3>
    <div class="row mt-4 g-3">  
      <div class="col-md-4 col-lg-3">
        <div class="card bg-primary text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-people-fill"></i> Usuarios</h5>
            <p class="display-4 fw-bold mb-0"><?php echo $totalUsuarios; ?></p>
            <small>Total en el sistema</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-success text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-person-check-fill"></i> Empleados Activos</h5>
            <p class="display-4 fw-bold mb-0"><?php echo $totalEmpleadosActivos; ?></p>
            <small><?php echo $totalEmpleadosInactivos; ?> inactivos</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-info text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-calendar-check"></i> Asistencias Hoy</h5>
            <p class="display-4 fw-bold mb-0"><?php echo $totalAsistenciasHoy; ?></p>
            <small><?php echo $totalSinSalida; ?> sin salida</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-warning text-dark text-center">
          <div class="card-body">
            <h5><i class="bi bi-clock-fill"></i> Horas Hoy</h5>
            <p class="display-4 fw-bold mb-0"><?php echo number_format($totalHorasHoy, 1); ?></p>
            <small>Total trabajadas</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-success text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-hourglass-split"></i> Horas Semana</h5>
            <p class="display-4 fw-bold mb-0"><?php echo number_format($totalHorasSemana, 1); ?></p>
            <small>Total trabajadas</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-primary text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-calendar-week"></i> Asistencias Semana</h5>
            <p class="display-4 fw-bold mb-0"><?php echo $totalAsistenciasSemana; ?></p>
            <small>Registradas</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-secondary text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-alarm"></i> Horarios</h5>
            <p class="display-4 fw-bold mb-0"><?php echo $totalHorarios; ?></p>
            <small>Configurados</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-dark text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-person-badge"></i> Roles</h5>
            <p class="display-4 fw-bold mb-0"><?php echo $totalRoles; ?></p>
            <small>Laborales</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-lg-3">
        <div class="card bg-danger text-white text-center">
          <div class="card-body">
            <h5><i class="bi bi-calendar-x"></i> Feriados</h5>
            <p class="display-4 fw-bold mb-0"><?php echo $totalDiasNoLaborales; ?></p>
            <small>Próximos</small>
          </div>
        </div>
      </div>
    </div>
  
<!-- EMPLEADOS TRABAJANDO AHORA -->
<h3 class="mt-5"><i class="bi bi-person-workspace"></i> Empleados Trabajando Ahora</h3>
<div class="dashboard-table-container mb-4">
  <table class="table table-hover align-middle">
    <thead class="table-light">
      <tr>
        <th>Empleado</th>
        <th>Rol</th>
        <th>Hora Entrada</th>
        <th>Tiempo Transcurrido</th>
      </tr>
    </thead>
    <tbody>
      <?php
    

      $sqlEmpleadosActivos = $conexion->query("
        SELECT a.*, u.nombre, u.apellido, r.nombre AS rol_nombre
        FROM asistencias a
        INNER JOIN empleados e ON a.empleado_id = e.id
        INNER JOIN usuarios u ON e.empleado_id = u.id
        INNER JOIN roles r ON e.rol_id = r.id
        WHERE a.fecha = '$fecha_actual' AND a.hora_salida IS NULL
        ORDER BY a.hora_entrada DESC
      ");

      if ($sqlEmpleadosActivos->num_rows > 0) {
        while ($emp = $sqlEmpleadosActivos->fetch_assoc()) {

          // Crear objetos DateTime para mayor precisión
          $entrada = new DateTime("{$emp['fecha']} {$emp['hora_entrada']}");
          $ahora = new DateTime();

          // Diferencia exacta
          $intervalo = $entrada->diff($ahora);
          $horas = $intervalo->h + ($intervalo->days * 24);
          $minutos = $intervalo->i;

          echo "<tr>
                  <td><strong>{$emp['nombre']} {$emp['apellido']}</strong></td>
                  <td>{$emp['rol_nombre']}</td>
                  <td>" . date('h:i A', strtotime($emp['hora_entrada'])) . "</td>
                  <td><span class='badge bg-success'>{$horas}h {$minutos}m</span></td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='4' class='text-center text-muted'>No hay empleados trabajando actualmente</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>


    <!-- ÚLTIMAS ASISTENCIAS -->
    <h3 class="mt-5"><i class="bi bi-clock-history"></i> Últimas Asistencias Registradas</h3>
    <div class="dashboard-table-container mb-4">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Fecha</th>
            <th>Empleado</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Horas</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sqlUltimas = $conexion->query("
            SELECT asistencias.*, usuarios.nombre, usuarios.apellido
            FROM asistencias
            INNER JOIN empleados ON asistencias.empleado_id = empleados.id
            INNER JOIN usuarios ON empleados.empleado_id = usuarios.id
            ORDER BY asistencias.fecha DESC, asistencias.hora_entrada DESC
            LIMIT 10
          ");
          if ($sqlUltimas->num_rows > 0) {
            while ($a = $sqlUltimas->fetch_assoc()) {
              $salida = $a['hora_salida']
                ? date('h:i A', strtotime($a['hora_salida']))
                : "<span class='badge bg-warning text-dark'>Pendiente</span>";
              echo "<tr>
                      <td>" . date('d/m/Y', strtotime($a['fecha'])) . "</td>
                      <td><strong>{$a['nombre']} {$a['apellido']}</strong></td>
                      <td>" . date('h:i A', strtotime($a['hora_entrada'])) . "</td>
                      <td>{$salida}</td>
                      <td>" . ($a['total_horas'] ? number_format($a['total_horas'], 2) . 'h' : '-') . "</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='5' class='text-center text-muted'>No hay asistencias registradas</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- PRÓXIMOS FERIADOS -->
    <h3 class="mt-5"><i class="bi bi-calendar-x"></i> Próximos Días No Laborales</h3>
    <div class="dashboard-table-container mb-5">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Motivo</th>
            <th>Descripción</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sqlFeriados = $conexion->query("
            SELECT * FROM dias_no_laborales 
            WHERE fecha_inicio >= '$fecha_actual' 
            ORDER BY fecha_inicio ASC 
            LIMIT 5
          ");
          if ($sqlFeriados->num_rows > 0) {
            while ($f = $sqlFeriados->fetch_assoc()) {
              echo "<tr>
                      <td>" . date('d/m/Y', strtotime($f['fecha_inicio'])) . "</td>
                      <td>" . date('d/m/Y', strtotime($f['fecha_fin'])) . "</td>
                      <td><span class='badge bg-danger'>{$f['motivo']}</span></td>
                      <td>{$f['descripcion']}</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='4' class='text-center text-muted'>No hay días no laborales próximos</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>
