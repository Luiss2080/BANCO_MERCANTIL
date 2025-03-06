<?php

class CuentaController {
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
    
    //Listar cuentas
    public function listar() {
        global $lang;
        
        // Obtener todas las cuentas
        $cuenta = new Cuenta($this->db);
        $result = $cuenta->obtenerTodas();
        $cuentas = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Definir título de la página
        $pageTitle = $lang['account_list'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cuenta';
        
        // Definir vista a incluir
        $contentView = 'views/cuentas/listar.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Ver detalles de una cuenta
    public function ver() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['account_id_not_specified']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        $idCuenta = (int)$_GET['id'];
        
        // Obtener datos de la cuenta
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuenta;
        
        if (!$cuenta->obtenerUna()) {
            $this->session->setFlashMessage('error', $lang['account_not_found']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        // Obtener datos del cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $cuenta->idPersona;
        $cliente->obtenerUno();
        
        // Obtener transacciones de la cuenta
        $transaccion = new Transaccion($this->db);
        $result = $transaccion->obtenerPorCuenta($idCuenta);
        $transacciones = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener tarjetas asociadas a la cuenta
        $tarjeta = new Tarjeta($this->db);
        $resultTarjetas = $tarjeta->obtenerPorCuenta($idCuenta);
        $tarjetas = $resultTarjetas->fetchAll(PDO::FETCH_ASSOC);
        
        // Definir título de la página
        $pageTitle = $lang['account_details'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cuenta';
        
        // Definir vista a incluir
        $contentView = 'views/cuentas/ver.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Crear cuenta
    public function crear() {
        global $lang;
        
        // Obtener clientes para seleccionar
        $cliente = new Cliente($this->db);
        $result = $cliente->obtenerTodos();
        $clientes = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $idPersona = isset($_POST['idPersona']) ? (int)$_POST['idPersona'] : 0;
            $tipoCuenta = isset($_POST['tipoCuenta']) ? (int)$_POST['tipoCuenta'] : 0;
            $tipoMoneda = isset($_POST['tipoMoneda']) ? (int)$_POST['tipoMoneda'] : 0;
            $saldoInicial = isset($_POST['saldoInicial']) ? (float)$_POST['saldoInicial'] : 0;
            
            // Validar campos obligatorios
            if (empty($idPersona) || empty($tipoCuenta) || empty($tipoMoneda)) {
                $this->session->setFlashMessage('error', $lang['required_fields_error']);
                header('Location: index.php?controller=cuenta&action=crear');
                exit;
            }
            
            // Crear cuenta
            $cuenta = new Cuenta($this->db);
            $cuenta->idPersona = $idPersona;
            $cuenta->tipoCuenta = $tipoCuenta;
            $cuenta->tipoMoneda = $tipoMoneda;
            $cuenta->saldo = $saldoInicial;
            $cuenta->fechaApertura = date('Y-m-d');
            $cuenta->estado = 1; // Activa
            
            if ($cuenta->crear()) {
                // Si se creó la cuenta y hay saldo inicial, registrar transacción de depósito inicial
                if ($saldoInicial > 0) {
                    $transaccion = new Transaccion($this->db);
                    $transaccion->idCuenta = $cuenta->idCuenta;
                    $transaccion->tipoTransaccion = 2; // Depósito
                    $transaccion->monto = $saldoInicial;
                    $transaccion->descripcion = $lang['initial_deposit'];
                    $transaccion->fecha = date('Y-m-d');
                    $transaccion->hora = date('H:i:s');
                    $transaccion->crear();
                }
                
                $this->session->setFlashMessage('success', $lang['account_saved']);
                
                // Redireccionar a la página del cliente si se especificó un ID de cliente
                if (isset($_POST['idPersona']) && !empty($_POST['idPersona'])) {
                    header('Location: index.php?controller=cliente&action=ver&id=' . $_POST['idPersona']);
                } else {
                    header('Location: index.php?controller=cuenta&action=listar');
                }
                exit;
            } else {
                $this->session->setFlashMessage('error', $lang['account_save_error']);
                header('Location: index.php?controller=cuenta&action=crear');
                exit;
            }
        }
        
        // Definir título de la página
        $pageTitle = $lang['new_account'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cuenta';
        
        // Definir vista a incluir
        $contentView = 'views/cuentas/crear.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Editar cuenta
    public function editar() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['account_id_not_specified']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        $idCuenta = (int)$_GET['id'];
        
        // Obtener datos de la cuenta
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuenta;
        
        if (!$cuenta->obtenerUna()) {
            $this->session->setFlashMessage('error', $lang['account_not_found']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        // Obtener datos del cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $cuenta->idPersona;
        $cliente->obtenerUno();
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $tipoCuenta = isset($_POST['tipoCuenta']) ? (int)$_POST['tipoCuenta'] : 0;
            $tipoMoneda = isset($_POST['tipoMoneda']) ? (int)$_POST['tipoMoneda'] : 0;
            $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;
            
            // Validar campos obligatorios
            if (empty($tipoCuenta) || empty($tipoMoneda) || $estado === 0) {
                $this->session->setFlashMessage('error', $lang['required_fields_error']);
                header('Location: index.php?controller=cuenta&action=editar&id=' . $idCuenta);
                exit;
            }
            
            // Actualizar cuenta
            $cuenta->tipoCuenta = $tipoCuenta;
            $cuenta->tipoMoneda = $tipoMoneda;
            $cuenta->estado = $estado;
            
            if ($cuenta->actualizar()) {
                $this->session->setFlashMessage('success', $lang['account_updated']);
                header('Location: index.php?controller=cuenta&action=ver&id=' . $idCuenta);
                exit;
            } else {
                $this->session->setFlashMessage('error', $lang['account_update_error']);
                header('Location: index.php?controller=cuenta&action=editar&id=' . $idCuenta);
                exit;
            }
        }
        
        // Definir título de la página
        $pageTitle = $lang['edit_account'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cuenta';
        
        // Definir vista a incluir
        $contentView = 'views/cuentas/editar.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Consultar saldo
    public function consultarSaldo() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['account_id_not_specified']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        $idCuenta = (int)$_GET['id'];
        
        // Obtener datos de la cuenta
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuenta;
        
        if (!$cuenta->obtenerUna()) {
            $this->session->setFlashMessage('error', $lang['account_not_found']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        // Obtener datos del cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $cuenta->idPersona;
        $cliente->obtenerUno();
        
        // Definir título de la página
        $pageTitle = $lang['check_balance'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cuenta';
        
        // Definir vista a incluir
        $contentView = 'views/cuentas/consultar_saldo.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Generar historial de transacciones
    public function historial() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['account_id_not_specified']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        $idCuenta = (int)$_GET['id'];
        
        // Obtener datos de la cuenta
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuenta;
        
        if (!$cuenta->obtenerUna()) {
            $this->session->setFlashMessage('error', $lang['account_not_found']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        // Obtener datos del cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $cuenta->idPersona;
        $cliente->obtenerUno();
        
        // Fechas por defecto (último mes)
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d', strtotime('-1 month'));
        
        // Si se envió el formulario, usar las fechas especificadas
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fechaInicio = isset($_POST['fechaInicio']) ? trim($_POST['fechaInicio']) : $fechaInicio;
            $fechaFin = isset($_POST['fechaFin']) ? trim($_POST['fechaFin']) : $fechaFin;
        }
        
        // Obtener transacciones por rango de fechas
        $transaccion = new Transaccion($this->db);
        $result = $transaccion->obtenerPorRangoFechas($idCuenta, $fechaInicio, $fechaFin);
        $transacciones = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Definir título de la página
        $pageTitle = $lang['transaction_history'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cuenta';
        
        // Definir vista a incluir
        $contentView = 'views/cuentas/historial.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Imprimir historial de transacciones
    public function imprimirHistorial() {
        global $lang;
        
        // Verificar si se especificaron los parámetros necesarios
        if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['inicio']) || empty($_GET['inicio']) || !isset($_GET['fin']) || empty($_GET['fin'])) {
            $this->session->setFlashMessage('error', $lang['insufficient_parameters']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        $idCuenta = (int)$_GET['id'];
        $fechaInicio = $_GET['inicio'];
        $fechaFin = $_GET['fin'];
        
        // Obtener datos de la cuenta
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuenta;
        
        if (!$cuenta->obtenerUna()) {
            $this->session->setFlashMessage('error', $lang['account_not_found']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        // Obtener datos del cliente
        $cliente = new Cliente($this->db);
        $cliente->idPersona = $cuenta->idPersona;
        $cliente->obtenerUno();
        
        // Obtener transacciones por rango de fechas
        $transaccion = new Transaccion($this->db);
        $result = $transaccion->obtenerPorRangoFechas($idCuenta, $fechaInicio, $fechaFin);
        $transacciones = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Mostrar vista de impresión (sin el layout)
        include_once 'views/cuentas/imprimir_historial.php';
    }
    
    //Crear tarjeta para una cuenta
    public function crearTarjeta() {
        global $lang;
        
        // Verificar si se especificó un ID
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            $this->session->setFlashMessage('error', $lang['account_id_not_specified']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        $idCuenta = (int)$_GET['id'];
        
        // Obtener datos de la cuenta
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuenta;
        
        if (!$cuenta->obtenerUna()) {
            $this->session->setFlashMessage('error', $lang['account_not_found']);
            header('Location: index.php?controller=cuenta&action=listar');
            exit;
        }
        
        // Verificar si la cuenta ya tiene una tarjeta
        $tarjeta = new Tarjeta($this->db);
        if ($tarjeta->cuentaTieneTarjeta($idCuenta)) {
            $this->session->setFlashMessage('error', $lang['account_already_has_card']);
            header('Location: index.php?controller=cuenta&action=ver&id=' . $idCuenta);
            exit;
        }
        
        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            $pin = isset($_POST['pin']) ? trim($_POST['pin']) : '';
            $pinConfirmacion = isset($_POST['pin_confirmacion']) ? trim($_POST['pin_confirmacion']) : '';
            
            // Validar PIN (4 dígitos)
            if (empty($pin) || !preg_match('/^\d{4}$/', $pin)) {
                $this->session->setFlashMessage('error', $lang['pin_must_be_4_digits']);
                header('Location: index.php?controller=cuenta&action=crearTarjeta&id=' . $idCuenta);
                exit;
            }
            
            // Validar confirmación de PIN
            if ($pin !== $pinConfirmacion) {
                $this->session->setFlashMessage('error', $lang['pins_dont_match']);
                header('Location: index.php?controller=cuenta&action=crearTarjeta&id=' . $idCuenta);
                exit;
            }
            
            // Crear tarjeta
            $tarjeta = new Tarjeta($this->db);
            $tarjeta->idCuenta = $idCuenta;
            $tarjeta->pin = $pin;
            $tarjeta->estado = 1; // Activa
            
            if ($tarjeta->crear()) {
                $this->session->setFlashMessage('success', $lang['card_created']);
                header('Location: index.php?controller=cuenta&action=ver&id=' . $idCuenta);
                exit;
            } else {
                $this->session->setFlashMessage('error', $lang['card_creation_error']);
                header('Location: index.php?controller=cuenta&action=crearTarjeta&id=' . $idCuenta);
                exit;
            }
        }
        
        // Definir título de la página
        $pageTitle = $lang['new_card'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'cuenta';
        
        // Definir vista a incluir
        $contentView = 'views/cuentas/crear_tarjeta.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
}
?>