<?php
/**
 * API para cajeros automáticos (ATM)
 * Esta API permite realizar operaciones desde cajeros automáticos
 */

// Permitir solicitudes desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir archivos necesarios
include_once '../config/database.php';
include_once '../models/ATM.php';
include_once '../models/Cuenta.php';
include_once '../models/Tarjeta.php';
include_once '../models/Transaccion.php';

// Inicializar conexión a la base de datos
$database = new Database();
$db = $database->connect();

// Obtener método de solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Procesar según el método
switch ($method) {
    case 'GET':
        // Obtener información del ATM
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $idATM = (int)$_GET['id'];
            $atm = new ATM($db);
            $atm->idATM = $idATM;
            
            if ($atm->obtenerUno()) {
                $response = [
                    'status' => 'success',
                    'data' => [
                        'idATM' => $atm->idATM,
                        'ubicacion' => $atm->ubicacion
                    ]
                ];
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ATM no encontrado'
                ]);
            }
        } else {
            // Listar todos los ATMs
            $atm = new ATM($db);
            $result = $atm->obtenerTodos();
            $atms = [];
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $atms[] = [
                    'idATM' => $row['idATM'],
                    'ubicacion' => $row['ubicacion']
                ];
            }
            
            $response = [
                'status' => 'success',
                'data' => $atms
            ];
            echo json_encode($response);
        }
        break;
        
    case 'POST':
        // Obtener datos enviados
        $data = json_decode(file_get_contents("php://input"));
        
        // Verificar la acción a realizar
        if (!isset($data->action)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Acción no especificada'
            ]);
            break;
        }
        
        // Procesar según la acción
        switch ($data->action) {
            case 'authenticate':
                // Autenticar tarjeta
                if (!isset($data->nroTarjeta) || !isset($data->pin) || !isset($data->idATM)) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Datos insuficientes'
                    ]);
                    break;
                }
                
                // Verificar si el ATM existe
                $atm = new ATM($db);
                $atm->idATM = $data->idATM;
                if (!$atm->obtenerUno()) {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'ATM no encontrado'
                    ]);
                    break;
                }
                
                // Buscar la tarjeta
                $query = "SELECT * FROM Tarjeta WHERE nroTarjeta = :nroTarjeta AND estado = 1";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':nroTarjeta', $data->nroTarjeta);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $idTarjeta = $row['idTarjeta'];
                    $idCuenta = $row['idCuenta'];
                    
                    // Verificar PIN
                    $tarjeta = new Tarjeta($db);
                    $tarjeta->idTarjeta = $idTarjeta;
                    
                    if ($tarjeta->verificarPIN($data->pin)) {
                        // Obtener datos de la cuenta
                        $cuenta = new Cuenta($db);
                        $cuenta->idCuenta = $idCuenta;
                        $cuenta->obtenerUna();
                        
                        // Obtener nombre del cliente
                        $query = "SELECT CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) as nombre_completo 
                                  FROM Persona WHERE idPersona = :idPersona";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':idPersona', $cuenta->idPersona);
                        $stmt->execute();
                        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $response = [
                            'status' => 'success',
                            'data' => [
                                'idTarjeta' => $idTarjeta,
                                'idCuenta' => $idCuenta,
                                'nroCuenta' => $cuenta->nroCuenta,
                                'saldo' => $cuenta->saldo,
                                'cliente' => $cliente['nombre_completo']
                            ]
                        ];
                        echo json_encode($response);
                    } else {
                        http_response_code(401);
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'PIN incorrecto'
                        ]);
                    }
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Tarjeta no encontrada o inactiva'
                    ]);
                }
                break;
                
            case 'withdraw':
                // Realizar retiro
                if (!isset($data->idCuenta) || !isset($data->monto) || !isset($data->idATM)) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Datos insuficientes'
                    ]);
                    break;
                }
                
                // Verificar si el ATM existe
                $atm = new ATM($db);
                $atm->idATM = $data->idATM;
                if (!$atm->obtenerUno()) {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'ATM no encontrado'
                    ]);
                    break;
                }
                
                // Verificar si la cuenta existe
                $cuenta = new Cuenta($db);
                $cuenta->idCuenta = $data->idCuenta;
                if (!$cuenta->obtenerUna()) {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Cuenta no encontrada'
                    ]);
                    break;
                }
                
                // Verificar si hay saldo suficiente
                if ($cuenta->saldo < $data->monto) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Saldo insuficiente'
                    ]);
                    break;
                }
                
                // Realizar transacción
                $descripcion = isset($data->descripcion) ? $data->descripcion : 'Retiro en ATM';
                if ($atm->realizarTransaccion($data->idCuenta, $data->monto, 1, $descripcion)) { // 1 = Retiro
                    // Obtener nuevo saldo
                    $cuenta->obtenerUna();
                    
                    $response = [
                        'status' => 'success',
                        'data' => [
                            'mensaje' => 'Retiro realizado exitosamente',
                            'nuevo_saldo' => $cuenta->saldo
                        ]
                    ];
                    echo json_encode($response);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al realizar el retiro'
                    ]);
                }
                break;
                
            case 'deposit':
                // Realizar depósito
                if (!isset($data->idCuenta) || !isset($data->monto) || !isset($data->idATM)) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Datos insuficientes'
                    ]);
                    break;
                }
                
                // Verificar si el ATM existe
                $atm = new ATM($db);
                $atm->idATM = $data->idATM;
                if (!$atm->obtenerUno()) {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'ATM no encontrado'
                    ]);
                    break;
                }
                
                // Verificar si la cuenta existe
                $cuenta = new Cuenta($db);
                $cuenta->idCuenta = $data->idCuenta;
                if (!$cuenta->obtenerUna()) {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Cuenta no encontrada'
                    ]);
                    break;
                }
                
                // Realizar transacción
                $descripcion = isset($data->descripcion) ? $data->descripcion : 'Depósito en ATM';
                if ($atm->realizarTransaccion($data->idCuenta, $data->monto, 2, $descripcion)) { // 2 = Depósito
                    // Obtener nuevo saldo
                    $cuenta->obtenerUna();
                    
                    $response = [
                        'status' => 'success',
                        'data' => [
                            'mensaje' => 'Depósito realizado exitosamente',
                            'nuevo_saldo' => $cuenta->saldo
                        ]
                    ];
                    echo json_encode($response);
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al realizar el depósito'
                    ]);
                }
                break;
                
            case 'balance':
                // Consultar saldo
                if (!isset($data->idCuenta)) {
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Datos insuficientes'
                    ]);
                    break;
                }
                
                // Verificar si la cuenta existe
                $cuenta = new Cuenta($db);
                $cuenta->idCuenta = $data->idCuenta;
                if (!$cuenta->obtenerUna()) {
                    http_response_code(404);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Cuenta no encontrada'
                    ]);
                    break;
                }
                
                // Obtener datos del cliente
                $query = "SELECT CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) as nombre_completo 
                          FROM Persona WHERE idPersona = :idPersona";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':idPersona', $cuenta->idPersona);
                $stmt->execute();
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $response = [
                    'status' => 'success',
                    'data' => [
                        'idCuenta' => $cuenta->idCuenta,
                        'nroCuenta' => $cuenta->nroCuenta,
                        'tipoCuenta' => $cuenta->tipoCuenta == 1 ? 'Cuenta de Ahorro' : 'Cuenta Corriente',
                        'tipoMoneda' => $cuenta->tipoMoneda == 1 ? 'Bolivianos' : 'Dólares',
                        'saldo' => $cuenta->saldo,
                        'cliente' => $cliente['nombre_completo']
                    ]
                ];
                echo json_encode($response);
                break;
                
            default:
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Acción no válida'
                ]);
                break;
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Método no permitido'
        ]);
        break;
}
?>