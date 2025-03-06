<?php

class ClienteController {
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
    
    //Listar clientes
    public function listar() {
        global $lang;
        
        // Verificar si hay un término de búsqueda
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        
        // Obtener clientes
        $cliente = new Cliente($this->db);
        
        if (!empty($busqueda)) {
            $result = $cliente->buscar($busqueda);
        } else {
            $result = $cliente->obtenerTodos();
        }
        
        $clientes = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Definir título de la página
        $pageTitle = $lang['client_list'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cliente';
        
        // Definir vista a incluir
        $contentView = 'views/clientes/listar.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
       //Ver detalles de un cliente
    public function ver() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['client_id_not_specified']);
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        
        $idCliente = (int)$_GET['id'];
        
        // Obtener datos del cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $idCliente;
        
        if (!$cliente->obtenerUno()) {
            $this->session->setFlashMessage('error', $lang['client_not_found']);
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        
        // Obtener cuentas del cliente
        $cuenta = new Cuenta($this->db);
        $result = $cuenta->obtenerPorCliente($idCliente);
        $cuentas = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Definir título de la página
        $pageTitle = $lang['client_details'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cliente';
        
        // Definir vista a incluir
        $contentView = 'views/clientes/ver.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Crear cliente
    public function crear() {
        global $lang;
        
        // Obtener oficinas para seleccionar
        $oficina = new Oficina($this->db);
        $result = $oficina->obtenerTodas();
        $oficinas = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['apellidoPaterno']) ? trim($_POST['apellidoPaterno']) : '';
            $apellidoMaterno = isset($_POST['apellidoMaterno']) ? trim($_POST['apellidoMaterno']) : '';
            $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
            $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $fechaNacimiento = isset($_POST['fechaNacimiento']) ? trim($_POST['fechaNacimiento']) : '';
            $ci = isset($_POST['ci']) ? trim($_POST['ci']) : '';
            $idOficina = isset($_POST['idOficina']) ? (int)$_POST['idOficina'] : 0;
            
            // Validar campos obligatorios
            if (empty($nombre) || empty($apellidoPaterno) || empty($ci) || empty($idOficina)) {
                $this->session->setFlashMessage('error', $lang['required_fields_error']);
                header('Location: index.php?controller=cliente&action=crear');
                exit;
            }
            
            // Crear cliente
            $cliente = new Cliente($this->db);
            $cliente->nombre = $nombre;
            $cliente->apellidoPaterno = $apellidoPaterno;
            $cliente->apellidoMaterno = $apellidoMaterno;
            $cliente->direccion = $direccion;
            $cliente->telefono = $telefono;
            $cliente->email = $email;
            $cliente->fechaNacimiento = $fechaNacimiento;
            $cliente->ci = $ci;
            $cliente->idOficina = $idOficina;
            
            if ($cliente->crear()) {
                $this->session->setFlashMessage('success', $lang['client_saved']);
                header('Location: index.php?controller=cliente&action=listar');
                exit;
            } else {
                $this->session->setFlashMessage('error', $lang['client_save_error']);
                header('Location: index.php?controller=cliente&action=crear');
                exit;
            }
        }
        
        // Definir título de la página
        $pageTitle = $lang['new_client'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cliente';
        
        // Definir vista a incluir
        $contentView = 'views/clientes/crear.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Editar cliente
    public function editar() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['client_id_not_specified']);
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        
        $idCliente = (int)$_GET['id'];
        
        // Obtener datos del cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $idCliente;
        
        if (!$cliente->obtenerUno()) {
            $this->session->setFlashMessage('error', $lang['client_not_found']);
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        
        // Obtener oficinas para seleccionar
        $oficina = new Oficina($this->db);
        $result = $oficina->obtenerTodas();
        $oficinas = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $apellidoPaterno = isset($_POST['apellidoPaterno']) ? trim($_POST['apellidoPaterno']) : '';
            $apellidoMaterno = isset($_POST['apellidoMaterno']) ? trim($_POST['apellidoMaterno']) : '';
            $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
            $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $fechaNacimiento = isset($_POST['fechaNacimiento']) ? trim($_POST['fechaNacimiento']) : '';
            $ci = isset($_POST['ci']) ? trim($_POST['ci']) : '';
            $idOficina = isset($_POST['idOficina']) ? (int)$_POST['idOficina'] : 0;
            
            // Validar campos obligatorios
            if (empty($nombre) || empty($apellidoPaterno) || empty($ci) || empty($idOficina)) {
                $this->session->setFlashMessage('error', $lang['required_fields_error']);
                header('Location: index.php?controller=cliente&action=editar&id=' . $idCliente);
                exit;
            }
            
            // Actualizar cliente
            $cliente->nombre = $nombre;
            $cliente->apellidoPaterno = $apellidoPaterno;
            $cliente->apellidoMaterno = $apellidoMaterno;
            $cliente->direccion = $direccion;
            $cliente->telefono = $telefono;
            $cliente->email = $email;
            $cliente->fechaNacimiento = $fechaNacimiento;
            $cliente->ci = $ci;
            $cliente->idOficina = $idOficina;
            
            if ($cliente->actualizar()) {
                $this->session->setFlashMessage('success', $lang['client_updated']);
                header('Location: index.php?controller=cliente&action=listar');
                exit;
            } else {
                $this->session->setFlashMessage('error', $lang['client_update_error']);
                header('Location: index.php?controller=cliente&action=editar&id=' . $idCliente);
                exit;
            }
        }
        
        // Definir título de la página
        $pageTitle = $lang['edit_client'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cliente';
        
        // Definir vista a incluir
        $contentView = 'views/clientes/editar.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Eliminar cliente
    public function eliminar() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['client_id_not_specified']);
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
        
        $idCliente = (int)$_GET['id'];
        
        // Eliminar cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $idCliente;
        
        if ($cliente->eliminar()) {
            $this->session->setFlashMessage('success', $lang['client_deleted']);
        } else {
            $this->session->setFlashMessage('error', $lang['client_has_accounts']);
        }
        
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }
}
?>