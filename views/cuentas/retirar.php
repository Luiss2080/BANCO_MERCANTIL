<?php
/* ---------- VERIFICACIÓN DE MENSAJES DE ERROR O ÉXITO ---------- */
// Verificar si hay mensajes de error o éxito almacenados en la sesión
$errorMsg = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$successMsg = isset($_SESSION['success']) ? $_SESSION['success'] : null;

// Limpiar mensajes de sesión después de obtenerlos
if (isset($_SESSION['error'])) {
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    unset($_SESSION['success']);
}

/* ---------- OBTENER INFORMACIÓN DE LA CUENTA ---------- */
// Obtener información de la cuenta si está disponible en los datos pasados a la vista
$cuenta = isset($data['cuenta']) ? $data['cuenta'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Retiro - Sistema Bancario</title>
    <!-- Enlace a la hoja de estilos principal -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css">
</head>
<body>
    <!-- Inclusión del encabezado de la página -->
    <?php include 'views/includes/header.php'; ?>
    
    <!-- Contenedor principal de la página -->
    <div class="container">
        <h1>Realizar Retiro</h1>
        
        <!-- ---------- MENSAJES DE ERROR O ÉXITO ---------- -->
        <?php if ($errorMsg): ?>
            <div class="alert alert-error">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($successMsg): ?>
            <div class="alert alert-success">
                <?php echo $successMsg; ?>
            </div>
        <?php endif; ?>
        
        <!-- ---------- FORMULARIO PARA BUSCAR CUENTA ---------- -->
        <div class="card">
            <div class="card-header">
                <h2>Ingrese el número de cuenta</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>cuentas/buscarCuentaRetiro" method="POST">
                    <div class="form-group">
                        <label for="numero_cuenta">Número de Cuenta:</label>
                        <input type="text" id="numero_cuenta" name="numero_cuenta" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Buscar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- ---------- INFORMACIÓN DE LA CUENTA Y FORMULARIO DE RETIRO ---------- -->
        <?php if ($cuenta): ?>
            <div class="card mt-20">
                <div class="card-header">
                    <h2>Información de la Cuenta</h2>
                </div>
                <div class="card-body">
                    <div class="info-group">
                        <!-- Número de cuenta -->
                        <p><strong>Número de Cuenta:</strong> <?php echo $cuenta['numero_cuenta']; ?></p>
                        <!-- Tipo de cuenta -->
                        <p><strong>Tipo de Cuenta:</strong> <?php echo $cuenta['tipo_cuenta']; ?></p>
                        <!-- Nombre del titular -->
                        <p><strong>Titular:</strong> <?php echo $cuenta['nombre_cliente'] . ' ' . $cuenta['apellido_cliente']; ?></p>
                        <!-- Saldo disponible -->
                        <p><strong>Saldo Disponible:</strong> $<?php echo number_format($cuenta['saldo'], 2, '.', ','); ?></p>
                        <!-- Estado de la cuenta (Activa o Inactiva) -->
                        <p><strong>Estado:</strong> <?php echo $cuenta['estado'] == 1 ? 'Activa' : 'Inactiva'; ?></p>
                    </div>
                    
                    <!-- ---------- FORMULARIO DE RETIRO (SOLO SI LA CUENTA ESTÁ ACTIVA) ---------- -->
                    <?php if ($cuenta['estado'] == 1): ?>
                        <div class="mt-20">
                            <h3>Realizar Retiro</h3>
                            <form action="<?php echo BASE_URL; ?>cuentas/procesarRetiro" method="POST">
                                <!-- Campos ocultos para enviar datos adicionales -->
                                <input type="hidden" name="id_cuenta" value="<?php echo $cuenta['id']; ?>">
                                <input type="hidden" name="numero_cuenta" value="<?php echo $cuenta['numero_cuenta']; ?>">
                                <input type="hidden" name="saldo_actual" value="<?php echo $cuenta['saldo']; ?>">
                                
                                <!-- Campo para ingresar el monto a retirar -->
                                <div class="form-group">
                                    <label for="monto">Monto a Retirar ($):</label>
                                    <input type="number" id="monto" name="monto" min="1" max="<?php echo $cuenta['saldo']; ?>" step="0.01" required>
                                    <small>El monto máximo a retirar es $<?php echo number_format($cuenta['saldo'], 2, '.', ','); ?></small>
                                </div>
                                
                                <!-- Campo para ingresar una descripción opcional -->
                                <div class="form-group">
                                    <label for="descripcion">Descripción (Opcional):</label>
                                    <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                                </div>
                                
                                <!-- Botones para confirmar o cancelar el retiro -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">Confirmar Retiro</button>
                                    <a href="<?php echo BASE_URL; ?>cuentas/retirar" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Mensaje si la cuenta está inactiva -->
                        <div class="alert alert-error mt-20">
                            <p>Esta cuenta está inactiva. No se pueden realizar retiros.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Inclusión del pie de página -->
    <?php include 'views/includes/footer.php'; ?>
    <!-- Enlace al archivo de scripts JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/scripts.js"></script>
</body>
</html>