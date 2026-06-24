<?php
require(BASE_PATH . "Config/Conexion.php");

$result = $conexion->query("
    SELECT id, descripcion
    FROM peticiones 
    WHERE estado = 'pendiente' 
    LIMIT 5
");

$peticiones = [];
while ($row = $result->fetch_assoc()) {
    $peticiones[] = $row;
}

$totalpendientes = $conexion->query("
    SELECT COUNT(*) as total 
    FROM peticiones 
    WHERE estado = 'pendiente'
")->fetch_assoc()['total'];
?>

<div class="user-header-bar">
    
    <div class="bar-branding-section">
        <img src="<?php echo rtrim(BASE_URL, '/') . '/src/images/logo-uml-2.png'; ?>" alt="Universidad Martín Lutero" class="bar-logo-institucional">
    </div>

    <div class="bar-actions-section">
        
        <div class="user-info-section">
            <a href="<?php echo BASE_URL; ?>pages/perfil.php" title="Ver mi Perfil">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
            </a>
            <div class="user-details">
                <span class="welcome-text">Bienvenido/a,</span>
                <span class="user-name">
                    <?php echo $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido']; ?>
                </span>
                <span class="badge role-badge-custom"><?php echo $_SESSION['usuario_rol']; ?></span>
            </div>
        </div>

        <div class="notification-wrapper">
            <div class="dropdown">
                <a class="notification-bell <?php echo ($totalpendientes > 0 ? 'has-notification' : ''); ?>"
                   href="#"
                   role="button"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    
                    <i class="bi bi-bell-fill"></i>

                    <?php if ($totalpendientes > 0): ?>
                        <span class="badge notification-badge-custom">
                            <?php echo ($totalpendientes > 9) ? '9+' : $totalpendientes; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end notification-dropdown shadow">
                    <li class="dropdown-header dropdown-header-custom">
                        🔔 Peticiones Pendientes
                    </li>

                    <?php if (count($peticiones) > 0): ?>
                        <?php foreach ($peticiones as $p): ?>
                            <li>
                                <a class="dropdown-item notification-item-custom" href="<?php echo BASE_URL; ?>pages/peticion.php">
                                    <div class="text-dark fw-bold mb-0">Petición #<?php echo $p['id']; ?></div>
                                    <div class="text-muted small text-truncate" style="max-width: 240px;"><?php echo $p['descripcion']; ?></div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="dropdown-item text-center text-muted py-3 italic small">
                            No tienes peticiones pendientes
                        </li>
                    <?php endif; ?>

                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item text-center fw-bold text-primary small py-2" href="<?php echo BASE_URL; ?>pages/peticion.php">
                            Ver todas las peticiones
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <a href="<?php echo BASE_URL; ?>CRUD/Login/cerrarSesion.php" class="btn-logout-custom">
            <i class="bi bi-box-arrow-right"></i>
            <span>Cerrar Sesión</span>
        </a>

    </div>
</div>