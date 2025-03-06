<?php
/**
 * Modelo ATM (Cajero Automático)
 */
class ATM {
    // Conexión a la base de datos
    private $conn;
    
    // Propiedades del ATM
    public $idATM;
    public $ubicacion;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todos los ATMs
     * @return PDOStatement
     */
    public function obtenerTodos() {
        $query = "SELECT * FROM ATM ORDER BY idATM";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener un ATM
     * @return boolean
     */
    public function obtenerUno() {
        $query = "SELECT * FROM ATM WHERE idATM = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->idATM);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->ubicacion = $row['ubicacion'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Crear un ATM
     * @return boolean
     */
    public function crear() {
        $query = "INSERT INTO ATM (ubicacion) VALUES (:ubicacion)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->ubicacion = htmlspecialchars(strip_tags($this->ubicacion));
        
        // Vincular datos
        $stmt->bindParam(':ubicacion', $this->ubicacion);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->idATM = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar ATM
     * @return boolean
     */
    public function actualizar() {
        $query = "UPDATE ATM SET ubicacion = :ubicacion WHERE idATM = :idATM";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->ubicacion = htmlspecialchars(strip_tags($this->ubicacion));
        
        // Vincular datos
        $stmt->bindParam(':ubicacion', $this->ubicacion);
        $stmt->bindParam(':idATM', $this->idATM);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Eliminar ATM
     * @return boolean
     */
    public function eliminar() {
        // Primero verificar si hay transacciones asociadas a este ATM
        $query = "SELECT COUNT(*) as total FROM TransaccionATM WHERE idATM = :idATM";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idATM', $this->idATM);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            // Hay transacciones asociadas, no se puede eliminar
            return false;
        }
        
        // Se puede eliminar
        $query = "DELETE FROM ATM WHERE idATM = :idATM";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idATM', $this->idATM);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Realizar transacción en ATM
     * @param int $idCuenta ID de la cuenta
     * @param float $monto Monto de la transacción
     * @param int $tipoTransaccion 1: Retiro, 2: Depósito
     * @param string $descripcion Descripción de la transacción
     * @return boolean
     */
    public function realizarTransaccion($idCuenta, $monto, $tipoTransaccion, $descripcion = '') {
        // Iniciar transacción en la base de datos
        $this->conn->beginTransaction();
        
        try {
            // Crear la transacción en la tabla Transaccion
            $transaccion = new Transaccion($this->conn);
            $transaccion->idCuenta = $idCuenta;
            $transaccion->monto = $monto;
            $transaccion->tipoTransaccion = $tipoTransaccion;
            $transaccion->descripcion = $descripcion;
            $transaccion->fecha = date('Y-m-d');
            $transaccion->hora = date('H:i:s');
            
            if (!$transaccion->crear()) {
                // Si hay un error, hacer rollback
                $this->conn->rollBack();
                return false;
            }
            
            // Registrar la transacción en la tabla TransaccionATM
            $query = "INSERT INTO TransaccionATM (idTransaccion, idATM) VALUES (:idTransaccion, :idATM)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idTransaccion', $transaccion->idTransaccion);
            $stmt->bindParam(':idATM', $this->idATM);
            
            if (!$stmt->execute()) {
                // Si hay un error, hacer rollback
                $this->conn->rollBack();
                return false;
            }
            
            // Si todo está bien, confirmar la transacción
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Si ocurre una excepción, hacer rollback
            $this->conn->rollBack();
            return false;
        }
    }
}
?>