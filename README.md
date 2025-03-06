# Sistema Bancario - Banco Camba

Este proyecto es un sistema web para módulo bancario que permite realizar operaciones financieras, desarrollado para Banco Camba.

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Navegador web moderno con soporte para PWA

## Características

- Control de oficinas (central y agencias)
- Gestión de clientes
- Apertura y gestión de cuentas bancarias
- Operaciones de depósito y retiro
- Consulta de saldo disponible
- Historial de transacciones con filtrado por fechas
- Soporte para múltiples idiomas (Español e Inglés)
- Funcionalidad PWA (Aplicación Web Progresiva)
- Servicio web para cajeros automáticos (ATM)

## Instalación

1. Clone el repositorio o descargue el código fuente en su servidor web:
```
git clone https://github.com/su-usuario/banco-camba.git
```

2. Cree una base de datos MySQL para el proyecto.

3. Importe el esquema de la base de datos desde el archivo `database/banco_camba.sql`:
```
mysql -u su_usuario -p su_base_de_datos < database/banco_camba.sql
```

4. Configure los parámetros de conexión a la base de datos en el archivo `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'banco_camba';
private $username = 'su_usuario';
private $password = 'su_contraseña';
```

5. Asegúrese de que el directorio raíz del servidor web apunte a la carpeta del proyecto.

6. Acceda al sistema a través de su navegador: `http://localhost/banco-camba/`

## Acceso al sistema

Credenciales por defecto:
- Usuario: `admin`
- Contraseña: `admin123`

Se recomienda cambiar estas credenciales inmediatamente después de la instalación.

## Estructura del proyecto

El proyecto sigue la arquitectura MVC (Modelo-Vista-Controlador):

- `/config`: Archivos de configuración
- `/controllers`: Controladores de la aplicación
- `/models`: Modelos de datos
- `/views`: Vistas de la aplicación
- `/assets`: Archivos estáticos (CSS, JS, imágenes)
- `/langs`: Archivos de idiomas
- `/utils`: Utilidades y clases auxiliares
- `/api`: Servicios web para ATM

## API para Cajeros Automáticos (ATM)

El sistema incluye una API para integrar con cajeros automáticos:

- Autenticación de tarjetas
- Consulta de saldo
- Retiros
- Depósitos

Documentación de la API disponible en `/api/README.md`

## Seguridad

- Las contraseñas se almacenan con hash seguro (bcrypt)
- Validación de datos de entrada
- Protección contra inyección SQL (PDO con parámetros preparados)
- Control de sesiones

## Soporte PWA

El sistema funciona como una Aplicación Web Progresiva (PWA), lo que permite:

- Instalación en dispositivos móviles
- Funcionamiento offline para ciertas funcionalidades
- Mejor experiencia de usuario en dispositivos móviles

## Autor

Este sistema fue desarrollado por [Su Nombre] para Banco Camba.

## Licencia

Este proyecto es privado y está protegido por derechos de autor. No se permite su distribución, copia o modificación sin autorización expresa del autor.