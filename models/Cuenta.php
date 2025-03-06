<?php
/**
 * Modelo Cuenta
 */
class Cuenta {
    // Conexión a la base de datos
    private $conn;
    
    // Propiedades de la cuenta
    public $idCuenta;
    public $tipoCuenta; // 1: Cuenta de ahorro, 2: Cuenta corriente
    public $tipoMoneda; // 1: Bolivianos, 2: Dólares
    public $fechaApertura;
    public $estado; // 1: Activa, 2: Inactiva
    public $nroCuenta;
    public $saldo;
    public $idPersona;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todas las cuentas
     * @return PDOStatement
     */
    public function obtenerTodas() {
        $query = "SELECT c.*, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                  FROM Cuenta c
                  INNER JOIN Persona p ON c.idPersona = p.idPersona
                  ORDER BY c.fechaApertura DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener una cuenta
     * @return boolean
     */
    public function obtenerUna() {
        $query = "SELECT c.*, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                  FROM Cuenta c
                  INNER JOIN Persona p ON c.idPersona = p.idPersona
                  WHERE c.idCuenta = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->idCuenta);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->tipoCuenta = $row['tipoCuenta'];
            $this->tipoMoneda = $row['tipoMoneda'];
            $this->fechaApertura = $row['fechaApertura'];
            $this->estado = $row['estado'];
            $this->nroCuenta = $row['nroCuenta'];
            $this->saldo = $row['saldo'];
            $this->idPersona = $row['idPersona'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtener cuentas por cliente
     * @param int $idPersona
     * @return PDOStatement
     */
    public function obtenerPorCliente($idPersona) {
        $query = "SELECT * FROM Cuenta WHERE idPersona = :idPersona ORDER BY fechaApertura DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPersona', $idPersona);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Crear una cuenta
     * @return boolean
     */
    public function crear() {
        // Generar número de cuenta único
        $this->nroCuenta = $this->generarNumeroCuenta();
        
        $query = "INSERT INTO Cuenta
                 (tipoCuenta, tipoMoneda, fechaApertura, estado, nroCuenta, saldo, idPersona)
                 VALUES
                 (:tipoCuenta, :tipoMoneda, :fechaApertura, :estado, :nroCuenta, :saldo, :idPersona)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nroCuenta = htmlspecialchars(strip_tags($this->nroCuenta));
        
        // Si no se proporciona fecha de apertura, usar la fecha actual
        if (empty($this->fechaApertura)) {
            $this->fechaApertura = date('Y-m-d');
        }
        
        // Estado por defecto: Activa (1)
        if (empty($this->estado)) {
            $this->estado = 1;
        }
        
        // Saldo inicial
        if (empty($this->saldo)) {
            $this->saldo = 0;
        }
        
        // Vincular datos
        $stmt->bindParam(':tipoCuenta', $this->tipoCuenta);
        $stmt->bindParam(':tipoMoneda', $this->tipoMoneda);
        $stmt->bindParam(':fechaApertura', $this->fechaApertura);
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':nroCuenta', $this->nroCuenta);
        $stmt->bindParam(':saldo', $this->saldo);
        $stmt->bindParam(':idPersona', $this->idPersona);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->idCuenta = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar cuenta
     * @return boolean
     */
    public function actualizar() {
        $query = "UPDATE Cuenta SET
                tipoCuenta = :tipoCuenta,
                tipoMoneda = :tipoMoneda,
                estado = :estado
                WHERE idCuenta = :idCuenta";
        
        $stmt = $this->conn->prepare($query);
        
        // Vincular datos
        $stmt->bindParam(':idCuenta', $this->idCuenta);
        $stmt->bindParam(':tipoCuenta', $this->tipoCuenta);
        $stmt->bindParam(':tipoMoneda', $this->tipoMoneda);
        $stmt->bindParam(':estado', $this->estado);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar saldo de la cuenta
     * @param float $monto El monto a añadir o restar
     * @param boolean $esDeposito True si es depósito, false si es retiro
     * @return boolean
     */
    public function actualizarSaldo($monto, $esDeposito = true) {
        // Si es un retiro, verificar que haya saldo suficiente
        if (!$esDeposito && $this->saldo < $monto) {
            return false;
        }
        
        // Calcular nuevo saldo
        $nuevoSaldo = $esDeposito ? $this->saldo + $monto : $this->saldo - $monto;
        
        $query = "UPDATE Cuenta SET saldo = :saldo WHERE idCuenta = :idCuenta";
        
        $stmt = $this->conn->prepare($query);
        
        // Vincular datos
        $stmt->bindParam(':idCuenta', $this->idCuenta);
        $stmt->bindParam(':saldo', $nuevoSaldo);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->saldo = $nuevoSaldo;
            return true;
        }
        
        return false;
    }
    
    /**
     * Generar número de cuenta único
     * @return string
     */
    private function generarNumeroCuenta() {
        // Formato: AAAA-MM-DD-XXXX (donde XXXX es un número aleatorio)
        $fecha = date('Ymd');
        $aleatorio = mt_rand(1000, 9999);
        return $fecha . '-' . $aleatorio;
    }
    
    /**
     * Contar cuentas activas
     * @return int
     */
    public function contarCuentasActivas() {
        $query = "SELECT COUNT(*) as total FROM Cuenta WHERE estado = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }

    /**
 * Método para mostrar el formulario de depósito
 */
public function depositar() {
    global $lang;
    
    // Verificar si se proporcionó un ID de cuenta
    if (isset($_GET['idCuenta']) && !empty($_GET['idCuenta'])) {
        $idCuenta = (int)$_GET['idCuenta'];
        
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
    }
    
    // Definir título de la página
    $pageTitle = $lang['deposit_funds'];
    
    // Determinar el controlador actual para marcar el menú
    $controller = 'transaccion';
    
    // Definir vista a incluir
    $contentView = 'views/cuentas/depositar.php';
    
    // Mostrar plantilla principal
    include_once 'views/main.php';
}

/**
 * Método para buscar cuenta para depósito
 */
public function buscarCuentaDeposito() {
    global $lang;
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['nroCuenta']) || empty($_POST['nroCuenta'])) {
        $this->session->setFlashMessage('error', $lang['account_number_required']);
        header('Location: index.php?controller=transaccion&action=depositar');
        exit;
    }
    
    $nroCuenta = trim($_POST['nroCuenta']);
    
    // Buscar la cuenta por número
    $cuentaModel = new Cuenta($this->db);
    $stmt = $this->conn->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
    $stmt->bindParam(':nroCuenta', $nroCuenta);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $idCuenta = $row['idCuenta'];
        
        // Redirigir al formulario de depósito con el ID de la cuenta
        header('Location: index.php?controller=transaccion&action=depositar&idCuenta=' . $idCuenta);
        exit;
    } else {
        $this->session->setFlashMessage('error', $lang['account_not_found']);
        header('Location: index.php?controller=transaccion&action=depositar');
        exit;
    }
}

/**
 * Método para procesar el depósito
 */
public function procesarDeposito() {
    global $lang;
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['idCuenta']) || empty($_POST['idCuenta']) || !isset($_POST['monto']) || empty($_POST['monto'])) {
        $this->session->setFlashMessage('error', $lang['missing_required_fields']);
        header('Location: index.php?controller=transaccion&action=depositar');
        exit;
    }
    
