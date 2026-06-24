<?php
// Proteger la página  
require("../Config/verificarSesion.php");

// Solo administradores pueden ver esta página  
verificarRol(['Administrador']);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="../src/css/styles.css">

    <style>
        /* Estilizado del título principal acoplado a la línea institucional */
        .titulo-modulo {
            background: linear-gradient(135deg, #1d439c 0%, #153482 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>

    <?php include('../src/includes/Componentes/sidebar.php'); ?>

    <main class="container mt-4 content-wrapper">
        <?php include('../src/includes/Componentes/userbar.php'); ?>

        <h1 class="titulo-modulo p-3 text-white text-center rounded mb-4">👥 GESTIÓN DE USUARIOS DEL SISTEMA</h1>

        <div class="text-end mb-3">
            <a href="<?php echo BASE_URL; ?>Formularios/Usuario/AgregarUsuario.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Agregar Usuario
            </a>
        </div>

        <div class="table-container mb-5">
            <table id="tabla" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Rol del Sistema</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require(BASE_PATH . "Config/Conexion.php");

                    $sql = $conexion->query("SELECT id, nombre, apellido, correo, rol_sistema  
                                   FROM usuarios  
                                   ORDER BY nombre ASC");

                    while ($resultado = $sql->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo $resultado['nombre'] . ' ' . $resultado['apellido']; ?></strong>
                        </td>
                        <td><?php echo $resultado['correo']; ?></td>
                        <td>
                            <?php
                                $badge_class = '';
                                switch ($resultado['rol_sistema']) {
                                    case 'Administrador':
                                        // Rojo corporativo formal
                                        $badge_class = 'bg-danger text-white';
                                        break;
                                    case 'Oficina':
                                        // Ámbar/Oro formal
                                        $badge_class = 'bg-warning text-white';
                                        break;
                                    case 'Empleado':
                                        // Azul claro de asistencia corporativo
                                        $badge_class = 'bg-primary text-white';
                                        break;
                                }
                                echo "<span class='badge $badge_class'>" . $resultado['rol_sistema'] . "</span>";
                                ?>
                        </td>
                        <td class="acciones">
                            <a href="<?php echo BASE_URL; ?>Formularios/Usuario/EditarUsuario.php?Id=<?php echo $resultado['id']; ?>"
                                class="btn btn-warning btn-sm text-white">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="<?php echo BASE_URL; ?>CRUD/Usuario/eliminarUsuario.php?Id=<?php echo $resultado['id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="event.preventDefault(); confirmarEliminacion(this.href)">
                                <i class="bi bi-trash3"></i> Eliminar
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