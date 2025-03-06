<?php

class DashboardController {
    // Propiedades
    private $db;
    
    //Constructor
    public function __construct() {
        // Inicializar conexión a la base de datos
        $database = new Database();
        $this->db = $database->connect();
    }
    
    //Método para mostrar el dashboard principal
    public function index() {
        global $lang;
        
        // Obtener estadísticas
        $totalClientes = $this->contarClientes();
        $totalCuentas = $this->contarCuentas();
        $transaccionesHoy = $this->contarTransaccionesHoy();
        $transaccionesRecientes = $this->obtenerTransaccionesRecientes();
        
        // Definir título de la página
        $pageTitle = $lang['dashboard'];
        
        // Determinar el controlador actual para marcar el menú
        $controller = 'dashboard';
        
        // Incluir la vista
        include_once 'views/dashboard/index.php';
    }
    
    //Método para contar el total de clientes
    private function contarClientes() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM Persona WHERE tipo = 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error al contar clientes: " . $e->getMessage());
            return 0;
        }
    }
    
    //Método para contar el total de cuentas
    private function contarCuentas() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM Cuenta");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error al contar cuentas: " . $e->getMessage());
            return 0;
        }
    }
    
    //Método para contar transacciones de hoy
    private function contarTransaccionesHoy() {
        try {
            $hoy = date('Y-m-d');
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM Transaccion WHERE fecha = :fecha");
            $stmt->bindParam(':fecha', $hoy);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error al contar transacciones de hoy: " . $e->getMessage());
            return 0;
        }
    }
    
    //Método para obtener las transacciones recientes
    private function obtenerTransaccionesRecientes($limit = 10) {
        try {
            $query = "SELECT t.*, c.nroCuenta, 
                     CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                     FROM Transaccion t
                     INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                     INNER JOIN Persona p ON c.idPersona = p.idPersona
                     ORDER BY t.fecha DESC, t.hora DESC
                     LIMIT :limit";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener transacciones recientes: " . $e->getMessage());
            return [];
        }
    }
}
?>