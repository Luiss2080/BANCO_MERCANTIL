<h1><?php echo $lang['deposit']; ?></h1> <!-- Título de la página: "Depositar" (traducido según el idioma seleccionado) -->

<div class="card">
    <div class="card-header">
        <h5><?php echo $lang['account']; ?></h5> <!-- Encabezado de la tarjeta: "Cuenta" -->
    </div>
    <div class="card-body">
        <?php if (!isset($cuenta)): ?>
        <!-- Formulario para buscar una cuenta si no se ha seleccionado una -->
        <form method="POST" action="index.php?controller=transaccion&action=buscarCuentaDeposito">
            <div class="form-group">
                <label for="nroCuenta"><?php echo $lang['account_number']; ?>:</label>
                <input type="text" class="form-control" id="nroCuenta" name="nroCuenta" required> <!-- Campo para ingresar el número de cuenta -->
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="icon-search"></i> <?php echo $lang['search_account']; ?> <!-- Botón para buscar la cuenta -->
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($cuenta)): ?>
<!-- Si se ha encontrado una cuenta, se muestra la información -->
<div class="card mt-4">
    <div class="card-header">
        <h5><?php echo $lang['account_information']; ?></h5> <!-- Encabezado: "Información de la cuenta" -->
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><?php echo $lang['account_details']; ?></h6> <!-- Subtítulo: "Detalles de la cuenta" -->
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $lang['account_number']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['nroCuenta']); ?></td> <!-- Número de cuenta -->
                    </tr>
                    <tr>
                        <th><?php echo $lang['account_type']; ?>:</th>
                        <td>
                            <?php
                            if ($cuenta['tipoCuenta'] == 1) {
                                echo $lang['savings_account']; // Cuenta de ahorros
                            } else {
                                echo $lang['checking_account']; // Cuenta corriente
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['currency']; ?>:</th>
                        <td>
                            <?php
                            if ($cuenta['tipoMoneda'] == 1) {
                                echo $lang['bolivianos']; // Moneda en bolivianos
                            } else {
                                echo $lang['dollars']; // Moneda en dólares
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['current_balance']; ?>:</th>
                        <td>
                            <?php 
                            $moneda = ($cuenta['tipoMoneda'] == 1) ? 'Bs. ' : '$ '; // Símbolo de la moneda
                            echo $moneda . number_format($cuenta['saldo'], 2); // Saldo formateado
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['status']; ?>:</th>
                        <td>
                            <?php if ($cuenta['estado'] == 1): ?>
                                <span class="badge" style="background-color: #28a745;"><?php echo $lang['active']; ?></span> <!-- Estado: Activo -->
                            <?php else: ?>
                                <span class="badge" style="background-color: #dc3545;"><?php echo $lang['inactive']; ?></span> <!-- Estado: Inactivo -->
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6><?php echo $lang['client_information']; ?></h6> <!-- Subtítulo: "Información del cliente" -->
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $lang['name']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_nombre']); ?></td> <!-- Nombre del cliente -->
                    </tr>
                    <tr>
                        <th><?php echo $lang['id_number']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_ci']); ?></td> <!-- Número de identificación del cliente -->
                    </tr>
                    <tr>
                        <th><?php echo $lang['phone']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_telefono']); ?></td> <!-- Teléfono del cliente -->
                    </tr>
                    <tr>
                        <th><?php echo $lang['email']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_email']); ?></td> <!-- Correo electrónico del cliente -->
                    </tr>
                </table>
            </div>
        </div>
        
        <?php if ($cuenta['estado'] == 1): ?>
            <!-- Si la cuenta está activa, se muestra el formulario para realizar un depósito -->
            <div class="deposit-form mt-4">
                <h5><?php echo $lang['make_deposit']; ?></h5> <!-- Título: "Realizar depósito" -->
                <form method="POST" action="index.php?controller=transaccion&action=procesarDeposito">
                    <input type="hidden" name="idCuenta" value="<?php echo $cuenta['idCuenta']; ?>"> <!-- ID de la cuenta oculto -->
                    <input type="hidden" name="tipoMoneda" value="<?php echo $cuenta['tipoMoneda']; ?>"> <!-- Tipo de moneda oculto -->
                    
                    <div class="form-group">
                        <label for="monto"><?php echo $lang['amount']; ?>:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <?php echo ($cuenta['tipoMoneda'] == 1) ? 'Bs.' : '$'; ?> <!-- Símbolo de la moneda -->
                                </span>
                            </div>
                            <input type="number" class="form-control" id="monto" name="monto" min="1" step="0.01" required> <!-- Campo para ingresar el monto -->
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion"><?php echo $lang['description']; ?>:</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea> <!-- Campo para la descripción -->
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <i class="icon-check"></i> <?php echo $lang['confirm_deposit']; ?> <!-- Botón para confirmar el depósito -->
                        </button>
                        <a href="index.php?controller=transaccion&action=depositar" class="btn btn-secondary">
                            <i class="icon-x"></i> <?php echo $lang['cancel']; ?> <!-- Botón para cancelar -->
                        </a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Si la cuenta está inactiva, se muestra un mensaje de error -->
            <div class="alert alert-danger mt-4">
                <i class="icon-alert-triangle"></i> <?php echo $lang['inactive_account_deposit_error']; ?> <!-- Mensaje de error -->
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>