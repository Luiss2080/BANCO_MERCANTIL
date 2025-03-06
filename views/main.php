<!DOCTYPE html>
<html lang="<?php echo $this->session->getLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título de la página dinámico -->
    <title><?php echo $lang['app_name']; ?> - <?php echo isset($pageTitle) ? $pageTitle : ''; ?></title>
    <!-- Enlace a la hoja de estilos principal -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Manifest para PWA (Progressive Web App) -->
    <link rel="manifest" href="manifest.json">
    <!-- Color del tema para navegadores móviles -->
    <meta name="theme-color" content="#056f1f">
</head>
<body>
    <!-- Encabezado de la página -->
    <div class="header">
        <div class="logo-container">
            <!-- Logo del Banco Mercantil -->
            <img src="/BANCO_CAMBA/assets/img/logo.png" alt="Logo Banco Mercantil" class="logo-image">
            <div class="logo">Banco Mercantil</div>
        </div>
        <!-- Información del usuario y selector de idioma -->
        <div class="user-info">
            <!-- Selector de idioma -->
            <div class="language-selector">
                <label for="language" style="color: white; margin-right: 5px;">Idioma:</label>
                <select id="language" style="padding: 5px; margin-right: 15px; border-radius: 4px;">
                    <option value="es" <?php echo $this->session->getLanguage() == 'es' ? 'selected' : ''; ?>>Español</option>
                    <option value="en" <?php echo $this->session->getLanguage() == 'en' ? 'selected' : ''; ?>>English</option>
                </select>
            </div>
            <!-- Nombre del usuario -->
            <div class="user-name"><?php echo $lang['welcome']; ?>, <span id="username"><?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario'; ?></span></div>
            <!-- Botón para cerrar sesión -->
            <a href="index.php?controller=usuario&action=cerrarSesion" class="btn btn-secondary logout-btn"><?php echo $lang['logout']; ?></a>
        </div>
    </div>
    
    <!-- Contenedor principal -->
    <div class="main-container">
        <!-- Barra lateral (sidebar) -->
        <div class="sidebar">
            <ul class="sidebar-menu">
                <!-- Enlace a la página de bienvenida -->
                <li class="sidebar-menu-item <?php echo $controller == 'bienvenida' ? 'active' : ''; ?>">
                    <a href="index.php?controller=bienvenida&action=index"><?php echo $lang['home']; ?></a>
                </li>
                <!-- Enlace al dashboard -->
                <li class="sidebar-menu-item <?php echo $controller == 'dashboard' ? 'active' : ''; ?>">
                    <a href="index.php?controller=dashboard&action=index"><?php echo $lang['dashboard']; ?></a>
                </li>
                <!-- Enlace a la gestión de clientes -->
                <li class="sidebar-menu-item <?php echo $controller == 'cliente' ? 'active' : ''; ?>">
                    <a href="index.php?controller=cliente&action=listar"><?php echo $lang['clients']; ?></a>
                </li>
                <!-- Enlace a la gestión de cuentas -->
                <li class="sidebar-menu-item <?php echo $controller == 'cuenta' ? 'active' : ''; ?>">
                    <a href="index.php?controller=cuenta&action=listar"><?php echo $lang['accounts']; ?></a>
                </li>
                <!-- Enlace a la gestión de transacciones -->
                <li class="sidebar-menu-item <?php echo $controller == 'transaccion' ? 'active' : ''; ?>">
                    <a href="index.php?controller=transaccion&action=listar"><?php echo $lang['transactions']; ?></a>
                </li>
                <!-- Enlace a la gestión de reportes -->
                <li class="sidebar-menu-item <?php echo $controller == 'reporte' ? 'active' : ''; ?>">
                    <a href="index.php?controller=reporte&action=index"><?php echo $lang['reports']; ?></a>
                </li>
                <!-- Enlace a la gestión de oficinas -->
                <li class="sidebar-menu-item <?php echo $controller == 'oficina' ? 'active' : ''; ?>">
                    <a href="index.php?controller=oficina&action=listar"><?php echo $lang['branches']; ?></a>
                </li>
                <!-- Enlace a la gestión de cajeros automáticos (ATM) -->
                <li class="sidebar-menu-item <?php echo $controller == 'atm' ? 'active' : ''; ?>">
                    <a href="index.php?controller=atm&action=listar"><?php echo $lang['atm']; ?></a>
                </li>
            </ul>
        </div>
        
        <!-- Contenido principal de la página -->
        <div class="content">
            <?php
            // Mostrar mensaje flash si existe
            $flashMessage = $this->session->getFlashMessage();
            if ($flashMessage) {
                echo '<div class="alert alert-' . $flashMessage['type'] . '">' . $flashMessage['message'] . '</div>';
            }
            ?>
            
            <!-- Incluir la vista dinámica del contenido -->
            <?php include $contentView; ?>
        </div>
    </div>
    
    <!-- Enlace al archivo JavaScript principal -->
    <script src="assets/js/main.js"></script>
</body>
</html>