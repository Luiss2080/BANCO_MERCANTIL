<h1><?php echo $lang['reports']; ?> - <?php echo $lang['transactions_by_date']; ?></h1>

<div class="card">
    <div class="card-header"><?php echo $lang['search_criteria']; ?></div>
    <div class="card-body">
        <form method="POST" action="index.php?controller=reporte&action=transaccionesPorFecha">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fechaInicio"><?php echo $lang['start_date']; ?></label>
                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?php echo $fechaInicio; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fechaFin"><?php echo $lang['end_date']; ?></label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?php echo $fechaFin; ?>" required>
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

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
<div class="card">
    <div class="card-header">
        <?php echo $lang['results']; ?>
        <a href="index.php?controller=reporte&action=imprimirTransaccionesFecha&inicio=<?php echo $fechaInicio; ?>&fin=<?php echo $fechaFin; ?>" class="btn btn-sm btn-info float-right" target="_blank">
            <i class="icon-printer"></i> <?php echo $lang['print']; ?>
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-info">
                    <strong><?php echo $lang['total_transactions']; ?>:</strong> <?php echo $totalTransacciones; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success">
                    <strong><?php echo $lang['total_deposits']; ?>:</strong> <?php echo number_format($totalDepositos, 2); ?>
                </div>
            </div>
            <div class="col-md-4">
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
                        <th><?php echo $lang['client_details']; ?></th>
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
                            <td><?php echo $transaccion['cliente_nombre']; ?></td>
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