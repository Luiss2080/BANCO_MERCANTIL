<h1><?php echo $lang['reports']; ?> - <?php echo $lang['transactions_by_client']; ?></h1>

<div class="card">
    <div class="card-header"><?php echo $lang['search_criteria']; ?></div>
    <div class="card-body">
        <form method="POST" action="index.php?controller=reporte&action=transaccionesPorCliente">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="idCliente"><?php echo $lang['select_client']; ?></label>
                        <select class="form-control" id="idCliente" name="idCliente" required>
                            <option value=""><?php echo $lang['select_option']; ?></option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['idPersona']; ?>" <?php echo (isset($_POST['idCliente']) && $_POST['idCliente'] == $cliente['idPersona']) ? 'selected' : ''; ?>>
                                    <?php echo $cliente['nombre'] . ' ' . $cliente['apellidoPaterno'] . ' ' . $cliente['apellidoMaterno']; ?> (<?php echo $cliente['ci']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-primary"><?php echo $lang['generate_report']; ?></button>
                        <a href="index.php?controller=reporte&action=index" class="btn btn-secondary"><?php echo $lang['back']; ?></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $clienteSeleccionado): ?>
<div class="card">
    <div class="card-header">
        <?php echo $lang['results']; ?>: <?php echo $clienteSeleccionado->nombre . ' ' . $clienteSeleccionado->apellidoPaterno . ' ' . $clienteSeleccionado->apellidoMaterno; ?>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-success">
                    <strong><?php echo $lang['total_deposits']; ?>:</strong> <?php echo number_format($totalDepositos, 2); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-danger">
                    <strong><?php echo $lang['total_withdrawals']; ?>:</strong> <?php echo number_format($totalRetiros, 2); ?>
                </div>
            </div>
        </div>
        
        <?php if (count($transacciones) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?php echo $lang['account_number']; ?></th>
                        <th><?php echo $lang['transaction_type']; ?></th>
                        <th><?php echo $lang['amount']; ?></th>
                        <th><?php echo $lang['date']; ?></th>
                        <th><?php echo $lang['time']; ?></th>
                        <th><?php echo $lang['description']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacciones as $transaccion): ?>
                        <tr>
                            <td><?php echo $transaccion['idTransaccion']; ?></td>
                            <td><?php echo $transaccion['nroCuenta']; ?></td>
                            <td>
                                <?php if ($transaccion['tipoTransaccion'] == 1): ?>
                                    <span class="badge" style="background-color: #dc3545;"><?php echo $lang['withdrawal']; ?></span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: #28a745;"><?php echo $lang['deposit']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($transaccion['monto'], 2); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($transaccion['fecha'])); ?></td>
                            <td><?php echo $transaccion['hora']; ?></td>
                            <td><?php echo $transaccion['descripcion']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">
                <?php echo $lang['no_transactions_found']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>