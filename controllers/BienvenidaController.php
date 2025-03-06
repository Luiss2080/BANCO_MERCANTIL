<?php

class BienvenidaController {
    // Propiedades
    private $db;
    private $session;
    private $model;
    
    //Constructor
    public function __construct() {
        // Inicializar conexión a la base de datos
        $database = new Database();
        $this->db = $database->connect();
        
        // Inicializar session
        $this->session = new Session();
        
        // Inicializar modelo
        $this->model = new Bienvenida($this->db);
    }
    
    //Método para mostrar la página de bienvenida
    public function index() {
        global $lang;
        
        // Obtener estadísticas del sistema
        $estadisticas = $this->model->obtenerEstadisticas();
        
        // Obtener transacciones recientes
        $transaccionesRecientes = $this->model->obtenerTransaccionesRecientes();
        
        // Obtener información del sistema
        $infoSistema = $this->model->obtenerInfoSistema();
        
        // Definir título de la página
        $pageTitle = $lang['welcome_to'] . ' ' . $lang['app_name'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'bienvenida';
        
        // Definir vista a incluir
        $contentView = 'views/bienvenida/bienvenida.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Método para mostrar información del sistema
    public function acercaDe() {
        global $lang;
        
        // Obtener información del sistema
        $infoSistema = $this->model->obtenerInfoSistema();
        
        // Definir título de la página
        $pageTitle = $lang['about'] . ' ' . $lang['app_name'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'bienvenida';
        
        // Definir vista a incluir
        $contentView = 'views/bienvenida/acerca_de.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
}
?>