    $idCuenta = (int)$_POST['idCuenta'];
    $monto = (float)$_POST['monto'];
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : $lang['deposit'];
    
    // Validar monto
    if ($monto <= 0) {
        $this->session->setFlashMessage('error', $lang['invalid_amount']);
        header('Location: index.php?controller=transaccion&action=depositar&idCuenta=' . $idCuenta);
        exit;
    }
    
    // Obtener cuenta
    $cuenta = new Cuenta($this->db);
    $cuenta->idCuenta = $idCuenta;
    
    if (!$cuenta->obtenerUna()) {
        $this->session->setFlashMessage('error', $lang['account_not_found']);
        header('Location: index.php?controller=transaccion&action=depositar');
        exit;
    }
    
    // Verificar si la cuenta está activa
    if ($cuenta->estado != 1) {
        $this->session->setFlashMessage('error', $lang['inactive_account_error']);
        header('Location: index.php?controller=transaccion&action=depositar&idCuenta=' . $idCuenta);
        exit;
    }
    
    // Iniciar transacción
    $this->db->beginTransaction();
    
    try {
        // Actualizar saldo
        if (!$cuenta->actualizarSaldo($monto, true)) {
            throw new Exception($lang['update_balance_error']);
        }
        
        // Registrar transacción
        $transaccion = new Transaccion($this->db);
        $transaccion->idCuenta = $idCuenta;
        $transaccion->tipo = 1; // Depósito
        $transaccion->monto = $monto;
        $transaccion->descripcion = $descripcion;
        $transaccion->fecha = date('Y-m-d H:i:s');
        $transaccion->saldoResultante = $cuenta->saldo;
        
        if (!$transaccion->crear()) {
            throw new Exception($lang['transaction_error']);
        }
        
        // Confirmar transacción
        $this->db->commit();
        
        $this->session->setFlashMessage('success', $lang['deposit_success']);
        header('Location: index.php?controller=cuenta&action=ver&id=' . $idCuenta);
        exit;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $this->db->rollBack();
        $this->session->setFlashMessage('error', $e->getMessage());
        header('Location: index.php?controller=transaccion&action=depositar&idCuenta=' . $idCuenta);
        exit;
    }
}

