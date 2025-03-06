<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título de la página -->
    <title>Banco Mercantil</title>
    <!-- Enlace a la hoja de estilos principal -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Encabezado de la página -->
    <header>
        <div class="header-main">
            <!-- Título del banco -->
            <div class="header-title">
                <h1>Banco Mercantil</h1>
            </div>
            <!-- Acciones del usuario -->
            <div class="header-actions">
                <!-- Selector de idioma -->
                <div class="language-selector">
                    <a href="index.php?controller=language&action=change&lang=es" class="<?php echo !isset($_SESSION['lang']) || $_SESSION['lang'] == 'es' ? 'active' : ''; ?>">ES</a> | 
                    <a href="index.php?controller=language&action=change&lang=en" class="<?php echo isset($_SESSION['lang']) && $_SESSION['lang'] == 'en' ? 'active' : ''; ?>">EN</a>
                </div>
                
                <!-- Información del usuario si ha iniciado sesión -->
                <?php if (isset($_SESSION['usuario'])): ?>
                    <div class="user-info">
                        <span>Usuario: <?php echo $_SESSION['usuario']; ?></span>
                        <a href="index.php?controller=auth&action=logout" class="btn-logout">Cerrar Sesión</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Menú de navegación (solo visible si el usuario ha iniciado sesión) -->
        <?php if (isset($_SESSION['usuario'])): ?>
            <nav class="main-menu">
                <ul>
                    <!-- Enlace a la página de bienvenida -->
                    <li class="<?php echo isset($_GET['controller']) && $_GET['controller'] == 'dashboard' ? 'active' : ''; ?>">
                        <a href="index.php?controller=bienvenido&action=index">Bienvenido</a>
                    </li>
                    <!-- Enlace al dashboard -->
                    <li class="<?php echo isset($_GET['controller']) && $_GET['controller'] == 'dashboard' ? 'active' : ''; ?>">
                        <a href="index.php?controller=dashboard&action=index">Dashboard</a>
                    </li>
                    <!-- Enlace a la gestión de clientes -->
                    <li class="<?php echo isset($_GET['controller']) && $_GET['controller'] == 'cliente' ? 'active' : ''; ?>">
                        <a href="index.php?controller=cliente&action=listar">Clientes</a>
                    </li>
                    <!-- Enlace a la gestión de cuentas -->
                    <li class="<?php echo isset($_GET['controller']) && $_GET['controller'] == 'cuenta' ? 'active' : ''; ?>">
                        <a href="index.php?controller=cuenta&action=listar">Cuentas</a>
                    </li>
                    <!-- Enlace a la gestión de transacciones -->
                    <li class="<?php echo isset($_GET['controller']) && $_GET['controller'] == 'transaccion' ? 'active' : ''; ?>">
                        <a href="index.php?controller=transaccion&action=listar">Transacciones</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </header>
    
    <!-- Contenedor principal del contenido -->
    <main class="container">
        <!-- El contenido de cada página se insertará aquí -->
</body>
</html>