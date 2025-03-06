<?php
/**
 * Modelo Cliente (Persona)
 */
class Cliente {
    // Conexión a la base de datos
    private $conn;
    
    // Propiedades del cliente
    public $idPersona;
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $direccion;
    public $telefono;
    public $email;
    public $fechaNacimiento;
    public $ci;
    public $idOficina;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todos los clientes
     * @return PDOStatement
     */
    public function obtenerTodos() {
        $query = "SELECT p.*, o.nombre as oficina_nombre
                 FROM Persona p
                 INNER JOIN Oficina o ON p.idOficina = o.idOficina
                 ORDER BY p.apellidoPaterno, p.apellidoMaterno, p.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener un cliente
     * @return boolean
     */
    public function obtenerUno() {
        $query = "SELECT p.*, o.nombre as oficina_nombre
                 FROM Persona p
                 INNER JOIN Oficina o ON p.idOficina = o.idOficina
                 WHERE p.idPersona = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->idPersona);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->nombre = $row['nombre'];
            $this->apellidoPaterno = $row['apellidoPaterno'];
            $this->apellidoMaterno = $row['apellidoMaterno'];
            $this->direccion = $row['direccion'];
            $this->telefono = $row['telefono'];
            $this->email = $row['email'];
            $this->fechaNacimiento = $row['fechaNacimiento'];
            $this->ci = $row['ci'];
            $this->idOficina = $row['idOficina'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Crear un cliente
     * @return boolean
     */
    public function crear() {
        $query = "INSERT INTO Persona
                 (nombre, apellidoPaterno, apellidoMaterno, direccion, telefono, email, fechaNacimiento, ci, idOficina)
                 VALUES
                 (:nombre, :apellidoPaterno, :apellidoMaterno, :direccion, :telefono, :email, :fechaNacimiento, :ci, :idOficina)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidoPaterno = htmlspecialchars(strip_tags($this->apellidoPaterno));
        $this->apellidoMaterno = htmlspecialchars(strip_tags($this->apellidoMaterno));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->fechaNacimiento = htmlspecialchars(strip_tags($this->fechaNacimiento));
        $this->ci = htmlspecialchars(strip_tags($this->ci));
        
        // Vincular datos
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellidoPaterno', $this->apellidoPaterno);
        $stmt->bindParam(':apellidoMaterno', $this->apellidoMaterno);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':fechaNacimiento', $this->fechaNacimiento);
        $stmt->bindParam(':ci', $this->ci);
        $stmt->bindParam(':idOficina', $this->idOficina);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->idPersona = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar cliente
     * @return boolean
     */
    public function actualizar() {
        $query = "UPDATE Persona SET
                nombre = :nombre,
                apellidoPaterno = :apellidoPaterno,
                apellidoMaterno = :apellidoMaterno,
                direccion = :direccion,
                telefono = :telefono,
                email = :email,
                fechaNacimiento = :fechaNacimiento,
                ci = :ci,
                idOficina = :idOficina
                WHERE idPersona = :idPersona";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellidoPaterno = htmlspecialchars(strip_tags($this->apellidoPaterno));
        $this->apellidoMaterno = htmlspecialchars(strip_tags($this->apellidoMaterno));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->fechaNacimiento = htmlspecialchars(strip_tags($this->fechaNacimiento));
        $this->ci = htmlspecialchars(strip_tags($this->ci));
        
        // Vincular datos
        $stmt->bindParam(':idPersona', $this->idPersona);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellidoPaterno', $this->apellidoPaterno);
        $stmt->bindParam(':apellidoMaterno', $this->apellidoMaterno);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':fechaNacimiento', $this->fechaNacimiento);
        $stmt->bindParam(':ci', $this->ci);
        $stmt->bindParam(':idOficina', $this->idOficina);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Eliminar cliente
     * @return boolean
     */
    public function eliminar() {
        // Primero verificar si el cliente tiene cuentas
        $query = "SELECT COUNT(*) as total FROM Cuenta WHERE idPersona = :idPersona";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPersona', $this->idPersona);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            // El cliente tiene cuentas, no se puede eliminar
            return false;
        }
        
        // Se puede eliminar
        $query = "DELETE FROM Persona WHERE idPersona = :idPersona";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idPersona', $this->idPersona);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Buscar clientes
     * @param string $keyword
     * @return PDOStatement
     */
    public function buscar($keyword) {
        $query = "SELECT p.*, o.nombre as oficina_nombre
                 FROM Persona p
                 INNER JOIN Oficina o ON p.idOficina = o.idOficina
                 WHERE p.nombre LIKE :keyword
                 OR p.apellidoPaterno LIKE :keyword
                 OR p.apellidoMaterno LIKE :keyword
                 OR p.ci LIKE :keyword
                 ORDER BY p.apellidoPaterno, p.apellidoMaterno, p.nombre";
        
        $keyword = "%{$keyword}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        
        return $stmt;
    }
}
?>