/**
 * Método para mostrar el formulario de retiro
 */
public function retirar() {
    global $lang;
    
    // Verificar si se proporcionó un ID de cuenta
    if (isset($_GET['idCuenta']) && !empty($_GET['idCuenta'])) {
        $idCuenta = (int)$_GET['idCuenta'];
        
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
    }
    
    // Definir título de la página
    $pageTitle = $lang['withdraw_funds'];
    
    // Determinar el controlador actual para marcar el menú
    $controller = 'transaccion';
    
    // Definir vista a incluir
    $contentView = 'views/cuentas/retirar.php';
    
    // Mostrar plantilla principal
    include_once 'views/main.php';
}

/**
 * Método para buscar cuenta para retiro
 */
public function buscarCuentaRetiro() {
    global $lang;
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['nroCuenta']) || empty($_POST['nroCuenta'])) {
        $this->session->setFlashMessage('error', $lang['account_number_required']);
        header('Location: index.php?controller=transaccion&action=retirar');
        exit;
    }
    
    $nroCuenta = trim($_POST['nroCuenta']);
    
    // Buscar la cuenta por número
    $cuentaModel = new Cuenta($this->db);
    $stmt = $this->conn->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
    $stmt->bindParam(':nroCuenta', $nroCuenta);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $idCuenta = $row['idCuenta'];
        
        // Redirigir al formulario de retiro con el ID de la cuenta
        header('Location: index.php?controller=transaccion&action=retirar&idCuenta=' . $idCuenta);
        exit;
    } else {
        $this->session->setFlashMessage('error', $lang['account_not_found']);
        header('Location: index.php?controller=transaccion&action=retirar');
        exit;
    }
}

/**
 * Método para procesar el retiro
 */
