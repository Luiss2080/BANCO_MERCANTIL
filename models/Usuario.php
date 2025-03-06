<?php
/**
 * Modelo Usuario
 */
class Usuario {
    // Conexión a la base de datos
    private $conn;
    
    // Propiedades del usuario
    public $idUsuario;
    public $ultimoInicioSesion;
    public $intentosFallido;
    public $username;
    public $password; // Para almacenar la contraseña en texto plano antes de hashearla
    public $idPersona;
    
    /**
     * Constructor con DB
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener todos los usuarios
     * @return PDOStatement
     */
    public function obtenerTodos() {
        $query = "SELECT u.*, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as nombre_completo
                  FROM Usuario u
                  INNER JOIN Persona p ON u.idPersona = p.idPersona
                  ORDER BY u.username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Obtener un usuario
     * @return boolean
     */
    public function obtenerUno() {
        $query = "SELECT u.*, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as nombre_completo
                  FROM Usuario u
                  INNER JOIN Persona p ON u.idPersona = p.idPersona
                  WHERE u.idUsuario = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->idUsuario);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->ultimoInicioSesion = $row['ultimoInicioSesion'];
            $this->intentosFallido = $row['intentosFallido'];
            $this->username = $row['username'];
            // No asignamos la contraseña por seguridad
            $this->idPersona = $row['idPersona'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Obtener usuario por nombre de usuario
     * @param string $username
     * @return boolean
     */
    public function obtenerPorUsername($username) {
        $query = "SELECT u.*, 
                  CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ', p.apellidoMaterno) as nombre_completo
                  FROM Usuario u
                  INNER JOIN Persona p ON u.idPersona = p.idPersona
                  WHERE u.username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->idUsuario = $row['idUsuario'];
            $this->ultimoInicioSesion = $row['ultimoInicioSesion'];
            $this->intentosFallido = $row['intentosFallido'];
            $this->username = $row['username'];
            // No asignamos la contraseña por seguridad
            $this->idPersona = $row['idPersona'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Verificar si un nombre de usuario ya existe
     * @param string $username
     * @return boolean
     */
    public function usernameExiste($username) {
        $query = "SELECT COUNT(*) as total FROM Usuario WHERE username = :username";
        
        if ($this->idUsuario) {
            // Si estamos actualizando, excluir el usuario actual
            $query .= " AND idUsuario != :idUsuario";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($this->idUsuario) {
            $stmt->bindParam(':idUsuario', $this->idUsuario);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'] > 0;
    }
    
    /**
     * Crear un usuario
     * @return boolean
     */
    public function crear() {
        // Verificar si el nombre de usuario ya existe
        if ($this->usernameExiste($this->username)) {
            return false;
        }
        
        $query = "INSERT INTO Usuario
                 (ultimoInicioSesion, intentosFallido, username, password, idPersona)
                 VALUES
                 (NULL, 0, :username, :password, :idPersona)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->username = htmlspecialchars(strip_tags($this->username));
        
        // Hashear la contraseña
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Vincular datos
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':idPersona', $this->idPersona);
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            $this->idUsuario = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar usuario
     * @return boolean
     */
    public function actualizar() {
        // Verificar si el nombre de usuario ya existe
        if ($this->usernameExiste($this->username)) {
            return false;
        }
        
        $query = "UPDATE Usuario SET
                username = :username";
        
        // Si hay una nueva contraseña, actualizarla
        if (!empty($this->password)) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE idUsuario = :idUsuario";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->username = htmlspecialchars(strip_tags($this->username));
        
        // Vincular datos
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':idUsuario', $this->idUsuario);
        
        // Si hay una nueva contraseña, hashearla y vincularla
        if (!empty($this->password)) {
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }
        
        // Ejecutar consulta
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Eliminar usuario
     * @return boolean
     */
    public function eliminar() {
        $query = "DELETE FROM Usuario WHERE idUsuario = :idUsuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $this->idUsuario);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Resetear intentos fallidos de inicio de sesión
     * @return boolean
     */
    public function resetearIntentosFallidos() {
        $query = "UPDATE Usuario SET intentosFallido = 0 WHERE idUsuario = :idUsuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $this->idUsuario);
        
        if ($stmt->execute()) {
            $this->intentosFallido = 0;
            return true;
        }
        
        return false;
    }
    
    /**
     * Actualizar último inicio de sesión
     * @return boolean
     */
    public function actualizarUltimoInicioSesion() {
        $query = "UPDATE Usuario SET ultimoInicioSesion = NOW() WHERE idUsuario = :idUsuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $this->idUsuario);
        
        if ($stmt->execute()) {
            $this->ultimoInicioSesion = date('Y-m-d H:i:s');
            return true;
        }
        
        return false;
    }
    
    /**
     * Incrementar intentos fallidos de inicio de sesión
     * @return boolean
     */
    public function incrementarIntentosFallidos() {
        $query = "UPDATE Usuario SET intentosFallido = intentosFallido + 1 WHERE idUsuario = :idUsuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idUsuario', $this->idUsuario);
        
        if ($stmt->execute()) {
            $this->intentosFallido++;
            return true;
        }
        
        return false;
    }
}
?>