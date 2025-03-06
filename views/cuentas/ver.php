<h1><?php echo $lang['account_details']; ?></h1>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <h3><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno . ' ' . $cliente->apellidoMaterno); ?></h3>
                <h4><?php echo $lang['account_number']; ?>: <?php echo htmlspecialchars($cuenta->nroCuenta); ?></h4>
            </div>
            <div class="col-md-4 text-right">
                <a href="index.php?controller=cuenta&action=editar&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-warning">
                    <i class="icon-edit"></i> <?php echo $lang['edit']; ?>
                </a>
                <a href="index.php?controller=cliente&action=ver&id=<?php echo $cliente->idPersona; ?>" class="btn btn-secondary">
                    <i class="icon-arrow-left"></i> <?php echo $lang['back_to_client']; ?>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Información de la cuenta -->
            <div class="col-md-6">
                <h4><?php echo $lang['account_information']; ?></h4>
                <table class="table table-borderless">
                    <tr>
                        <th><?php echo $lang['account_type']; ?>:</th>
                        <td>
                            <?php
                            if ($cuenta->tipoCuenta == 1) {
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
                            if ($cuenta->tipoMoneda == 1) {
                                echo $lang['bolivianos'];
                            } else {
                                echo $lang['dollars'];
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['opening_date']; ?>:</th>
                        <td><?php echo date('d/m/Y', strtotime($cuenta->fechaApertura)); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['status']; ?>:</th>
                        <td>
                            <?php if ($cuenta->estado == 1): ?>
                                <span class="badge" style="background-color: #28a745;"><?php echo $lang['active']; ?></span>
                            <?php else: ?>
                                <span class="badge" style="background-color: #dc3545;"><?php echo $lang['inactive']; ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Saldo y acciones rápidas -->
            <div class="col-md-6">
                <div class="balance-box" style="text-align: center; border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h4><?php echo $lang['current_balance']; ?></h4>
                    <h2 style="font-size: 2.5rem; color: #056f1f;">
                        <?php
                        $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                        echo $moneda . number_format($cuenta->saldo, 2);
                        ?>
                    </h2>
                    <div class="action-buttons" style="margin-top: 15px;">
                        <a href="index.php?controller=transaccion&action=depositar&idCuenta=<?php echo $cuenta->idCuenta; ?>" class="btn btn-success">
                            <i class="icon-arrow-down"></i> <?php echo $lang['deposit']; ?>
                        </a>
                        <a href="index.php?controller=transaccion&action=retirar&idCuenta=<?php echo $cuenta->idCuenta; ?>" class="btn btn-danger">
                            <i class="icon-arrow-up"></i> <?php echo $lang['withdrawal']; ?>
                        </a>
                        <a href="index.php?controller=cuenta&action=historial&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-info">
                            <i class="icon-list"></i> <?php echo $lang['transaction_history']; ?>
                        </a>
                    </div>
                </div>
                
                <!-- Tarjeta asociada o botón para crear una -->
                <div class="card card-inner">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5><?php echo $lang['associated_card']; ?></h5>
                            <?php if (count($tarjetas) == 0): ?>
                                <a href="index.php?controller=cuenta&action=crearTarjeta&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-sm btn-primary">
                                    <i class="icon-plus"></i> <?php echo $lang['request_card']; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (count($tarjetas) > 0): ?>
                            <?php foreach ($tarjetas as $tarjeta): ?>
                                <div class="credit-card" style="background: linear-gradient(45deg, #056f1f, #0a4d14); color: white; padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                                    <div class="card-number" style="font-size: 1.2rem; margin-bottom: 10px; letter-spacing: 2px;">
                                        <?php 
                                        $numero = $tarjeta['nroTarjeta'];
                                        // Mostrar sólo los últimos 4 dígitos, ocultar el resto
                                        echo '**** **** **** ' . substr($numero, -4);
                                        ?>
                                    </div>
                                    <div class="card-info d-flex justify-content-between">
                                        <div>
                                            <div class="card-holder" style="font-size: 0.8rem; text-transform: uppercase;"><?php echo $lang['cardholder']; ?></div>
                                            <div><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno); ?></div>
                                        </div>
                                        <div>
                                            <div class="expiry" style="font-size: 0.8rem; text-transform: uppercase;"><?php echo $lang['expiration_date']; ?></div>
                                            <div><?php echo htmlspecialchars($tarjeta['fechaExpiracion']); ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-status">
                                    <p>
                                        <?php echo $lang['status']; ?>: 
                                        <?php if ($tarjeta['estado'] == 1): ?>
                                            <span class="badge" style="background-color: #28a745;"><?php echo $lang['active']; ?></span>
                                        <?php else: ?>
                                            <span class="badge" style="background-color: #dc3545;"><?php echo $lang['inactive']; ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <?php echo $lang['no_cards_associated']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Últimas transacciones -->
<div class="card mt-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3><?php echo $lang['recent_transactions']; ?></h3>
            <a href="index.php?controller=cuenta&action=historial&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-info">
                <i class="icon-list"></i> <?php echo $lang['view_all']; ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($transacciones) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $lang['transaction_type']; ?></th>
                            <th><?php echo $lang['amount']; ?></th>
                            <th><?php echo $lang['date']; ?></th>
                            <th><?php echo $lang['time']; ?></th>
                            <th><?php echo $lang['description']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Mostrar solo las 10 últimas transacciones
                        $recentTransactions = array_slice($transacciones, 0, 10);
                        foreach ($recentTransactions as $transaccion): 
                        ?>
                            <tr>
                                <td><?php echo $transaccion['idTransaccion']; ?></td>
                                <td>
                                    <?php if ($transaccion['tipoTransaccion'] == 1): ?>
                                        <span class="badge" style="background-color: #dc3545;"><?php echo $lang['withdrawal']; ?></span>
                                    <?php else: ?>
                                        <span class="badge" style="background-color: #28a745;"><?php echo $lang['deposit']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                                    echo $moneda . number_format($transaccion['monto'], 2);
                                    ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($transaccion['fecha'])); ?></td>
                                <td><?php echo $transaccion['hora']; ?></td>
                                <td><?php echo htmlspecialchars($transaccion['descripcion']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <?php echo $lang['no_transactions']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>