public function procesarRetiro() {
    global $lang;
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['idCuenta']) || empty($_POST['idCuenta']) || !isset($_POST['monto']) || empty($_POST['monto'])) {
        $this->session->setFlashMessage('error', $lang['missing_required_fields']);
        header('Location: index.php?controller=transaccion&action=retirar');
        exit;
    }
    
    $idCuenta = (int)$_POST['idCuenta'];
    $monto = (float)$_POST['monto'];
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : $lang['withdrawal'];
    
    // Validar monto
    if ($monto <= 0) {
        $this->session->setFlashMessage('error', $lang['invalid_amount']);
        header('Location: index.php?controller=transaccion&action=retirar&idCuenta=' . $idCuenta);
        exit;
    }
    
    // Obtener cuenta
    $cuenta = new Cuenta($this->db);
    $cuenta->idCuenta = $idCuenta;
    
    if (!$cuenta->obtenerUna()) {
        $this->session->setFlashMessage('error', $lang['account_not_found']);
        header('Location: index.php?controller=transaccion&action=retirar');
        exit;
    }
    
    // Verificar si la cuenta está activa
    if ($cuenta->estado != 1) {
        $this->session->setFlashMessage('error', $lang['inactive_account_error']);
        header('Location: index.php?controller=transaccion&action=retirar&idCuenta=' . $idCuenta);
        exit;
    }
    
    // Verificar si hay saldo suficiente
    if ($cuenta->saldo < $monto) {
        $this->session->setFlashMessage('error', $lang['insufficient_funds']);
        header('Location: index.php?controller=transaccion&action=retirar&idCuenta=' . $idCuenta);
        exit;
    }
    
    // Iniciar transacción
    $this->db->beginTransaction();
    
    try {
        // Actualizar saldo
        if (!$cuenta->actualizarSaldo($monto, false)) {
            throw new Exception($lang['update_balance_error']);
        }
        
        // Registrar transacción
        $transaccion = new Transaccion($this->db);
        $transaccion->idCuenta = $idCuenta;
        $transaccion->tipo = 2; // Retiro
        $transaccion->monto = $monto;
        $transaccion->descripcion = $descripcion;
        $transaccion->fecha = date('Y-m-d H:i:s');
        $transaccion->saldoResultante = $cuenta->saldo;
        
        if (!$transaccion->crear()) {
            throw new Exception($lang['transaction_error']);
        }
        
        // Confirmar transacción
        $this->db->commit();
        
        $this->session->setFlashMessage('success', $lang['withdrawal_success']);
        header('Location: index.php?controller=cuenta&action=ver&id=' . $idCuenta);
        exit;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $this->db->rollBack();
        $this->session->setFlashMessage('error', $e->getMessage());
        header('Location: index.php?controller=transaccion&action=retirar&idCuenta=' . $idCuenta);
        exit;
    }
}

/**
 * Método para mostrar el formulario de transferencia
 */
public function transferir() {
    global $lang;
    
    // Variables para almacenar datos de cuenta de origen y destino
    $cuentaOrigen = null;
    $cuentaDestino = null;
    
    // Verificar si se proporcionó un ID de cuenta de origen
    if (isset($_GET['idCuentaOrigen']) && !empty($_GET['idCuentaOrigen'])) {
        $idCuentaOrigen = (int)$_GET['idCuentaOrigen'];
        
        // Obtener datos de la cuenta de origen
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuentaOrigen;
        
        if ($cuenta->obtenerUna()) {
            $cuentaOrigen = [
                'idCuenta' => $cuenta->idCuenta,
                'nroCuenta' => $cuenta->nroCuenta,
                'tipoCuenta' => $cuenta->tipoCuenta,
                'tipoMoneda' => $cuenta->tipoMoneda,
                'saldo' => $cuenta->saldo,
                'estado' => $cuenta->estado,
                'cliente_nombre' => $cuenta->cliente_nombre
            ];
            
            // Obtener datos del cliente
            $cliente = new Cliente($this->db);
            $cliente->idPersona = $cuenta->idPersona;
            $cliente->obtenerUno();
            
            // Añadir datos del cliente a la cuenta
            $cuentaOrigen['cliente_ci'] = $cliente->ci;
            $cuentaOrigen['cliente_telefono'] = $cliente->telefono;
            $cuentaOrigen['cliente_email'] = $cliente->email;
        }
    }
    
    // Verificar si se proporcionó un ID de cuenta de destino
    if (isset($_GET['idCuentaDestino']) && !empty($_GET['idCuentaDestino'])) {
        $idCuentaDestino = (int)$_GET['idCuentaDestino'];
        
        // Obtener datos de la cuenta de destino
        $cuenta = new Cuenta($this->db);
        $cuenta->idCuenta = $idCuentaDestino;
        
        if ($cuenta->obtenerUna()) {
            $cuentaDestino = [
                'idCuenta' => $cuenta->idCuenta,
                'nroCuenta' => $cuenta->nroCuenta,
                'tipoCuenta' => $cuenta->tipoCuenta,
                'tipoMoneda' => $cuenta->tipoMoneda,
                'estado' => $cuenta->estado,
                'cliente_nombre' => $cuenta->cliente_nombre
            ];
            
            // Obtener datos del cliente
            $cliente = new Cliente($this->db);
            $cliente->idPersona = $cuenta->idPersona;
            $cliente->obtenerUno();
            
            // Añadir datos del cliente a la cuenta
            $cuentaDestino['cliente_ci'] = $cliente->ci;
            $cuentaDestino['cliente_telefono'] = $cliente->telefono;
            $cuentaDestino['cliente_email'] = $cliente->email;
        }
    }
    
    // Definir título de la página
    $pageTitle = $lang['transfer_funds'];
    
    // Determinar el controlador actual para marcar el menú
    $controller = 'transaccion';
    
    // Definir vista a incluir
    $contentView = 'views/cuentas/transferir.php';
    
    // Mostrar plantilla principal
    include_once 'views/main.php';
}

