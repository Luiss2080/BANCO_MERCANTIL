<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <!-- ---------- HEADER ---------- -->
    <div class="header">
        <div class="logo-container">
            <!-- Logo del Banco Mercantil -->
            <img src="/BANCO_CAMBA/assets/img/logo.png" alt="Logo Banco Mercantil" class="logo-image">
            <div class="logo">Banco Mercantil</div>
        </div>
        <div class="user-info">
            <!-- Información del usuario logueado -->
            <div class="user-name">Usuario : <?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario'; ?></div>
            <!-- Botón para cerrar sesión -->
            <a href="index.php?controller=usuario&action=cerrarSesion" class="btn btn-secondary">Cerrar Sesión</a>
        </div>
    </div>

    <!-- ---------- MAIN CONTAINER ---------- -->
    <div class="main-container">

        <!-- ---------- SIDEBAR ---------- -->
        <div class="sidebar">
            <ul class="sidebar-menu">
                <!-- Menú de navegación con enlaces a diferentes secciones del dashboard -->
                <li class="sidebar-menu-item <?php echo $controller == 'bienvenida' ? 'active' : ''; ?>">
                    <a href="index.php?controller=bienvenida&action=index"><?php echo $lang['home']; ?></a>
                </li>
                <li class="sidebar-menu-item <?php echo $controller == 'dashboard' ? 'active' : ''; ?>">
                    <a href="index.php?controller=dashboard&action=index"><?php echo $lang['dashboard']; ?></a>
                </li>
                <li class="sidebar-menu-item <?php echo $controller == 'cliente' ? 'active' : ''; ?>">
                    <a href="index.php?controller=cliente&action=listar"><?php echo $lang['clients']; ?></a>
                </li>
                <li class="sidebar-menu-item <?php echo $controller == 'cuenta' ? 'active' : ''; ?>">
                    <a href="index.php?controller=cuenta&action=listar"><?php echo $lang['accounts']; ?></a>
                </li>
                <li class="sidebar-menu-item <?php echo $controller == 'transaccion' ? 'active' : ''; ?>">
                    <a href="index.php?controller=transaccion&action=listar"><?php echo $lang['transactions']; ?></a>
                </li>
                <li class="sidebar-menu-item <?php echo $controller == 'reporte' ? 'active' : ''; ?>">
                    <a href="index.php?controller=reporte&action=index"><?php echo $lang['reports']; ?></a>
                </li>
                <li class="sidebar-menu-item <?php echo $controller == 'oficina' ? 'active' : ''; ?>">
                    <a href="index.php?controller=oficina&action=listar"><?php echo $lang['branches']; ?></a>
                </li>
                <li class="sidebar-menu-item <?php echo $controller == 'atm' ? 'active' : ''; ?>">
                    <a href="index.php?controller=atm&action=listar"><?php echo $lang['atm']; ?></a>
                </li>
            </ul>
        </div>

        <!-- ---------- CONTENT ---------- -->
        <div class="content">
            <h1>Dashboard</h1>

            <!-- ---------- DASHBOARD CARDS ---------- -->
            <div class="dashboard">
                <!-- Tarjeta de Clientes -->
                <div class="card">
                    <div class="card-header">Clientes</div>
                    <div class="card-body">
                        <p>Total de clientes: <span id="total-clients"><?php echo isset($totalClientes) ? $totalClientes : 0; ?></span></p>
                        <a href="index.php?controller=cliente&action=crear" class="btn btn-primary">Nuevo Cliente</a>
                    </div>
                </div>

                <!-- Tarjeta de Cuentas -->
                <div class="card">
                    <div class="card-header">Cuentas</div>
                    <div class="card-body">
                        <p>Total de cuentas: <span id="total-accounts"><?php echo isset($totalCuentas) ? $totalCuentas : 0; ?></span></p>
                        <a href="index.php?controller=cuenta&action=crear" class="btn btn-primary">Nueva Cuenta</a>
                    </div>
                </div>

                <!-- Tarjeta de Transacciones -->
                <div class="card">
                    <div class="card-header">Transacciones</div>
                    <div class="card-body">
                        <p>Transacciones hoy: <span id="today-transactions"><?php echo isset($transaccionesHoy) ? $transaccionesHoy : 0; ?></span></p>
                        <a href="index.php?controller=transaccion&action=crear" class="btn btn-primary">Nueva Transacción</a>
                    </div>
                </div>
            </div>

            <!-- ---------- TRANSACCIONES RECIENTES ---------- -->
            <div class="card">
                <div class="card-header">Transacciones Recientes</div>
                <div class="card-body">
                    <p>No hay transacciones recientes.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>