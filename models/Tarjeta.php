<?php
/**
 * Modelo Tarjeta
 */
class Tarjeta {
    // Conexión a la base de datos
    private $conn;
    
    // Propiedades de la tarjeta
    public $idTarjeta;
    public $estado; // 1: Activa, 2: Inactiva
    public $nroTarjeta;
    public $cvv;
    public $fechaExpiracion;
    public $pin;
    public $idCuenta;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todas las tarjetas
     * @return PDOStatement
     */
    public function obtenerTodas() {
        $query = "SELECT t.*, c.nroCuenta,
                 CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                 FROM Tarjeta t
                 INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                 INNER JOIN Persona p ON c.idPersona = p.idPersona
                 ORDER BY t.estado, t.fechaExpiracion";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener una tarjeta
     * @return boolean
     */
    public function obtenerUna() {
        $query = "SELECT t.*, c.nroCuenta,
                 CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as cliente_nombre
                 FROM Tarjeta t
                 INNER JOIN Cuenta c ON t.idCuenta = c.idCuenta
                 INNER JOIN Persona p ON c.idPersona = p.idPersona
                 WHERE t.idTarjeta = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->idTarjeta);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->estado = $row['estado'];
            $this->nroTarjeta = $row['nroTarjeta'];
            $this->cvv = $row['cvv'];
            $this->fechaExpiracion = $row['fechaExpiracion'];
            // No asignamos el PIN por seguridad
            $this->idCuenta = $row['idCuenta'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtener tarjetas por cuenta
     * @param int $idCuenta
     * @return PDOStatement
     */
    public function obtenerPorCuenta($idCuenta) {
        $query = "SELECT * FROM Tarjeta WHERE idCuenta = :idCuenta";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idCuenta', $idCuenta);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Verificar si una cuenta ya tiene tarjeta
     * @param int $idCuenta
     * @return boolean
     */
    public function cuentaTieneTarjeta($idCuenta) {
        $query = "SELECT COUNT(*) as total FROM Tarjeta WHERE idCuenta = :idCuenta";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idCuenta', $idCuenta);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'] > 0;
    }
    
    /**
     * Crear una tarjeta
     * @return boolean
     */
    public function crear() {
        // Verificar si la cuenta ya tiene una tarjeta
        if ($this->cuentaTieneTarjeta($this->idCuenta)) {
            return false;
        }
        
        // Generar número de tarjeta, CVV y fecha de expiración
        $this->generarDatosTarjeta();
        
        $query = "INSERT INTO Tarjeta
                 (estado, nroTarjeta, cvv, fechaExpiracion, pin, idCuenta)
                 VALUES
                 (:estado, :nroTarjeta, :cvv, :fechaExpiracion, :pin, :idCuenta)";
        
        $stmt = $this->conn->prepare($query);
        
        // Estado por defecto: Activa (1)
        if (empty($this->estado)) {
            $this->estado = 1;
        }
        
        // Hashear el PIN por seguridad
        $pin_hash = password_hash($this->pin, PASSWORD_BCRYPT);
        
        // Vincular datos
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':nroTarjeta', $this->nroTarjeta);
        $stmt->bindParam(':cvv', $this->cvv);
        $stmt->bindParam(':fechaExpiracion', $this->fechaExpiracion);
        $stmt->bindParam(':pin', $pin_hash);
        $stmt->bindParam(':idCuenta', $this->idCuenta);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->idTarjeta = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar tarjeta
     * @return boolean
     */
    public function actualizar() {
        $query = "UPDATE Tarjeta SET estado = :estado";
        
        // Si hay un nuevo PIN, actualizarlo
        if (!empty($this->pin)) {
            $query .= ", pin = :pin";
        }
        
        $query .= " WHERE idTarjeta = :idTarjeta";
        
        $stmt = $this->conn->prepare($query);
        
        // Vincular datos
        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':idTarjeta', $this->idTarjeta);
        
        // Si hay un nuevo PIN, hashearlo y vincularlo
        if (!empty($this->pin)) {
            $pin_hash = password_hash($this->pin, PASSWORD_BCRYPT);
            $stmt->bindParam(':pin', $pin_hash);
        }
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Eliminar tarjeta
     * @return boolean
     */
    public function eliminar() {
        $query = "DELETE FROM Tarjeta WHERE idTarjeta = :idTarjeta";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarjeta', $this->idTarjeta);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generar datos de la tarjeta (número, CVV, fecha de expiración)
     */
    private function generarDatosTarjeta() {
        // Generar número de tarjeta (16 dígitos)
        $this->nroTarjeta = $this->generarNumeroTarjeta();
        
        // Generar CVV (3 dígitos)
        $this->cvv = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
        
        // Generar fecha de expiración (MM/AA, 3 años a partir de ahora)
        $fecha = new DateTime();
        $fecha->add(new DateInterval('P3Y'));
        $this->fechaExpiracion = $fecha->format('m/y');
    }
    
    /**
     * Generar número de tarjeta único
     * @return string
     */
    private function generarNumeroTarjeta() {
        // Formato: 4XXXXXXXXXXXXXXX (Visa)
        $numero = '4';
        for ($i = 0; $i < 15; $i++) {
            $numero .= mt_rand(0, 9);
        }
        
        return $numero;
    }
    
    /**
     * Verificar PIN
     * @param string $pin PIN a verificar
     * @return boolean
     */
    public function verificarPIN($pin) {
        $query = "SELECT pin FROM Tarjeta WHERE idTarjeta = :idTarjeta";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idTarjeta', $this->idTarjeta);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return password_verify($pin, $row['pin']);
        }
        
        return false;
    }
}