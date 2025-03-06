<?php
/**
 * Clase Autoloader para carga automática de clases
 */
class Autoloader {
    /**
     * Registrar el autoloader
     */
    public static function register() {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }
    
    /**
     * Autoload de clases
     * @param string $className Nombre de la clase a cargar
     */
    public static function autoload($className) {
        // Definir directorios donde buscar clases
        $directories = array(
            'models/',
            'controllers/',
            'utils/',
            'views/',
            'libs/',
            'api/',
            'assets/',
            'config/',
            'langs/'
        );
        
        // Verificar cada directorio
        foreach ($directories as $directory) {
            $file = $directory . $className . '.php';
            
            // Si existe el archivo, incluirlo
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
}
?>