/**
 * Método para buscar cuenta de origen
 */
public function buscarCuentaOrigen() {
    global $lang;
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['nroCuentaOrigen']) || empty($_POST['nroCuentaOrigen'])) {
        $this->session->setFlashMessage('error', $lang['account_number_required']);
        header('Location: index.php?controller=transaccion&action=transferir');
        exit;
    }
    
    $nroCuentaOrigen = trim($_POST['nroCuentaOrigen']);
    
    // Buscar la cuenta por número
    $cuentaModel = new Cuenta($this->db);
    $stmt = $this->conn->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
    $stmt->bindParam(':nroCuenta', $nroCuentaOrigen);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $idCuentaOrigen = $row['idCuenta'];
        
        // Redirigir al formulario de transferencia con el ID de la cuenta de origen
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen);
        exit;
    } else {
        $this->session->setFlashMessage('error', $lang['account_not_found']);
        header('Location: index.php?controller=transaccion&action=transferir');
        exit;
    }
}

/**
 * Método para buscar cuenta de destino
 */
public function buscarCuentaDestino() {
    global $lang;
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['idCuentaOrigen']) || empty($_POST['idCuentaOrigen']) || !isset($_POST['nroCuentaDestino']) || empty($_POST['nroCuentaDestino'])) {
        $this->session->setFlashMessage('error', $lang['missing_required_fields']);
        header('Location: index.php?controller=transaccion&action=transferir');
        exit;
    }
    
    $idCuentaOrigen = (int)$_POST['idCuentaOrigen'];
    $nroCuentaDestino = trim($_POST['nroCuentaDestino']);
    
    // Verificar que la cuenta de destino no sea la misma que la de origen
    $cuentaModel = new Cuenta($this->db);
    $stmt = $this->conn->prepare("SELECT nroCuenta FROM Cuenta WHERE idCuenta = :idCuenta");
    $stmt->bindParam(':idCuenta', $idCuentaOrigen);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row['nroCuenta'] == $nroCuentaDestino) {
        $this->session->setFlashMessage('error', $lang['same_account_error']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen);
        exit;
    }
    
    // Buscar la cuenta de destino por número
    $stmt = $this->conn->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
    $stmt->bindParam(':nroCuenta', $nroCuentaDestino);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $idCuentaDestino = $row['idCuenta'];
        
        // Redirigir al formulario de transferencia con ambos IDs de cuenta
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen . '&idCuentaDestino=' . $idCuentaDestino);
        exit;
    } else {
        $this->session->setFlashMessage('error', $lang['destination_account_not_found']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen);
        exit;
    }
}

/**
 * Método para procesar la transferencia
 */
