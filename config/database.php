<?php

/* ---------- CLASE DATABASE ---------- */
class Database {
    // Propiedades de la clase para la configuración de la base de datos
    private $host = 'localhost';       // Host de la base de datos
    private $db_name = 'banca-mercantil';  // Nombre de la base de datos
    private $username = 'root';        // Usuario de la base de datos
    private $password = '';            // Contraseña de la base de datos
    private $conn;                     // Objeto de conexión a la base de datos
    
    /**
     * Método para conectar a la base de datos
     * @return PDO Retorna un objeto de conexión PDO
     */
    public function connect() {
        // Inicializa la conexión como nula
        $this->conn = null;
        
        try {
            // Intenta establecer la conexión usando PDO
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name, // DSN (Data Source Name)
                $this->username, // Nombre de usuario
                $this->password  // Contraseña
            );
            
            // Configura el manejo de errores para lanzar excepciones
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Establece el conjunto de caracteres a UTF-8
            $this->conn->exec("SET NAMES utf8");
        } catch(PDOException $e) {
            // Captura y muestra errores de conexión
            echo 'Connection Error: ' . $e->getMessage();
        }
        
        // Retorna el objeto de conexión
        return $this->conn;
    }
}
?>