<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título de la página de error -->
    <title>Error - Banco Mercantil</title>
    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Manifest para PWA (Progressive Web App) -->
    <link rel="manifest" href="manifest.json">
    <!-- Color del tema para navegadores móviles -->
    <meta name="theme-color" content="#056f1f">
</head>
<body>
    <div class="container">
        <!-- Contenedor del mensaje de error -->
        <div class="error-container" style="text-align: center; margin-top: 100px;">
            <!-- Título del error -->
            <h1 style="color: #dc3545;">¡Oops! Ha ocurrido un error</h1>
            <!-- Mensaje descriptivo del error -->
            <p>Lo sentimos, la página que estás buscando no existe o no está disponible en este momento.</p>
            <p>Por favor, verifica la URL o regresa a la página de inicio.</p>
            <!-- Botón para volver al inicio -->
            <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Volver al inicio</a>
        </div>
    </div>
</body>
</html>