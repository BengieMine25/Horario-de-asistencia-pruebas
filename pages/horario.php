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
    <title>Horarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>src/css/styles.css?v=1.8">
</head>

<body class="bg-light">

    <?php include(BASE_PATH . "src/includes/Componentes/sidebar.php"); ?>

    <main class="container mt-4 mb-5">
        <?php include(BASE_PATH . "src/includes/Componentes/userbar.php"); ?>

        <h1 class="titulo-modulo-horarios p-3 text-white text-center rounded mb-4">📋 LISTADO DE HORARIOS</h1>

        <div class="text-end mb-3">
            <a href="<?php echo BASE_URL; ?>Formularios/Horario/AgregarHorario.php" class="btn btn-success px-3 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Agregar Horario
            </a>
        </div>

        <div class="table-container shadow-sm bg-white p-3 rounded-4">
            <table id="tabla" class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Horario</th>
                        <th>Horas Mínimas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require(BASE_PATH . "Config/Conexion.php");
                    $sql = $conexion->query("SELECT * FROM horarios ORDER BY nombre ASC");
                    while ($resultado = $sql->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><strong class="text-dark"><?php echo $resultado['nombre']; ?></strong></td>
                        <td>
                            <?php
                                if ($resultado['tipo'] == 'Fijo') {
                                    echo "<span class='badge-horario-fijo'><i class='bi bi-lock-fill me-1'></i>Fijo</span>";
                                } else {
                                    echo "<span class='badge-horario-flexible'><i class='bi bi-unlock-fill me-1'></i>Flexible</span>";
                                }
                                ?>
                        </td>
                        <td>
                            <?php
                                if ($resultado['hora_inicio'] && $resultado['hora_fin']) {
                                    echo "<span class='badge bg-light text-dark border fw-medium'><i class='bi bi-clock text-primary me-1'></i>" . date('h:i A', strtotime($resultado['hora_inicio'])) . " - " . date('h:i A', strtotime($resultado['hora_fin'])) . "</span>";
                                } else {
                                    echo "<span class='text-muted small italic'>Horario flexible</span>";
                                }
                                ?>
                        </td>
                        <td class="fw-semibold text-secondary"><?php echo $resultado['horas_minimas']; ?> hrs</td>
                        <td class="acciones">
                            <div class="d-flex gap-2">
                                <a href="<?php echo BASE_URL; ?>Formularios/Horario/EditarHorario.php?Id=<?php echo $resultado['id']; ?>"
                                   class="btn btn-warning btn-sm rounded-3">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="<?php echo BASE_URL; ?>CRUD/Horario/eliminarHorario.php?Id=<?php echo $resultado['id']; ?>"
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