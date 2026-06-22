<!-- Sidebar Navegacion -->
<!-- Botón para abrir el menú -->

<button class="menu-toggle" id="menuToggle">
    <i class="bi bi-list"></i> Menú
</button>


<!-- Overlay oscuro -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="<?php echo BASE_URL; ?>src/images/logo-uml.png" alt="Logo UML">
            <div>
                <strong>UNIVERSIDAD</strong>
                <strong>Martín Lutero</strong>
            </div>
        </div>
    </div>

    <ul class="sidebar-menu">
        <?php if ($_SESSION['usuario_rol'] != 'Oficina'): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>index.php" 
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">       
                    <i class="bi bi-speedometer2"></i>
                    <strong>Dashboard</strong>
                </a>
            </li>

            <li>
                <a href="<?php echo BASE_URL; ?>pages/usuario.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuario.php' ? 'active' : ''; ?>">
                    <i class="bi bi-person-gear"></i>
                    <strong>Usuarios</strong>
                </a>
            </li>
        <?php endif; ?>

        <!-- <li>
            <a href="perfil.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : ''; ?>">
                <i class="bi bi-person-circle"></i>
                <strong>Mi Perfil</strong>
            </a>
        </li> -->

        <li>
            <a href="<?php echo BASE_URL; ?>pages/empleado.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'empleado.php' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                <strong>Empleados</strong>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>pages/asistencia.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'asistencia.php' ? 'active' : ''; ?>">
                <i class="bi bi-calendar-check"></i>
                <strong>Asistencias</strong>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>pages/rol.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'rol.php' ? 'active' : ''; ?>">
                <i class="bi bi-person-badge"></i>
                <strong>Roles</strong>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>pages/horario.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'horario.php' ? 'active' : ''; ?>">
                <i class="bi bi-clock"></i>
                <strong>Horarios</strong>
            </a>
        </li>

        <li>
            <a href="<?php echo BASE_URL; ?>pages/dia_no_laboral.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dia_no_laboral.php' ? 'active' : ''; ?>">
                <i class="bi bi-calendar-x"></i>
                <strong>Días No Laborales</strong>
            </a>
        </li>
    </ul>
</nav>


<script>
    // Toggle sidebar    
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Cerrar sidebar al hacer clic en un enlace (en móviles)    
        const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
    }
</script>