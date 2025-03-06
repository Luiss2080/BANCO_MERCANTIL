<?php
/**
 * Modelo Bienvenida
 */
class Bienvenida {
    // Conexión a la base de datos
    private $conn;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener estadísticas para la página de bienvenida
     * @return array
     */
    public function obtenerEstadisticas() {
        try {
            $stats = [
                'total_clientes' => 0,
                'total_cuentas' => 0,
                'total_transacciones' => 0,
                'saldo_total_bolivianos' => 0,
                'saldo_total_dolares' => 0
            ];
            
            // Contar clientes
            $query = "SELECT COUNT(*) as total FROM Persona WHERE idPersona > 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_clientes'] = $row['total'];
            
            // Contar cuentas
            $query = "SELECT COUNT(*) as total FROM Cuenta";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_cuentas'] = $row['total'];
            
            // Contar transacciones
            $query = "SELECT COUNT(*) as total FROM Transaccion";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_transacciones'] = $row['total'];
            
            // Sumar saldos por moneda
            $query = "SELECT tipoMoneda, SUM(saldo) as total FROM Cuenta GROUP BY tipoMoneda";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['tipoMoneda'] == 1) { // Bolivianos
                    $stats['saldo_total_bolivianos'] = $row['total'];
                } else if ($row['tipoMoneda'] == 2) { // Dólares
                    $stats['saldo_total_dolares'] = $row['total'];
                }
            }
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return $stats;
        }
    }
    
    /**
     * Obtener transacciones recientes para la página de bienvenida
     * @param int $limit Número de transacciones a obtener
     * @return array
     */
    public function obtenerTransaccionesRecientes($limit = 5) {
        try {
            $query = "SELECT t.*, c.nroCuenta, 
                    CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                    FROM Transaccion t
                    INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                    INNER JOIN Persona p ON c.idPersona = p.idPersona
                    ORDER BY t.fecha DESC, t.hora DESC
                    LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener transacciones recientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener información del sistema para mostrar en la página de bienvenida
     * @return array
     */
    public function obtenerInfoSistema() {
        return [
            'nombre_sistema' => 'Banco Mercantil',
            'version' => '1.0.0',
            'fecha_actualizacion' => '2025-03-03',
            'developer' => 'Banco Camba Team'
        ];
    }
}
?>