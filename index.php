<?php
/* ---------- BANCO CAMBA - PUNTO DE ENTRADA PRINCIPAL ---------- */

// Implementación de cabeceras de seguridad HTTP
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline';");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Configuración para ocultar errores en producción
error_reporting(0);

// Iniciar sesión
session_start();

// Incluir archivos de configuración y utilidades
require_once 'config/database.php';
require_once 'utils/Session.php';
require_once 'utils/Auth.php';
require_once 'utils/Autoloader.php';

// Cargar modelos
require_once 'models/Oficina.php';
require_once 'models/Cliente.php';
require_once 'models/Cuenta.php';
require_once 'models/Transaccion.php';
require_once 'models/ATM.php';
require_once 'models/Tarjeta.php';
require_once 'models/Usuario.php';
require_once 'models/Bienvenida.php';

// Cargar el idioma seleccionado
$session = new Session();
$lang_code = $session->getLanguage();
require_once "langs/{$lang_code}.php";

// Determinar controlador y acción por defecto
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'bienvenida';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Verificar si el usuario ha iniciado sesión para acciones protegidas
$publicActions = ['login', 'autenticar', 'cambiarIdioma'];

if (!isset($_SESSION['user_id']) && !in_array($action, $publicActions) && $controller != 'api') {
    // Redirigir al login si no ha iniciado sesión y está intentando acceder a páginas protegidas
    header('Location: index.php?controller=usuario&action=login');
    exit;
}

// Cargar el controlador apropiado
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = "controllers/{$controllerName}.php";

// Verificar si el archivo del controlador existe
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Crear instancia del controlador
    $controllerInstance = new $controllerName();
    
    // Verificar si el método de acción existe
    if (method_exists($controllerInstance, $action)) {
        // Ejecutar la acción
        $controllerInstance->$action();
    } else {
        // Acción no encontrada
        include 'views/error.php';
    }
} else {
    // Controlador no encontrado
    include 'views/error.php';
}
?>