<?php
/**
 * Authentication Utility
 * Handles user authentication and authorization
 */
class Auth {
    /**
     * Check if user is logged in
     * @return boolean
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Authenticate user
     * @param string $username
     * @param string $password
     * @return boolean|array Returns user data on success, false on failure
     */
    public function login($username, $password) {
        // Connect to database
        $database = new Database();
        $db = $database->connect();
        
        // Prepare query
        $query = "SELECT u.idUsuario, u.username, u.password, u.intentosFallido, 
                  p.idPersona, p.nombre, p.apellidoPaterno, p.apellidoMaterno, p.idOficina 
                  FROM Usuario u 
                  INNER JOIN Persona p ON u.idPersona = p.idPersona 
                  WHERE u.username = :username";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password (assuming it's stored as a hash)
            if (password_verify($password, $row['password'])) {
                // Reset failed attempts
                $this->resetFailedAttempts($row['idUsuario']);
                
                // Update last login time
                $this->updateLastLogin($row['idUsuario']);
                
                // Return user data
                return [
                    'id' => $row['idUsuario'],
                    'username' => $row['username'],
                    'nombre' => $row['nombre'],
                    'apellido' => $row['apellidoPaterno'] . ' ' . $row['apellidoMaterno'],
                    'idPersona' => $row['idPersona'],
                    'idOficina' => $row['idOficina']
                ];
            } else {
                // Increment failed attempts
                $this->incrementFailedAttempts($row['idUsuario']);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Update the last login timestamp
     * @param int $userId
     */
    private function updateLastLogin($userId) {
        $database = new Database();
        $db = $database->connect();
        
        $query = "UPDATE Usuario SET ultimoInicioSesion = NOW() WHERE idUsuario = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    /**
     * Reset failed login attempts
     * @param int $userId
     */
    private function resetFailedAttempts($userId) {
        $database = new Database();
        $db = $database->connect();
        
        $query = "UPDATE Usuario SET intentosFallido = 0 WHERE idUsuario = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    /**
     * Increment failed login attempts
     * @param int $userId
     */
    private function incrementFailedAttempts($userId) {
        $database = new Database();
        $db = $database->connect();
        
        $query = "UPDATE Usuario SET intentosFallido = intentosFallido + 1 WHERE idUsuario = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    /**
     * Log user out
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
    }
}
?>