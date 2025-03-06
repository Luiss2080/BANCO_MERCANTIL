<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema Bancario</title>
    <!-- Enlace a la hoja de estilos principal -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css">
</head>
<body>
    <!-- Inclusión del encabezado de la página -->
    <?php include 'views/includes/header.php'; ?>
    
    <!-- Contenedor principal de la página -->
    <div class="container">
        <!-- ---------- SECCIÓN DE BIENVENIDA ---------- -->
        <div class="welcome-section">
            <h1>Bienvenido al Sistema de Gestión Bancaria</h1>
            <p class="subtitle">Una plataforma completa para gestionar clientes, cuentas y transacciones bancarias</p>
        </div>
        
        <!-- ---------- PANEL DE CONTROL CON TARJETAS DE FUNCIONALIDADES ---------- -->
        <div class="dashboard">
            <!-- ---------- TARJETA DE GESTIÓN DE CLIENTES ---------- -->
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i> <!-- Ícono de usuarios -->
                </div>
                <div class="card-content">
                    <h2>Gestión de Clientes</h2>
                    <p>Administre información de clientes, registre nuevos clientes y actualice sus datos.</p>
                    <a href="<?php echo BASE_URL; ?>clientes/listar" class="btn btn-primary">Ir a Clientes</a>
                </div>
            </div>
            
            <!-- ---------- TARJETA DE GESTIÓN DE CUENTAS ---------- -->
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-wallet"></i> <!-- Ícono de billetera -->
                </div>
                <div class="card-content">
                    <h2>Gestión de Cuentas</h2>
                    <p>Cree y administre cuentas bancarias, consulte saldos y vea movimientos.</p>
                    <a href="<?php echo BASE_URL; ?>cuentas/listar" class="btn btn-primary">Ir a Cuentas</a>
                </div>
            </div>
            
            <!-- ---------- TARJETA DE TRANSACCIONES ---------- -->
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-exchange-alt"></i> <!-- Ícono de intercambio -->
                </div>
                <div class="card-content">
                    <h2>Transacciones</h2>
                    <p>Realice depósitos, retiros y transferencias entre cuentas de manera rápida y segura.</p>
                    <div class="btn-group">
                        <a href="<?php echo BASE_URL; ?>cuentas/depositar" class="btn btn-success">Depósito</a>
                        <a href="<?php echo BASE_URL; ?>cuentas/retirar" class="btn btn-warning">Retiro</a>
                        <a href="<?php echo BASE_URL; ?>cuentas/transferir" class="btn btn-info">Transferencia</a>
                    </div>
                </div>
            </div>
            
            <!-- ---------- TARJETA DE CONSULTAS ---------- -->
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-search-dollar"></i> <!-- Ícono de búsqueda -->
                </div>
                <div class="card-content">
                    <h2>Consultas</h2>
                    <p>Consulte saldos y movimientos de cuentas de forma rápida y sencilla.</p>
                    <a href="<?php echo BASE_URL; ?>cuentas/consultarSaldo" class="btn btn-primary">Consultar Saldo</a>
                </div>
            </div>
        </div>
        
        <!-- ---------- SECCIÓN DE ACCESO RÁPIDO ---------- -->
        <div class="quick-access">
            <h2>Acceso Rápido</h2>
            <div class="quick-links">
                <!-- ---------- ENLACE PARA CREAR UN NUEVO CLIENTE ---------- -->
                <a href="<?php echo BASE_URL; ?>clientes/crear" class="quick-link">
                    <i class="fas fa-user-plus"></i> <!-- Ícono de agregar usuario -->
                    <span>Nuevo Cliente</span>
                </a>
                
                <!-- ---------- ENLACE PARA CREAR UNA NUEVA CUENTA ---------- -->
                <a href="<?php echo BASE_URL; ?>cuentas/crear" class="quick-link">
                    <i class="fas fa-plus-circle"></i> <!-- Ícono de agregar -->
                    <span>Nueva Cuenta</span>
                </a>
                
                <!-- ---------- ENLACE PARA REALIZAR UN DEPÓSITO ---------- -->
                <a href="<?php echo BASE_URL; ?>cuentas/depositar" class="quick-link">
                    <i class="fas fa-hand-holding-usd"></i> <!-- Ícono de depósito -->
                    <span>Depósito</span>
                </a>
                
                <!-- ---------- ENLACE PARA REALIZAR UN RETIRO ---------- -->
                <a href="<?php echo BASE_URL; ?>cuentas/retirar" class="quick-link">
                    <i class="fas fa-money-bill-wave"></i> <!-- Ícono de retiro -->
                    <span>Retiro</span>
                </a>
                
                <!-- ---------- ENLACE PARA REALIZAR UNA TRANSFERENCIA ---------- -->
                <a href="<?php echo BASE_URL; ?>cuentas/transferir" class="quick-link">
                    <i class="fas fa-exchange-alt"></i> <!-- Ícono de transferencia -->
                    <span>Transferencia</span>
                </a>
                
                <!-- ---------- ENLACE PARA CONSULTAR SALDO ---------- -->
                <a href="<?php echo BASE_URL; ?>cuentas/consultarSaldo" class="quick-link">
                    <i class="fas fa-search"></i> <!-- Ícono de búsqueda -->
                    <span>Consultar Saldo</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- ---------- PIE DE PÁGINA ---------- -->
    <?php include 'views/includes/footer.php'; ?>
    <!-- Enlace al archivo de scripts JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/scripts.js"></script>
</body>
</html>