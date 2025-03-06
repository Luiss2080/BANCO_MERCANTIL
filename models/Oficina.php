<?php
/**
 * Modelo Oficina
 */
class Oficina {
    // Conexión a la base de datos
    private $conn;
    
    // Propiedades de la oficina
    public $idOficina;
    public $central; // Boolean
    public $nombre;
    public $direccion;
    public $telefono;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todas las oficinas
     * @return PDOStatement
     */
    public function obtenerTodas() {
        $query = "SELECT * FROM Oficina ORDER BY central DESC, nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener una oficina
     * @return boolean
     */
    public function obtenerUna() {
        $query = "SELECT * FROM Oficina WHERE idOficina = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->idOficina);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->central = $row['central'];
            $this->nombre = $row['nombre'];
            $this->direccion = $row['direccion'];
            $this->telefono = $row['telefono'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Crear una oficina
     * @return boolean
     */
    public function crear() {
        $query = "INSERT INTO Oficina
                 (central, nombre, direccion, telefono)
                 VALUES
                 (:central, :nombre, :direccion, :telefono)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        
        // Convertir central a boolean
        $this->central = (bool)$this->central;
        
        // Vincular datos
        $stmt->bindParam(':central', $this->central, PDO::PARAM_BOOL);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':telefono', $this->telefono);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->idOficina = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar oficina
     * @return boolean
     */
    public function actualizar() {
        $query = "UPDATE Oficina SET
                central = :central,
                nombre = :nombre,
                direccion = :direccion,
                telefono = :telefono
                WHERE idOficina = :idOficina";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        
        // Convertir central a boolean
        $this->central = (bool)$this->central;
        
        // Vincular datos
        $stmt->bindParam(':idOficina', $this->idOficina);
        $stmt->bindParam(':central', $this->central, PDO::PARAM_BOOL);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':telefono', $this->telefono);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Eliminar oficina
     * @return boolean
     */
    public function eliminar() {
        // Primero verificar si hay clientes asociados a esta oficina
        $query = "SELECT COUNT(*) as total FROM Persona WHERE idOficina = :idOficina";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idOficina', $this->idOficina);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            // Hay clientes asociados, no se puede eliminar
            return false;
        }
        
        // Se puede eliminar
        $query = "DELETE FROM Oficina WHERE idOficina = :idOficina";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idOficina', $this->idOficina);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Contar oficinas
     * @return int
     */
    public function contarOficinas() {
        $query = "SELECT COUNT(*) as total FROM Oficina";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
}
?>