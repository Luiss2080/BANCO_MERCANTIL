<?php

class TransaccionController {
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
    
    //Método para mostrar el formulario de depósito
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
    
    //Método para buscar cuenta para depósito
    public function buscarCuentaDeposito() {
        global $lang;
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['nroCuenta']) || empty($_POST['nroCuenta'])) {
            $this->session->setFlashMessage('error', $lang['account_number_required']);
            header('Location: index.php?controller=transaccion&action=depositar');
            exit;
        }
        
        $nroCuenta = trim($_POST['nroCuenta']);
        
        // Buscar la cuenta por número
        $stmt = $this->db->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
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
    
    //Método para procesar el depósito
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
    
    //Método para mostrar el formulario de retiro
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
    
    //Método para buscar cuenta para retiro
    public function buscarCuentaRetiro() {
        global $lang;
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['nroCuenta']) || empty($_POST['nroCuenta'])) {
            $this->session->setFlashMessage('error', $lang['account_number_required']);
            header('Location: index.php?controller=transaccion&action=retirar');
            exit;
        }
        
        $nroCuenta = trim($_POST['nroCuenta']);
        
        // Buscar la cuenta por número
        $stmt = $this->db->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
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
    
    //Método para procesar el retiro
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
    
    //Método para mostrar el formulario de transferencia
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
    
    //Método para buscar cuenta de origen
    public function buscarCuentaOrigen() {
        global $lang;
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['nroCuentaOrigen']) || empty($_POST['nroCuentaOrigen'])) {
            $this->session->setFlashMessage('error', $lang['account_number_required']);
            header('Location: index.php?controller=transaccion&action=transferir');
            exit;
        }
        
        $nroCuentaOrigen = trim($_POST['nroCuentaOrigen']);
        
        // Buscar la cuenta por número
        $stmt = $this->db->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
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
    
    //Método para buscar cuenta de destino
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
        $stmt = $this->db->prepare("SELECT nroCuenta FROM Cuenta WHERE idCuenta = :idCuenta");
        $stmt->bindParam(':idCuenta', $idCuentaOrigen);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['nroCuenta'] == $nroCuentaDestino) {
            $this->session->setFlashMessage('error', $lang['same_account_error']);
            header('Location: index.php?controller=transaccion&action=transferir&idCuentaOrigen=' . $idCuentaOrigen);
            exit;
        }
        
        // Buscar la cuenta de destino por número
        $stmt = $this->db->prepare("SELECT idCuenta FROM Cuenta WHERE nroCuenta = :nroCuenta");
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
    
    //Método para procesar la transferencia
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
    /**
     * Obtener transacciones por rango de fechas global (todos los usuarios)
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return PDOStatement
     */
    public function obtenerPorRangoFechasGlobal($fechaInicio, $fechaFin) {
        $query = "SELECT t.*, c.nroCuenta, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                  FROM Transaccion t
                  INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                  INNER JOIN Persona p ON c.idPersona = p.idPersona
                  WHERE t.fecha BETWEEN :fechaInicio AND :fechaFin
                  ORDER BY t.fecha DESC, t.hora DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fechaInicio', $fechaInicio);
        $stmt->bindParam(':fechaFin', $fechaFin);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener transacciones por ATM
     * @param int $idATM
     * @return PDOStatement
     */
    public function obtenerPorATM($idATM) {
        $query = "SELECT t.*, c.nroCuenta, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                  FROM TransaccionATM ta
                  INNER JOIN Transaccion t ON ta.idTransaccion = t.idTransaccion
                  INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                  INNER JOIN Persona p ON c.idPersona = p.idPersona
                  WHERE ta.idATM = :idATM
                  ORDER BY t.fecha DESC, t.hora DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idATM', $idATM);
        $stmt->execute();
        
        return $stmt;
    }
}
?>