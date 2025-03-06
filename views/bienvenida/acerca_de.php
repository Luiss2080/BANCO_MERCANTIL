<div class="about-container">
    <!-- Título de la página "Acerca de" -->
    <h1><?php echo $lang['about']; ?> <?php echo $infoSistema['nombre_sistema']; ?></h1>
    
    <div class="about-content">
        <!-- Logo del sistema -->
        <div class="about-logo">
            <img src="assets/img/logo.png" alt="<?php echo $infoSistema['nombre_sistema']; ?>" class="img-fluid">
        </div>
        
        <!-- Información sobre el sistema -->
        <div class="about-info">
            <h2><?php echo $infoSistema['nombre_sistema']; ?></h2>
            <!-- Versión del sistema -->
            <p class="version">Version <?php echo $infoSistema['version']; ?></p>
            
            <!-- Detalles sobre el sistema -->
            <div class="about-details">
                <!-- Descripción del sistema -->
                <p><?php echo $lang['about_description']; ?></p>
                
                <!-- Información técnica del sistema -->
                <h3><?php echo $lang['system_information']; ?></h3>
                <ul class="about-list">
                    <!-- Versión del sistema -->
                    <li><strong><?php echo $lang['version']; ?>:</strong> <?php echo $infoSistema['version']; ?></li>
                    <!-- Fecha de la última actualización -->
                    <li><strong><?php echo $lang['last_update']; ?>:</strong> <?php echo date('d/m/Y', strtotime($infoSistema['fecha_actualizacion'])); ?></li>
                    <!-- Desarrollador del sistema -->
                    <li><strong><?php echo $lang['developer']; ?>:</strong> <?php echo $infoSistema['developer']; ?></li>
                </ul>
                
                <!-- Características principales del sistema -->
                <h3><?php echo $lang['features']; ?></h3>
                <ul class="about-list">
                    <!-- Gestión de clientes -->
                    <li><?php echo $lang['client_management']; ?></li>
                    <!-- Gestión de cuentas -->
                    <li><?php echo $lang['account_management']; ?></li>
                    <!-- Procesamiento de transacciones -->
                    <li><?php echo $lang['transaction_processing']; ?></li>
                    <!-- Sistema de reportes -->
                    <li><?php echo $lang['reporting_system']; ?></li>
                    <!-- Soporte multilingüe -->
                    <li><?php echo $lang['multilanguage_support']; ?></li>
                    <!-- Compatibilidad con PWA (Progressive Web App) -->
                    <li><?php echo $lang['pwa_compatibility']; ?></li>
                </ul>
            </div>
            
            <!-- Acciones disponibles -->
            <div class="about-actions">
                <!-- Botón para regresar a la página de inicio -->
                <a href="index.php?controller=bienvenida&action=index" class="btn btn-primary"><?php echo $lang['back_to_home']; ?></a>
            </div>
        </div>
    </div>
</div>