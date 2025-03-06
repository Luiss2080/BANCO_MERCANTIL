<div class="sidebar">
    <ul class="menu">
        <!-- Enlace a la página de bienvenida -->
        <li class="<?php echo isset($_GET['controller']) && $_GET['controller'] == 'bienvenida' ? 'active' : ''; ?>">
            <a href="index.php?controller=dashboard&action=index">Bienvenido</a>
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
</div>