public function procesarTransferencia() {
    global $lang;
    
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['idCuentaOrigen']) || empty($_POST['idCuentaOrigen']) || !isset($_POST['idCuentaDestino']) || empty($_POST['idCuentaDestino']) || !isset($_POST['monto']) || empty($_POST['monto'])) {
        $this->session->setFlashMessage('error', $lang['missing_required_fields']);
        header('Location: index.php?controller=transaccion&action=transferir');
        exit;
    }
    
    $idCuentaOrigen = (int)$_POST['idCuentaOrigen'];
    $idCuentaDestino = (int)$_POST['idCuentaDestino'];
    $monto = (float)$_POST['monto'];
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : $lang['transfer'];
    
    // Validar monto
    if ($monto <= 0) {
        $this->session->setFlashMessage('error', $lang['invalid_amount']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen . '&idCuentaDestino=' . $idCuentaDestino);
        exit;
    }
    
    // Obtener cuenta de origen
    $cuentaOrigen = new Cuenta($this->db);
    $cuentaOrigen->idCuenta = $idCuentaOrigen;
    
    if (!$cuentaOrigen->obtenerUna()) {
        $this->session->setFlashMessage('error', $lang['source_account_not_found']);
        header('Location: index.php?controller=transaccion&action=transferir');
        exit;
    }
    
    // Obtener cuenta de destino
    $cuentaDestino = new Cuenta($this->db);
    $cuentaDestino->idCuenta = $idCuentaDestino;
    
    if (!$cuentaDestino->obtenerUna()) {
        $this->session->setFlashMessage('error', $lang['destination_account_not_found']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen);
        exit;
    }
    
    // Verificar si las cuentas están activas
    if ($cuentaOrigen->estado != 1) {
        $this->session->setFlashMessage('error', $lang['source_account_inactive_error']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen . '&idCuentaDestino=' . $idCuentaDestino);
        exit;
    }
    
    if ($cuentaDestino->estado != 1) {
        $this->session->setFlashMessage('error', $lang['destination_account_inactive_error']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen . '&idCuentaDestino=' . $idCuentaDestino);
        exit;
    }
    
    // Verificar si hay saldo suficiente
    if ($cuentaOrigen->saldo < $monto) {
        $this->session->setFlashMessage('error', $lang['insufficient_funds']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen . '&idCuentaDestino=' . $idCuentaDestino);
        exit;
    }
    
    // Verificar si las monedas son compatibles
    if ($cuentaOrigen->tipoMoneda != $cuentaDestino->tipoMoneda) {
        $this->session->setFlashMessage('error', $lang['currency_mismatch_error']);
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen . '&idCuentaDestino=' . $idCuentaDestino);
        exit;
    }
    
    // Iniciar transacción
    $this->db->beginTransaction();
    
    try {
        // Debitar de la cuenta de origen
        if (!$cuentaOrigen->actualizarSaldo($monto, false)) {
            throw new Exception($lang['update_balance_error']);
        }
        
        // Acreditar a la cuenta de destino
        if (!$cuentaDestino->actualizarSaldo($monto, true)) {
            throw new Exception($lang['update_balance_error']);
        }
        
        // Registrar transacción de retiro en cuenta origen
        $transaccionOrigen = new Transaccion($this->db);
        $transaccionOrigen->idCuenta = $idCuentaOrigen;
        $transaccionOrigen->tipo = 4; // Transferencia enviada
        $transaccionOrigen->monto = $monto;
        $transaccionOrigen->descripcion = $descripcion;
        $transaccionOrigen->fecha = date('Y-m-d H:i:s');
        $transaccionOrigen->saldoResultante = $cuentaOrigen->saldo;
        $transaccionOrigen->cuentaDestino = $cuentaDestino->nroCuenta;
        
        if (!$transaccionOrigen->crear()) {
            throw new Exception($lang['transaction_error']);
        }
        
        // Registrar transacción de depósito en cuenta destino
        $transaccionDestino = new Transaccion($this->db);
        $transaccionDestino->idCuenta = $idCuentaDestino;
        $transaccionDestino->tipo = 3; // Transferencia recibida
        $transaccionDestino->monto = $monto;
        $transaccionDestino->descripcion = $descripcion;
        $transaccionDestino->fecha = date('Y-m-d H:i:s');
        $transaccionDestino->saldoResultante = $cuentaDestino->saldo;
        $transaccionDestino->cuentaOrigen = $cuentaOrigen->nroCuenta;
        
        if (!$transaccionDestino->crear()) {
            throw new Exception($lang['transaction_error']);
        }
        
        // Confirmar transacción
        $this->db->commit();
        
        $this->session->setFlashMessage('success', $lang['transfer_success']);
        header('Location: index.php?controller=cuenta&action=ver&id=' . $idCuentaOrigen);
        exit;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $this->db->rollBack();
        $this->session->setFlashMessage('error', $e->getMessage());
        header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen . '&idCuentaDestino=' . $idCuentaDestino);
        exit;
    }
}
}
?>