<?php
// Verifica si la variable $lang está definida. Si no, intenta incluir el archivo de idioma predeterminado.
if (!isset($lang)) {
    // Idioma predeterminado
    $default_lang = 'es'; 
    // Ruta al archivo de idioma
    $lang_file = dirname(__FILE__) . '/../../langs/' . $default_lang . '.php';
    
    // Si el archivo de idioma existe, lo incluye
    if (file_exists($lang_file)) {
        include_once($lang_file);
    } else {
        // Si no se encuentra el archivo de idioma, se define un array $lang con valores básicos en inglés
        $lang = [
            'welcome' => 'Welcome',
            'app_name' => 'Mercantil Bank',
            'welcome_message_title' => 'Integrated Banking Management System',
            'welcome_message' => 'Welcome to Mercantil Bank System',
            'quick_access' => 'Quick Access',
            'clients' => 'Clients',
            'accounts' => 'Accounts',
            'transactions' => 'Transactions',
            'reports' => 'Reports',
            'total_clients' => 'Total clients',
            'total_accounts' => 'Total accounts',
            'today_transactions' => 'Today\'s transactions',
            'total_balance_bolivianos' => 'Total Balance (Bs.)',
            'total_balance_dollars' => 'Total Balance ($)',
            'recent_transactions' => 'Recent Transactions',
            'view_all_transactions' => 'View all transactions',
            'no_transactions' => 'No recent transactions',
            'about' => 'About',
            'system_information' => 'System Information',
            'features' => 'Features',
            'client_management' => 'Client management',
            'account_management' => 'Account management',
            'transaction_processing' => 'Transaction processing',
            'reporting_system' => 'Reporting system',
            'multilanguage_support' => 'Multilanguage support'
        ];
    }
}

// Inicializa las estadísticas si no están definidas
if (!isset($estadisticas)) {
    $estadisticas = [
        'totalClientes' => 0,
        'totalCuentas' => 0,
        'transaccionesHoy' => 0,
        'saldoTotalBs' => '0.00',
        'saldoTotalUsd' => '0.00'
    ];
}

// Inicializa la información del sistema si no está definida
if (!isset($infoSistema)) {
    $infoSistema = [
        'version' => '1.0'
    ];
}

// Inicializa el array de transacciones recientes si no está definido
if (!isset($transaccionesRecientes)) {
    $transaccionesRecientes = [];
}
?>



<!-- Enlaces a hojas de estilo -->
<link rel="stylesheet" href="assets/css/styleBienvenida.css"> 
<link rel="stylesheet" href="assets/css/styleFooter.css"> 

<!-- Contenedor principal -->
<div class="service-container">
    <div class="main-content">
        <!-- Sección de bienvenida -->
        <div class="welcome-section">
            <h1 class="welcome-title">¡BIENVENIDO AL BANCO MERCANTIL!</h1>

            <!-- Espaciador -->
            <div style="height: 30px;"></div>

            <!-- Sección de iconos/tarjetas -->
            <div class="icons-section">
                <div class="movie-grid">
                    <!-- Tarjeta 1: Liderazgo -->
                    <div class="movie-card">
                        <img src="/BANCO_CAMBA/assets/img/Liderazgo.jpg" alt="Icono Liderazgo">
                        <div class="card-overlay">
                            <h3 class="card-title">Liderazgo</h3>
                            <p class="card-description">"Ser reconocidos como el mejor banco financiero."</p>
                        </div>
                    </div>

                    <!-- Tarjeta 2: Servicio al cliente -->
                    <div class="movie-card">
                        <img src="/BANCO_CAMBA/assets/img/servicio.jpg" alt="Icono Servicio al cliente">
                        <div class="card-overlay">
                            <h3 class="card-title">Servicio al cliente</h3>
                            <p class="card-description">"Satisfacer las expectativas de nuestros clientes"</p>
                        </div>
                    </div>

                    <!-- Tarjeta 3: Calidad y confiabilidad -->
                    <div class="movie-card">
                        <img src="/BANCO_CAMBA/assets/img/calidad_confiabilidad.jpeg" alt="Icono Calidad y confiabilidad">
                        <div class="card-overlay">
                            <h3 class="card-title">Confiabilidad</h3>
                            <p class="card-description">"Cumplir eficientemente con los compromisos pactados."</p>
                        </div>
                    </div>

                    <!-- Tarjeta 4: Integridad -->
                    <div class="movie-card">
                        <img src="/BANCO_CAMBA/assets/img/integridad.jpeg" alt="Icono Integridad">
                        <div class="card-overlay">
                            <h3 class="card-title">Integridad</h3>
                            <p class="card-description">"Actuar con honestidad, lealtad y ética profesional"</p>
                        </div>
                    </div>

                    <!-- Tarjeta 5: Excelencia y profesionalismo -->
                    <div class="movie-card">
                        <img src="/BANCO_CAMBA/assets/img/excelencia_profesionalismo.jpeg" alt="Icono Excelencia y profesionalismo">
                        <div class="card-overlay">
                            <h3 class="card-title">Profesionalismo</h3>
                            <p class="card-description">"Desempeñar una gestión sobresaliente"</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Espaciador -->
            <div style="height: 20px;"></div>

            <!-- Mensaje de bienvenida -->
            <p class="welcome-text">
                "🤝 Juntos, construimos un futuro sólido y próspero. 💪✨ Tu confianza impulsa nuestro crecimiento 🚀 para brindarte soluciones financieras 💰 y seguras 🔒. 
                En nuestro banco, trabajamos para ofrecerte excelencia ⭐, apoyando tus metas 🎯 y contribuyendo al desarrollo sostenible 🌱 de nuestra sociedad."
            </p>
        </div>
    </div>

    <!-- Footer fijo -->
    <div class="footer-full-width">
        <p>
            © 2025 Banco Mercantil Santa Cruz S.A. | 
            <a href="#" class="hover:underline">Términos y Condiciones</a> | 
            <a href="#" class="hover:underline">Política de Privacidad</a>
        </p>
    </div>
</div>

