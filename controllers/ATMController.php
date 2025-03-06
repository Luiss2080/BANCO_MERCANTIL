<?php
//Controlador de ATM (Cajero Automático)
class ATMController {
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
    
      //Listar ATMs
    public function listar() {
        global $lang;
        
        // Obtener todos los ATMs
        $atm = new ATM($this->db);
        $result = $atm->obtenerTodos();
        $atms = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Mostrar vista
        include_once 'views/atm/listar.php';
    }
    
     //Ver detalles de un ATM
    public function ver() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', 'ID de ATM no especificado');
            header('Location: index.php?controller=atm&action=listar');
            exit;
        }
        
        $idATM = (int)$_GET['id'];
        
        // Obtener datos del ATM
        $atm = new ATM($this->db);
        $atm->idATM = $idATM;
        
        if (!$atm->obtenerUno()) {
            $this->session->setFlashMessage('error', 'ATM no encontrado');
            header('Location: index.php?controller=atm&action=listar');
            exit;
        }
        
        // Obtener las transacciones realizadas en este ATM
        $query = "SELECT t.*, ta.idATM, c.nroCuenta, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                  FROM TransaccionATM ta
                  INNER JOIN Transaccion t ON ta.idTransaccion = t.idTransaccion
                  INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                  INNER JOIN Persona p ON c.idPersona = p.idPersona
                  WHERE ta.idATM = :idATM
                  ORDER BY t.fecha DESC, t.hora DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idATM', $idATM);
        $stmt->execute();
        $transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Mostrar vista
        include_once 'views/atm/ver.php';
    }
    
       //Crear ATM
    public function crear() {
        global $lang;
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $ubicacion = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : '';
            
            // Validar campos obligatorios
            if (empty($ubicacion)) {
                $this->session->setFlashMessage('error', 'La ubicación es obligatoria');
                header('Location: index.php?controller=atm&action=crear');
                exit;
            }
            
            // Crear ATM
            $atm = new ATM($this->db);
            $atm->ubicacion = $ubicacion;
            
            if ($atm->crear()) {
                $this->session->setFlashMessage('success', 'ATM creado exitosamente');
                header('Location: index.php?controller=atm&action=listar');
                exit;
            } else {
                $this->session->setFlashMessage('error', 'Error al crear el ATM');
                header('Location: index.php?controller=atm&action=crear');
                exit;
            }
        }
        
        // Mostrar vista
        include_once 'views/atm/crear.php';
    }
    
      //Editar ATM
    public function editar() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', 'ID de ATM no especificado');
            header('Location: index.php?controller=atm&action=listar');
            exit;
        }
        
        $idATM = (int)$_GET['id'];
        
        // Obtener datos del ATM
        $atm = new ATM($this->db);
        $atm->idATM = $idATM;
        
        if (!$atm->obtenerUno()) {
            $this->session->setFlashMessage('error', 'ATM no encontrado');
            header('Location: index.php?controller=atm&action=listar');
            exit;
        }
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $ubicacion = isset($_POST['ubicacion']) ? trim($_POST['ubicacion']) : '';
            
            // Validar campos obligatorios
            if (empty($ubicacion)) {
                $this->session->setFlashMessage('error', 'La ubicación es obligatoria');
                header('Location: index.php?controller=atm&action=editar&id=' . $idATM);
                exit;
            }
            
            // Actualizar ATM
            $atm->ubicacion = $ubicacion;
            
            if ($atm->actualizar()) {
                $this->session->setFlashMessage('success', 'ATM actualizado exitosamente');
                header('Location: index.php?controller=atm&action=listar');
                exit;
            } else {
                $this->session->setFlashMessage('error', 'Error al actualizar el ATM');
                header('Location: index.php?controller=atm&action=editar&id=' . $idATM);
                exit;
            }
        }
        
        // Mostrar vista
        include_once 'views/atm/editar.php';
    }
    
      //Eliminar ATM
    public function eliminar() {
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', 'ID de ATM no especificado');
            header('Location: index.php?controller=atm&action=listar');
            exit;
        }
        
        $idATM = (int)$_GET['id'];
        
        // Eliminar ATM
        $atm = new ATM($this->db);
        $atm->idATM = $idATM;
        
        if ($atm->eliminar()) {
            $this->session->setFlashMessage('success', 'ATM eliminado exitosamente');
        } else {
            $this->session->setFlashMessage('error', 'No se puede eliminar el ATM porque tiene transacciones asociadas');
        }
        
        header('Location: index.php?controller=atm&action=listar');
        exit;
    }
    
     //Simular interfaz de cajero automático
    public function simular() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', 'ID de ATM no especificado');
            header('Location: index.php?controller=atm&action=listar');
            exit;
        }
        
        $idATM = (int)$_GET['id'];
        
        // Obtener datos del ATM
        $atm = new ATM($this->db);
        $atm->idATM = $idATM;
        
        if (!$atm->obtenerUno()) {
            $this->session->setFlashMessage('error', 'ATM no encontrado');
            header('Location: index.php?controller=atm&action=listar');
            exit;
        }
        
        // Mostrar vista de simulación
        include_once 'views/atm/simular.php';
    }
}