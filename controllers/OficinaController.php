<?php

class OficinaController {
    // Propiedades
    private $db;
    private $session;
    
    //Constructor
    public function __construct() {
        // Inicializar conexión a la base de datos
        $database = new Database();
        $this->db = $database->connect();
        
        // Inicializar session
        $this->session = new Session();
    }
    
    //Listar oficinas
    public function listar() {
        global $lang;
        
        // Obtener todas las oficinas
        $oficina = new Oficina($this->db);
        $result = $oficina->obtenerTodas();
        $oficinas = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Mostrar vista
        include_once 'views/oficinas/listar.php';
    }
    
    //Ver detalles de una oficina
    public function ver() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', 'ID de oficina no especificado');
            header('Location: index.php?controller=oficina&action=listar');
            exit;
        }
        
        $idOficina = (int)$_GET['id'];
        
        // Obtener datos de la oficina
        $oficina = new Oficina($this->db);
        $oficina->idOficina = $idOficina;
        
        if (!$oficina->obtenerUna()) {
            $this->session->setFlashMessage('error', 'Oficina no encontrada');
            header('Location: index.php?controller=oficina&action=listar');
            exit;
        }
        
        // Obtener clientes de esta oficina
        $cliente = new Cliente($this->db);
        $resultClientes = $cliente->buscar($idOficina);
        $clientes = $resultClientes->fetchAll(PDO::FETCH_ASSOC);
        
        // Mostrar vista
        include_once 'views/oficinas/ver.php';
    }
    
    //Crear oficina
    public function crear() {
        global $lang;
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
            $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
            $central = isset($_POST['central']) ? 1 : 0;
            
            // Validar campos obligatorios
            if (empty($nombre) || empty($direccion)) {
                $this->session->setFlashMessage('error', 'Los campos Nombre y Dirección son obligatorios');
                header('Location: index.php?controller=oficina&action=crear');
                exit;
            }
            
            // Crear oficina
            $oficina = new Oficina($this->db);
            $oficina->nombre = $nombre;
            $oficina->direccion = $direccion;
            $oficina->telefono = $telefono;
            $oficina->central = $central;
            
            if ($oficina->crear()) {
                $this->session->setFlashMessage('success', 'Oficina creada exitosamente');
                header('Location: index.php?controller=oficina&action=listar');
                exit;
            } else {
                $this->session->setFlashMessage('error', 'Error al crear la oficina');
                header('Location: index.php?controller=oficina&action=crear');
                exit;
            }
        }
        
        // Mostrar vista
        include_once 'views/oficinas/crear.php';
    }
    
    //Editar oficina
    public function editar() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', 'ID de oficina no especificado');
            header('Location: index.php?controller=oficina&action=listar');
            exit;
        }
        
        $idOficina = (int)$_GET['id'];
        
        // Obtener datos de la oficina
        $oficina = new Oficina($this->db);
        $oficina->idOficina = $idOficina;
        
        if (!$oficina->obtenerUna()) {
            $this->session->setFlashMessage('error', 'Oficina no encontrada');
            header('Location: index.php?controller=oficina&action=listar');
            exit;
        }
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
            $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
            $central = isset($_POST['central']) ? 1 : 0;
            
            // Validar campos obligatorios
            if (empty($nombre) || empty($direccion)) {
                $this->session->setFlashMessage('error', 'Los campos Nombre y Dirección son obligatorios');
                header('Location: index.php?controller=oficina&action=editar&id=' . $idOficina);
                exit;
            }
            
            // Actualizar oficina
            $oficina->nombre = $nombre;
            $oficina->direccion = $direccion;
            $oficina->telefono = $telefono;
            $oficina->central = $central;
            
            if ($oficina->actualizar()) {
                $this->session->setFlashMessage('success', 'Oficina actualizada exitosamente');
                header('Location: index.php?controller=oficina&action=listar');
                exit;
            } else {
                $this->session->setFlashMessage('error', 'Error al actualizar la oficina');
                header('Location: index.php?controller=oficina&action=editar&id=' . $idOficina);
                exit;
            }
        }
        
        // Mostrar vista
        include_once 'views/oficinas/editar.php';
    }
    
    //Eliminar oficina
    public function eliminar() {
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', 'ID de oficina no especificado');
            header('Location: index.php?controller=oficina&action=listar');
            exit;
        }
        
        $idOficina = (int)$_GET['id'];
        
        // Eliminar oficina
        $oficina = new Oficina($this->db);
        $oficina->idOficina = $idOficina;
        
        if ($oficina->eliminar()) {
            $this->session->setFlashMessage('success', 'Oficina eliminada exitosamente');
        } else {
            $this->session->setFlashMessage('error', 'No se puede eliminar la oficina porque tiene clientes asociados');
        }
        
        header('Location: index.php?controller=oficina&action=listar');
        exit;
    }
}
?>