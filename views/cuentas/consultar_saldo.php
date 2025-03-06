<!-- Título de la página -->
<h1><?php echo $lang['check_balance']; ?></h1>

<!-- Tarjeta que contiene el formulario para consultar el saldo -->
<div class="card">
    <div class="card-header">
        <h5><?php echo $lang['account_balance_inquiry']; ?></h5>
    </div>
    <div class="card-body">
        <!-- Formulario para consultar el saldo de una cuenta -->
        <form method="POST" action="index.php?controller=cuenta&action=consultarSaldo">
            <div class="form-group">
                <!-- Campo para ingresar el número de cuenta -->
                <label for="nroCuenta"><?php echo $lang['account_number']; ?>:</label>
                <input type="text" class="form-control" id="nroCuenta" name="nroCuenta" required>
            </div>
            <!-- Botón para enviar el formulario y consultar el saldo -->
            <button type="submit" class="btn btn-primary">
                <i class="icon-search"></i> <?php echo $lang['check_balance']; ?>
            </button>
        </form>
    </div>
</div>

<!-- Si se ha consultado una cuenta, mostrar la información -->
<?php if (isset($cuenta)): ?>
<div class="card mt-4">
    <div class="card-header">
        <h5><?php echo $lang['account_information']; ?></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Información de la cuenta -->
            <div class="col-md-6">
                <h6><?php echo $lang['account_details']; ?></h6>
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $lang['account_number']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['nroCuenta']); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['account_type']; ?>:</th>
                        <td>
                            <?php
                            // Mostrar el tipo de cuenta (ahorro o corriente)
                            if ($cuenta['tipoCuenta'] == 1) {
                                echo $lang['savings_account'];
                            } else {
                                echo $lang['checking_account'];
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['currency']; ?>:</th>
                        <td>
                            <?php
                            // Mostrar el tipo de moneda (bolivianos o dólares)
                            if ($cuenta['tipoMoneda'] == 1) {
                                echo $lang['bolivianos'];
                            } else {
                                echo $lang['dollars'];
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['opening_date']; ?>:</th>
                        <td><?php echo date('d/m/Y', strtotime($cuenta['fechaApertura'])); ?></td>
                    </tr>
                </table>
            </div>
            <!-- Información del cliente asociado a la cuenta -->
            <div class="col-md-6">
                <h6><?php echo $lang['client_information']; ?></h6>
                <table class="table table-bordered">
                    <tr>
                        <th><?php echo $lang['name']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_nombre']); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['id_number']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_ci']); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['phone']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_telefono']); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['email']; ?>:</th>
                        <td><?php echo htmlspecialchars($cuenta['cliente_email']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Sección para mostrar el saldo actual de la cuenta -->
        <div class="balance-display text-center mt-4">
            <h4><?php echo $lang['current_balance']; ?></h4>
            <div class="balance-amount">
                <?php 
                // Mostrar el saldo con el símbolo de moneda correspondiente
                $moneda = ($cuenta['tipoMoneda'] == 1) ? 'Bs. ' : '$ ';
                echo '<span class="currency">' . $moneda . '</span><span class="amount">' . number_format($cuenta['saldo'], 2) . '</span>'; 
                ?>
            </div>
            <!-- Estado de la cuenta (activa o inactiva) -->
            <div class="balance-status mt-2">
                <?php if ($cuenta['estado'] == 1): ?>
                    <span class="badge" style="background-color: #28a745;"><?php echo $lang['active']; ?></span>
                <?php else: ?>
                    <span class="badge" style="background-color: #dc3545;"><?php echo $lang['inactive']; ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Botones de acción para la cuenta -->
        <div class="action-buttons text-center mt-4">
            <!-- Botón para realizar un depósito -->
            <a href="index.php?controller=transaccion&action=depositar&idCuenta=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-success">
                <i class="icon-arrow-down"></i> <?php echo $lang['deposit']; ?>
            </a>
            <!-- Botón para realizar un retiro -->
            <a href="index.php?controller=transaccion&action=retirar&idCuenta=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-danger">
                <i class="icon-arrow-up"></i> <?php echo $lang['withdrawal']; ?>
            </a>
            <!-- Botón para ver el historial de transacciones -->
            <a href="index.php?controller=cuenta&action=historial&id=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-primary">
                <i class="icon-list"></i> <?php echo $lang['transaction_history']; ?>
            </a>
        </div>
    </div>
</div>

<!-- Si hay movimientos recientes, mostrar una tabla con las transacciones -->
<?php if (!empty($movimientos)): ?>
<div class="card mt-4">
    <div class="card-header">
        <h5><?php echo $lang['recent_transactions']; ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo $lang['date']; ?></th>
                        <th><?php echo $lang['transaction_type']; ?></th>
                        <th><?php echo $lang['amount']; ?></th>
                        <th><?php echo $lang['description']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $movimiento): ?>
                        <tr>
                            <!-- Fecha de la transacción -->
                            <td><?php echo date('d/m/Y H:i', strtotime($movimiento['fecha'])); ?></td>
                            <!-- Tipo de transacción -->
                            <td>
                                <?php
                                $tipoTransaccion = '';
                                $badgeClass = '';
                                
                                switch($movimiento['tipo']) {
                                    case 1:
                                        $tipoTransaccion = $lang['deposit'];
                                        $badgeClass = 'success';
                                        break;
                                    case 2:
                                        $tipoTransaccion = $lang['withdrawal'];
                                        $badgeClass = 'danger';
                                        break;
                                    case 3:
                                        $tipoTransaccion = $lang['received_transfer'];
                                        $badgeClass = 'info';
                                        break;
                                    case 4:
                                        $tipoTransaccion = $lang['sent_transfer'];
                                        $badgeClass = 'warning';
                                        break;
                                }
                                ?>
                                <span class="badge" style="background-color: var(--bs-<?php echo $badgeClass; ?>);">
                                    <?php echo $tipoTransaccion; ?>
                                </span>
                            </td>
                            <!-- Monto de la transacción -->
                            <td>
                                <?php 
                                $moneda = ($cuenta['tipoMoneda'] == 1) ? 'Bs. ' : '$ ';
                                $prefix = in_array($movimiento['tipo'], [1, 3]) ? '+' : '-';
                                echo $prefix . ' ' . $moneda . number_format($movimiento['monto'], 2); 
                                ?>
                            </td>
                            <!-- Descripción de la transacción -->
                            <td><?php echo htmlspecialchars($movimiento['descripcion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Botón para ver todas las transacciones -->
        <div class="text-center mt-3">
            <a href="index.php?controller=cuenta&action=historial&id=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-outline-primary">
                <?php echo $lang['view_all_transactions']; ?>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>