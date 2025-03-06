<?php
/**
 * Modelo Transacción
 */
class Transaccion {
    // Conexión a la base de datos
    private $conn;
    
    // Propiedades de la transacción
    public $idTransaccion;
    public $tipoTransaccion; // 1: Retiro, 2: Depósito
    public $fecha;
    public $hora;
    public $descripcion;
    public $monto;
    public $idCuenta;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todas las transacciones
     * @return PDOStatement
     */
    public function obtenerTodas() {
        $query = "SELECT t.*, c.nroCuenta, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                  FROM Transaccion t
                  INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                  INNER JOIN Persona p ON c.idPersona = p.idPersona
                  ORDER BY t.fecha DESC, t.hora DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener una transacción
     * @return boolean
     */
    public function obtenerUna() {
        $query = "SELECT t.*, c.nroCuenta,
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                  FROM Transaccion t
                  INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                  INNER JOIN Persona p ON c.idPersona = p.idPersona
                  WHERE t.idTransaccion = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->idTransaccion);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->tipoTransaccion = $row['tipoTransaccion'];
            $this->fecha = $row['fecha'];
            $this->hora = $row['hora'];
            $this->descripcion = $row['descripcion'];
            $this->monto = $row['monto'];
            $this->idCuenta = $row['idCuenta'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtener transacciones por cuenta
     * @param int $idCuenta
     * @return PDOStatement
     */
    public function obtenerPorCuenta($idCuenta) {
        $query = "SELECT * FROM Transaccion 
                  WHERE idCuenta = :idCuenta 
                  ORDER BY fecha DESC, hora DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idCuenta', $idCuenta);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener transacciones por cuenta y rango de fechas
     * @param int $idCuenta
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return PDOStatement
     */
    public function obtenerPorRangoFechas($idCuenta, $fechaInicio, $fechaFin) {
        $query = "SELECT * FROM Transaccion 
                  WHERE idCuenta = :idCuenta 
                  AND fecha BETWEEN :fechaInicio AND :fechaFin
                  ORDER BY fecha DESC, hora DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idCuenta', $idCuenta);
        $stmt->bindParam(':fechaInicio', $fechaInicio);
        $stmt->bindParam(':fechaFin', $fechaFin);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Crear una transacción
     * @return boolean
     */
    public function crear() {
        // Iniciar transacción
        $this->conn->beginTransaction();
        
        try {
            // Si no se proporciona fecha y hora, usar la fecha y hora actual
            if (empty($this->fecha)) {
                $this->fecha = date('Y-m-d');
            }
            
            if (empty($this->hora)) {
                $this->hora = date('H:i:s');
            }
            
            // Crear la transacción
            $query = "INSERT INTO Transaccion
                     (tipoTransaccion, fecha, hora, descripcion, monto, idCuenta)
                     VALUES
                     (:tipoTransaccion, :fecha, :hora, :descripcion, :monto, :idCuenta)";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
            
            // Vincular datos
            $stmt->bindParam(':tipoTransaccion', $this->tipoTransaccion);
            $stmt->bindParam(':fecha', $this->fecha);
            $stmt->bindParam(':hora', $this->hora);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':monto', $this->monto);
            $stmt->bindParam(':idCuenta', $this->idCuenta);
            
            // Ejecutar consulta
            $stmt->execute();
            
            // Obtener el ID de la transacción creada
            $this->idTransaccion = $this->conn->lastInsertId();
            
            // Actualizar el saldo de la cuenta
            $cuenta = new Cuenta($this->conn);
            $cuenta->idCuenta = $this->idCuenta;
            $cuenta->obtenerUna();
            
            $esDeposito = ($this->tipoTransaccion == 2); // 2 es depósito
            
            if (!$cuenta->actualizarSaldo($this->monto, $esDeposito)) {
                // Si hay un error al actualizar el saldo, se hace rollback
                $this->conn->rollBack();
                return false;
            }
            
            // Si todo está bien, confirmar la transacción
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Si ocurre un error, hacer rollback
            $this->conn->rollBack();
            return false;
        }
    }
    
    /**
     * Contar transacciones del día
     * @return int
     */
    public function contarTransaccionesHoy() {
        $hoy = date('Y-m-d');
        $query = "SELECT COUNT(*) as total FROM Transaccion WHERE fecha = :fecha";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $hoy);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
    
    /**
     * Obtener transacciones recientes
     * @param int $limit Número de transacciones a obtener
     * @return PDOStatement
     */
    public function obtenerRecientes($limit = 10) {
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
        
        return $stmt;
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