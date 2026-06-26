<?php
// Proteger la página    
require("../../Config/verificarSesion.php");
// Verificar que roles de gestión puedan acceder (puedes ajustar los nombres de roles si es necesario)
verificarRol(['Administrador', 'Oficina']);

// La base de datos se incluye mediante el archivo, pero lo dejamos listo
include("../../Config/Conexion.php");  
?>
<!DOCTYPE html>  
<html lang="es">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>Registrar Asistencia</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">  
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> 
    <link rel="stylesheet" href="../../src/css/styles.css?v=2.9" />
</head>  
<body class="bg-light">  

    <div class="container mt-3" style="max-width: 1200px;">
        <?php 
        if (defined('BASE_PATH')) {
            include(BASE_PATH . "src/includes/userbar_simplificada.php");
        } else {
            include("../../src/includes/userbar_simplificada.php");
        }
        ?>
    </div>

    <div class="container" style="max-width: 650px;">  
        
        <div class="form-card-container shadow-sm">
            <h1 class="form-title-custom text-center mb-4">⏱️ Registrar Asistencia</h1>

            <form action="../../CRUD/Asistencia/insertarAsistencia.php" method="post">  
                
                <div class="mb-3">
                    <label class="form-label-custom">Empleado</label>  
                    <select id="select2" class="form-select form-control-custom" name="EmpleadoId" required>  
                        <option selected disabled value="">-- Seleccionar empleado --</option>  
                        <?php  
                        $sql = $conexion->query("SELECT empleados.id,   
                                                        usuarios.nombre,   
                                                        usuarios.apellido,   
                                                        roles.nombre as rol_nombre     
                                                 FROM empleados  
                                                 INNER JOIN usuarios ON empleados.empleado_id = usuarios.id  
                                                 INNER JOIN roles ON empleados.rol_id = roles.id     
                                                 WHERE empleados.activo = 1     
                                                 ORDER BY usuarios.nombre ASC");  
                        while ($resultado = $sql->fetch_assoc()) {  
                            echo "<option value='".$resultado['id']."'>".htmlspecialchars($resultado['nombre']." ".$resultado['apellido']." — ".$resultado['rol_nombre'], ENT_QUOTES, 'UTF-8')."</option>";  
                        }  
                        ?>  
                    </select>  
                </div>
      
                <div class="mb-3">  
                    <label class="form-label-custom">Fecha de Registro</label>  
                    <input type="date" class="form-control form-control-custom" name="Fecha" value="<?php echo date('Y-m-d'); ?>" required>  
                </div>  
      
                <div class="mb-3">  
                    <label class="form-label-custom">Hora de Entrada</label>  
                    <input type="time" class="form-control form-control-custom" name="HoraEntrada" id="horaEntrada" required>  
                </div>  
      
                <div class="mb-3">  
                    <label class="form-label-custom">Hora de Salida (Opcional)</label>  
                    <input type="time" class="form-control form-control-custom" name="HoraSalida" id="horaSalida">  
                </div>  
      
                <div class="mb-4">  
                    <label class="form-label-custom">Total de Horas Calculadas</label>  
                    <input type="text" class="form-control form-control-custom bg-white" name="TotalHoras" id="totalHoras" placeholder="0.00 hrs" readonly>  
                </div>  
      
                <div class="d-flex justify-content-center gap-3">  
                    <button type="submit" class="btn-submit-custom">
                        <i class="bi bi-check-circle-fill me-1"></i> Registrar
                    </button>  
                    <a href="../../pages/empleado.php" class="btn-cancel-custom text-decoration-none d-flex align-items-center justify-content-center">
                        Cancelar
                    </a>  
                </div>  
            </form>  
        </div>
    </div>  

    <script>  
        function calcularHoras() {  
            const entrada = document.getElementById("horaEntrada").value;  
            const salida = document.getElementById("horaSalida").value;  

            if (entrada && salida) {  
                const [hEntrada, mEntrada] = entrada.split(":").map(Number);  
                const [hSalida, mSalida] = salida.split(":").map(Number);  

                const entradaMin = hEntrada * 60 + mEntrada;  
                const salidaMin = hSalida * 60 + mSalida;  

                let totalMin = salidaMin - entradaMin;  
                if (totalMin < 0) totalMin += 24 * 60; // Soporte para turnos nocturnos u overwrapping  

                const horas = Math.floor(totalMin / 60);  
                const minutos = totalMin % 60;  
                    
                // Formato decimal requerido para la BD  
                const totalDecimal = (horas + minutos / 60).toFixed(2);  
                document.getElementById("totalHoras").value = totalDecimal + " hrs";  
            } else {
                document.getElementById("totalHoras").value = "";
            } 
        }  

        document.addEventListener("DOMContentLoaded", function() {  
            document.getElementById("horaEntrada").addEventListener("change", calcularHoras);  
            document.getElementById("horaSalida").addEventListener("change", calcularHoras);  
        });  
    </script>  

    <?php include('../../src/includes/Dependencias/Flatpickr.php'); ?> 
    <?php include('../../src/includes/Dependencias/Select2.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>