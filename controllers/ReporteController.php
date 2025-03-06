<?php

class ReporteController {
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
    
    //Mostrar página principal de reportes
    public function index() {
        global $lang;
        
        // Definir título de la página
        $pageTitle = $lang['reports'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'reporte';
        
        // Definir vista a incluir
        $contentView = 'views/reportes/index.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Reporte de transacciones por fechas
    public function transaccionesPorFecha() {
        global $lang;
        
        // Definir título de la página
        $pageTitle = $lang['reports'] . ' - ' . $lang['transactions'];
        
        // Obtener fechas por defecto (último mes)
        $fechaFin = date('Y-m-d');
        $fechaInicio = date('Y-m-d', strtotime('-1 month'));
        
        // Inicializar variables
        $transacciones = [];
        $totalDepositos = 0;
        $totalRetiros = 0;
        $totalTransacciones = 0;
        
        // Si se envió el formulario, usar las fechas especificadas
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fechaInicio = isset($_POST['fechaInicio']) ? trim($_POST['fechaInicio']) : $fechaInicio;
            $fechaFin = isset($_POST['fechaFin']) ? trim($_POST['fechaFin']) : $fechaFin;
            
            // Obtener transacciones en el rango de fechas
            $transaccion = new Transaccion($this->db);
            $result = $transaccion->obtenerPorRangoFechasGlobal($fechaInicio, $fechaFin);
            $transacciones = $result->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular totales
            foreach ($transacciones as $t) {
                if ($t['tipoTransaccion'] == 1) { // Retiro
                    $totalRetiros += $t['monto'];
                } else { // Depósito
                    $totalDepositos += $t['monto'];
                }
            }
            
            $totalTransacciones = count($transacciones);
        }
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'reporte';
        
        // Definir vista a incluir
        $contentView = 'views/reportes/transacciones_fecha.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Reporte de transacciones por cliente
    public function transaccionesPorCliente() {
        global $lang;
        
        // Definir título de la página
        $pageTitle = $lang['reports'] . ' - ' . $lang['transactions_by_client'];
        
        // Obtener lista de clientes
        $cliente = new Cliente($this->db);
        $resultClientes = $cliente->obtenerTodos();
        $clientes = $resultClientes->fetchAll(PDO::FETCH_ASSOC);
        
        // Inicializar variables
        $transacciones = [];
        $clienteSeleccionado = null;
        $totalDepositos = 0;
        $totalRetiros = 0;
        
        // Si se envió el formulario, procesar
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idCliente = isset($_POST['idCliente']) ? (int)$_POST['idCliente'] : 0;
            
            if ($idCliente > 0) {
                // Obtener datos del cliente seleccionado
                $clienteObj = new Cliente($this->db);
                $clienteObj->idPersona = $idCliente;
                $clienteObj->obtenerUno();
                $clienteSeleccionado = $clienteObj;
                
                // Obtener cuentas del cliente
                $cuenta = new Cuenta($this->db);
                $resultCuentas = $cuenta->obtenerPorCliente($idCliente);
                $cuentas = $resultCuentas->fetchAll(PDO::FETCH_ASSOC);
                
                // Obtener transacciones de todas las cuentas del cliente
                $transacciones = [];
                foreach ($cuentas as $c) {
                    $transaccion = new Transaccion($this->db);
                    $resultTransacciones = $transaccion->obtenerPorCuenta($c['idCuenta']);
                    $trans = $resultTransacciones->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Agregar información de la cuenta a cada transacción
                    foreach ($trans as &$t) {
                        $t['nroCuenta'] = $c['nroCuenta'];
                        if ($t['tipoTransaccion'] == 1) { // Retiro
                            $totalRetiros += $t['monto'];
                        } else { // Depósito
                            $totalDepositos += $t['monto'];
                        }
                    }
                    
                    // Fusionar con el array principal
                    $transacciones = array_merge($transacciones, $trans);
                }
                
                // Ordenar transacciones por fecha y hora (más recientes primero)
                usort($transacciones, function($a, $b) {
                    $dateA = $a['fecha'] . ' ' . $a['hora'];
                    $dateB = $b['fecha'] . ' ' . $b['hora'];
                    return strcmp($dateB, $dateA);
                });
            }
        }
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'reporte';
        
        // Definir vista a incluir
        $contentView = 'views/reportes/transacciones_cliente.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Reporte de estadísticas de ATM
    public function estadisticasATM() {
        global $lang;
        
        // Definir título de la página
        $pageTitle = $lang['reports'] . ' - ATM';
        
        // Obtener lista de ATMs
        $atm = new ATM($this->db);
        $resultATMs = $atm->obtenerTodos();
        $atms = $resultATMs->fetchAll(PDO::FETCH_ASSOC);
        
        // Inicializar variables
        $estadisticas = [];
        $atmSeleccionado = null;
        
        // Si se envió el formulario, procesar
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idATM = isset($_POST['idATM']) ? (int)$_POST['idATM'] : 0;
            
            if ($idATM > 0) {
                // Obtener datos del ATM seleccionado
                $atmObj = new ATM($this->db);
                $atmObj->idATM = $idATM;
                $atmObj->obtenerUno();
                $atmSeleccionado = $atmObj;
                
                // Preparar la consulta para estadísticas por mes
                $query = "SELECT 
                            YEAR(t.fecha) as anio,
                            MONTH(t.fecha) as mes,
                            SUM(CASE WHEN t.tipoTransaccion = 1 THEN 1 ELSE 0 END) as retiros,
                            SUM(CASE WHEN t.tipoTransaccion = 2 THEN 1 ELSE 0 END) as depositos,
                            SUM(CASE WHEN t.tipoTransaccion = 1 THEN t.monto ELSE 0 END) as monto_retiros,
                            SUM(CASE WHEN t.tipoTransaccion = 2 THEN t.monto ELSE 0 END) as monto_depositos
                          FROM TransaccionATM ta
                          INNER JOIN Transaccion t ON ta.idTransaccion = t.idTransaccion
                          WHERE ta.idATM = :idATM
                          GROUP BY YEAR(t.fecha), MONTH(t.fecha)
                          ORDER BY YEAR(t.fecha) DESC, MONTH(t.fecha) DESC";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':idATM', $idATM);
                $stmt->execute();
                $estadisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Formatear datos y añadir nombre del mes
                $nombresMeses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                
                foreach ($estadisticas as &$est) {
                    $est['nombre_mes'] = $nombresMeses[$est['mes']];
                    $est['etiqueta'] = $nombresMeses[$est['mes']] . ' ' . $est['anio'];
                    $est['total_transacciones'] = $est['retiros'] + $est['depositos'];
                }
            }
        }
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'reporte';
        
        // Definir vista a incluir
        $contentView = 'views/reportes/estadisticas_atm.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
    
    //Imprimir reporte de transacciones por fecha
    public function imprimirTransaccionesFecha() {
        global $lang;
        
        // Verificar si se especificaron los parámetros necesarios
        if (!isset($_GET['inicio']) || empty($_GET['inicio']) || !isset($_GET['fin']) || empty($_GET['fin'])) {
            $this->session->setFlashMessage('error', 'Parámetros insuficientes');
            header('Location: index.php?controller=reporte&action=transaccionesPorFecha');
            exit;
        }
        
        $fechaInicio = $_GET['inicio'];
        $fechaFin = $_GET['fin'];
        
        // Obtener transacciones en el rango de fechas
        $transaccion = new Transaccion($this->db);
        $result = $transaccion->obtenerPorRangoFechasGlobal($fechaInicio, $fechaFin);
        $transacciones = $result->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular totales
        $totalDepositos = 0;
        $totalRetiros = 0;
        
        foreach ($transacciones as $t) {
            if ($t['tipoTransaccion'] == 1) { // Retiro
                $totalRetiros += $t['monto'];
            } else { // Depósito
                $totalDepositos += $t['monto'];
            }
        }
        
        $totalTransacciones = count($transacciones);
        
        // Mostrar vista de impresión (sin el layout)
        include_once 'views/reportes/imprimir_transacciones_fecha.php';
    }
    
    //Reporte de saldos por oficina
    public function saldosPorOficina() {
        global $lang;
        
        // Definir título de la página
        $pageTitle = $lang['reports'] . ' - ' . $lang['balances_by_branch'];
        
        // Preparar la consulta para saldos por oficina
        $query = "SELECT 
                    o.idOficina,
                    o.nombre as oficina_nombre,
                    COUNT(DISTINCT p.idPersona) as total_clientes,
                    COUNT(DISTINCT c.idCuenta) as total_cuentas,
                    SUM(CASE WHEN c.tipoMoneda = 1 THEN c.saldo ELSE 0 END) as saldo_bolivianos,
                    SUM(CASE WHEN c.tipoMoneda = 2 THEN c.saldo ELSE 0 END) as saldo_dolares
                  FROM Oficina o
                  LEFT JOIN Persona p ON o.idOficina = p.idOficina
                  LEFT JOIN Cuenta c ON p.idPersona = c.idPersona
                  GROUP BY o.idOficina, o.nombre
                  ORDER BY o.central DESC, o.nombre ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $oficinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular totales generales
        $totalClientes = 0;
        $totalCuentas = 0;
        $totalBolivianos = 0;
        $totalDolares = 0;
        
        foreach ($oficinas as $ofi) {
            $totalClientes += $ofi['total_clientes'];
            $totalCuentas += $ofi['total_cuentas'];
            $totalBolivianos += $ofi['saldo_bolivianos'];
            $totalDolares += $ofi['saldo_dolares'];
        }
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'reporte';
        
        // Definir vista a incluir
        $contentView = 'views/reportes/saldos_oficina.php';
        
        // Mostrar plantilla principal
        include_once 'views/main.php';
    }
}
?>