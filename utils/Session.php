<?php
/**
 * Clase para manejar sesiones en el sistema
 */
class Session {
    /**
     * Constructor
     */
    public function __construct() {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Establecer datos de usuario después del login
     * @param array $userData
     */
    public function setUserData($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['nombre'] = $userData['nombre'];
        $_SESSION['apellido'] = $userData['apellido'];
        $_SESSION['idPersona'] = $userData['idPersona'];
        $_SESSION['idOficina'] = $userData['idOficina'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Obtener datos de usuario desde la sesión
     * @return array
     */
    public function getUserData() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'nombre' => $_SESSION['nombre'],
                'apellido' => $_SESSION['apellido'],
                'idPersona' => $_SESSION['idPersona'],
                'idOficina' => $_SESSION['idOficina'],
                'is_logged_in' => $_SESSION['is_logged_in'],
                'login_time' => $_SESSION['login_time']
            ];
        }
        
        return null;
    }
    
    // Implementacion de  protección contra CSRF
    // 03/03/2025: Añadido método para generar y validar tokens CSRF
    public function generateCSRFToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    public function validateCSRFToken($token) {
        if (isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token) {
            // Renovar token
            $this->generateCSRFToken();
            return true;
        }
        return false;
    }

    /**
     * Establecer un mensaje flash para mostrarlo en la próxima carga de página
     * @param string $type Tipo de mensaje (success, error, info, warning)
     * @param string $message Contenido del mensaje
     */
    public function setFlashMessage($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Obtener mensaje flash y eliminarlo de la sesión
     * @return array|null
     */
    public function getFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        
        return null;
    }
    
    /**
     * Establecer idioma actual
     * @param string $lang Código de idioma (es, en)
     * 02/03/2025: Asegurado que el idioma se guarde correctamente en la sesión
     */
    public function setLanguage($lang) {
        $_SESSION['lang'] = $lang;
    }
    
    /**
     * Obtener idioma actual
     * @return string
     * 02/03/2025: Añadida validación para asegurar que siempre retorne un idioma válido
     */
    public function getLanguage() {
        return isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['es', 'en']) ? $_SESSION['lang'] : 'es';
    }
}
?>