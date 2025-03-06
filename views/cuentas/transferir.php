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

/* ---------- OBTENER INFORMACIÓN DE LAS CUENTAS ---------- */
// Obtener información de las cuentas de origen y destino si están disponibles
$cuentaOrigen = isset($data['cuenta_origen']) ? $data['cuenta_origen'] : null;
$cuentaDestino = isset($data['cuenta_destino']) ? $data['cuenta_destino'] : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferir Fondos - Sistema Bancario</title>
    <!-- Enlace a la hoja de estilos principal -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css">
</head>
<body>
    <!-- Inclusión del encabezado de la página -->
    <?php include 'views/includes/header.php'; ?>
    
    <!-- Contenedor principal de la página -->
    <div class="container">
        <h1>Transferir Fondos</h1>
        
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
        
        <!-- ---------- FORMULARIO PARA BUSCAR CUENTA DE ORIGEN ---------- -->
        <div class="card">
            <div class="card-header">
                <h2>Cuenta de Origen</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>cuentas/buscarCuentaOrigen" method="POST">
                    <div class="form-group">
                        <label for="numero_cuenta_origen">Número de Cuenta de Origen:</label>
                        <input type="text" id="numero_cuenta_origen" name="numero_cuenta_origen" required
                               value="<?php echo isset($_POST['numero_cuenta_origen']) ? $_POST['numero_cuenta_origen'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Buscar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- ---------- INFORMACIÓN DE LA CUENTA DE ORIGEN ---------- -->
        <?php if ($cuentaOrigen): ?>
            <div class="card mt-20">
                <div class="card-header">
                    <h2>Información de Cuenta Origen</h2>
                </div>
                <div class="card-body">
                    <div class="info-group">
                        <!-- Número de cuenta de origen -->
                        <p><strong>Número de Cuenta:</strong> <?php echo $cuentaOrigen['numero_cuenta']; ?></p>
                        <!-- Tipo de cuenta de origen -->
                        <p><strong>Tipo de Cuenta:</strong> <?php echo $cuentaOrigen['tipo_cuenta']; ?></p>
                        <!-- Nombre del titular de la cuenta de origen -->
                        <p><strong>Titular:</strong> <?php echo $cuentaOrigen['nombre_cliente'] . ' ' . $cuentaOrigen['apellido_cliente']; ?></p>
                        <!-- Saldo disponible en la cuenta de origen -->
                        <p><strong>Saldo Disponible:</strong> $<?php echo number_format($cuentaOrigen['saldo'], 2, '.', ','); ?></p>
                        <!-- Estado de la cuenta de origen (Activa o Inactiva) -->
                        <p><strong>Estado:</strong> <?php echo $cuentaOrigen['estado'] == 1 ? 'Activa' : 'Inactiva'; ?></p>
                    </div>
                    
                    <!-- ---------- FORMULARIO PARA BUSCAR CUENTA DE DESTINO (SOLO SI LA CUENTA DE ORIGEN ESTÁ ACTIVA) ---------- -->
                    <?php if ($cuentaOrigen['estado'] == 1): ?>
                        <div class="mt-20">
                            <h3>Buscar Cuenta Destino</h3>
                            <form action="<?php echo BASE_URL; ?>cuentas/buscarCuentaDestino" method="POST">
                                <!-- Campos ocultos para enviar datos de la cuenta de origen -->
                                <input type="hidden" name="id_cuenta_origen" value="<?php echo $cuentaOrigen['id']; ?>">
                                <input type="hidden" name="numero_cuenta_origen" value="<?php echo $cuentaOrigen['numero_cuenta']; ?>">
                                
                                <!-- Campo para ingresar el número de cuenta de destino -->
                                <div class="form-group">
                                    <label for="numero_cuenta_destino">Número de Cuenta Destino:</label>
                                    <input type="text" id="numero_cuenta_destino" name="numero_cuenta_destino" required>
                                </div>
                                
                                <!-- Botón para buscar la cuenta de destino -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Buscar Cuenta Destino</button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Mensaje si la cuenta de origen está inactiva -->
                        <div class="alert alert-error mt-20">
                            <p>La cuenta de origen está inactiva. No se pueden realizar transferencias.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- ---------- INFORMACIÓN DE LA CUENTA DE DESTINO Y FORMULARIO DE TRANSFERENCIA ---------- -->
        <?php if ($cuentaOrigen && $cuentaDestino): ?>
            <div class="card mt-20">
                <div class="card-header">
                    <h2>Información de Cuenta Destino</h2>
                </div>
                <div class="card-body">
                    <div class="info-group">
                        <!-- Número de cuenta de destino -->
                        <p><strong>Número de Cuenta:</strong> <?php echo $cuentaDestino['numero_cuenta']; ?></p>
                        <!-- Tipo de cuenta de destino -->
                        <p><strong>Tipo de Cuenta:</strong> <?php echo $cuentaDestino['tipo_cuenta']; ?></p>
                        <!-- Nombre del titular de la cuenta de destino -->
                        <p><strong>Titular:</strong> <?php echo $cuentaDestino['nombre_cliente'] . ' ' . $cuentaDestino['apellido_cliente']; ?></p>
                        <!-- Estado de la cuenta de destino (Activa o Inactiva) -->
                        <p><strong>Estado:</strong> <?php echo $cuentaDestino['estado'] == 1 ? 'Activa' : 'Inactiva'; ?></p>
                    </div>
                    
                    <!-- ---------- FORMULARIO DE TRANSFERENCIA (SOLO SI LA CUENTA DE DESTINO ESTÁ ACTIVA) ---------- -->
                    <?php if ($cuentaDestino['estado'] == 1): ?>
                        <div class="mt-20">
                            <h3>Realizar Transferencia</h3>
                            <form action="<?php echo BASE_URL; ?>cuentas/procesarTransferencia" method="POST">
                                <!-- Campos ocultos para enviar datos de las cuentas y el saldo -->
                                <input type="hidden" name="id_cuenta_origen" value="<?php echo $cuentaOrigen['id']; ?>">
                                <input type="hidden" name="numero_cuenta_origen" value="<?php echo $cuentaOrigen['numero_cuenta']; ?>">
                                <input type="hidden" name="id_cuenta_destino" value="<?php echo $cuentaDestino['id']; ?>">
                                <input type="hidden" name="numero_cuenta_destino" value="<?php echo $cuentaDestino['numero_cuenta']; ?>">
                                <input type="hidden" name="saldo_origen" value="<?php echo $cuentaOrigen['saldo']; ?>">
                                
                                <!-- Campo para ingresar el monto a transferir -->
                                <div class="form-group">
                                    <label for="monto">Monto a Transferir ($):</label>
                                    <input type="number" id="monto" name="monto" min="1" max="<?php echo $cuentaOrigen['saldo']; ?>" step="0.01" required>
                                    <small>El monto máximo a transferir es $<?php echo number_format($cuentaOrigen['saldo'], 2, '.', ','); ?></small>
                                </div>
                                
                                <!-- Campo para ingresar una descripción opcional -->
                                <div class="form-group">
                                    <label for="descripcion">Descripción (Opcional):</label>
                                    <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                                </div>
                                
                                <!-- Botones para confirmar o cancelar la transferencia -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Confirmar Transferencia</button>
                                    <a href="<?php echo BASE_URL; ?>cuentas/transferir" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- Mensaje si la cuenta de destino está inactiva -->
                        <div class="alert alert-error mt-20">
                            <p>La cuenta de destino está inactiva. No se pueden realizar transferencias.</p>
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