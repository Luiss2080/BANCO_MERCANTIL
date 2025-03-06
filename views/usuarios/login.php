<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco Mercantil - <?php echo $lang['login']; ?></title>
    <link rel="stylesheet" href="assets/css/styleLogin.css"> <!-- Enlace a la hoja de estilos principal -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#056f1f">
</head>
<body>
    <!-- Contenedor principal del formulario de login -->
    <div class="login-container">
        <!-- LOGO -->
        <div class="logo">
        <img src="/BANCO_CAMBA/assets/img/LOGO-FINAL.png" alt="Logo de Banco Mercantil">
        <div style="height: 25px;"></div>
        <div class="animated-bar"></div>
        </div>
        
        <!-- TÍTULO -->
        <div style="height: 15px;"></div>
        <h2><?php echo $lang['login']; ?></h2>
        <div style="height: 5px;"></div>

        <?php
        // Mostrar mensaje flash si existe (notificaciones de error o éxito en login)
        $flashMessage = $this->session->getFlashMessage();
        if ($flashMessage) {
            echo '<div class="alert alert-' . $flashMessage['type'] . '">' . $flashMessage['message'] . '</div>';
        }
        ?>

        <!-- FORMULARIO -->
        <form id="login-form" method="POST" action="index.php?controller=usuario&action=autenticar" onsubmit="return validateForm()">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <!-- Campo de Usuario -->
            <div class="form-group">
                <div class="input-with-icon">
                    <span class="icon">👤</span> <!-- Emoji de usuario -->
                    <input type="text" id="username" name="username" placeholder="<?php echo $lang['username_placeholder']; ?>" required autocomplete="username">
                </div>
            </div>
            
            <!-- Campo de Contraseña -->
            <div class="form-group">
                <div class="input-with-icon">
                    <span class="icon">🔒</span> <!-- Emoji de contraseña -->
                    <input type="password" id="password" name="password" placeholder="<?php echo $lang['password_placeholder']; ?>" required autocomplete="current-password">
                </div>
            </div>
            
            <div style="height: 2px;"></div>
            <button type="submit" id="login-button">
                <span id="login-text"><?php echo $lang['login_button_text']; ?></span>
                <span id="loading-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
            </button>
            
            <a href="#" class="recover-password"><?php echo $lang['forgot_password']; ?></a>
        </form>

        <!-- Selector de idioma -->
        <div class="language-selector">
            <span class="icon">🌐</span> <!-- Emoji de globo -->
            <select id="language">
                <option value="es" <?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'es') ? 'selected' : ''; ?>>🇪🇸 Español</option>
                <option value="en" <?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') ? 'selected' : ''; ?>>🇺🇸 English</option>
                <option value="pt" <?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == 'pt') ? 'selected' : ''; ?>>🇵🇹 Portuguese</option>
            </select>
        </div>

        <!-- Versión -->
        <p class="version">Versión 4.1.0.6</p>
    </div>

    <script>
        // Selector de idioma
        document.getElementById('language').addEventListener('change', function() {
            window.location.href = `index.php?controller=usuario&action=cambiarIdioma&lang=${this.value}`;
        });

        // Validación del formulario
        function validateForm() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!username || !password) {
                alert('<?php echo $lang['fill_all_fields']; ?>');
                return false;
            }

            // Mostrar spinner de carga
            document.getElementById('login-text').style.display = 'none';
            document.getElementById('loading-spinner').style.display = 'inline-block';

            return true;
        }
    </script>
</body>
</html>