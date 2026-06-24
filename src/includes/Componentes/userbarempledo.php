<div class="user-header-bar">
    
    <div class="bar-branding-section">
        <img src="<?php echo rtrim(BASE_URL, '/') . '/src/images/logo-uml-2.png'; ?>" alt="Universidad Martín Lutero" class="bar-logo-institucional">
    </div>

    <div class="bar-actions-section">
        
        <div class="user-info-section" style="border-right: none; padding-right: 0;">
            <div class="user-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="user-details">
                <span class="welcome-text">Bienvenido/a,</span>
                <span class="user-name">
                    <?php echo $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido']; ?>
                </span>
                <span class="badge role-badge-custom"><?php echo $_SESSION['usuario_rol']; ?></span>
            </div>
        </div>

        <a href="<?php echo BASE_URL; ?>CRUD/Login/cerrarSesion.php" class="btn-logout-custom">
            <i class="bi bi-box-arrow-right"></i>
            <span>Cerrar Sesión</span>
        </a>

    </div>
</div>