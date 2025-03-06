<h1><?php echo $lang['transaction_history']; ?></h1>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <h3><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno . ' ' . $cliente->apellidoMaterno); ?></h3>
                <h4><?php echo $lang['account_number']; ?>: <?php echo htmlspecialchars($cuenta->nroCuenta); ?></h4>
            </div>
            <div class="col-md-4 text-right">
                <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-secondary">
                    <i class="icon-arrow-left"></i> <?php echo $lang['back_to_account']; ?>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['mensaje']; 
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['tipo_mensaje']);
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <div class="row mb-4">
            <!-- Resumen de la cuenta -->
            <div class="col-md-4">
                <div class="card card-inner">
                    <div class="card-header bg-info text-white">
                        <h5><?php echo $lang['account_summary']; ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
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
                                <th><?php echo $lang['current_balance']; ?>:</th>
                                <td>
                                    <strong style="color: #056f1f; font-size: 1.2rem;">
                                        <?php
                                        $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                                        echo $moneda . number_format($cuenta->saldo, 2);
                                        ?>
                                    </strong>
                                </td>
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
                </div>
            </div>
            
            <!-- Filtro de transacciones -->
            <div class="col-md-8">
                <div class="card card-inner">
                    <div class="card-header bg-secondary text-white">
                        <h5><?php echo $lang['filter_transactions']; ?></h5>
                    </div>
                    <div class="card-body">
                        <form action="index.php" method="GET">
                            <input type="hidden" name="controller" value="cuenta">
                            <input type="hidden" name="action" value="historial">
                            <input type="hidden" name="id" value="<?php echo $cuenta->idCuenta; ?>">
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fechaInicio"><?php echo $lang['start_date']; ?>:</label>
                                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" 
                                               value="<?php echo isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : date('Y-m-d', strtotime('-30 days')); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fechaFin"><?php echo $lang['end_date']; ?>:</label>
                                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" 
                                               value="<?php echo isset($_GET['fechaFin']) ? $_GET['fechaFin'] : date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tipoTransaccion"><?php echo $lang['transaction_type']; ?>:</label>
                                        <select class="form-control" id="tipoTransaccion" name="tipoTransaccion">
                                            <option value=""><?php echo $lang['all']; ?></option>
                                            <option value="1" <?php echo (isset($_GET['tipoTransaccion']) && $_GET['tipoTransaccion'] == '1') ? 'selected' : ''; ?>>
                                                <?php echo $lang['withdrawals']; ?>
                                            </option>
                                            <option value="2" <?php echo (isset($_GET['tipoTransaccion']) && $_GET['tipoTransaccion'] == '2') ? 'selected' : ''; ?>>
                                                <?php echo $lang['deposits']; ?>
                                            </option>
                                            <option value="3" <?php echo (isset($_GET['tipoTransaccion']) && $_GET['tipoTransaccion'] == '3') ? 'selected' : ''; ?>>
                                                <?php echo $lang['transfers_received']; ?>
                                            </option>
                                            <option value="4" <?php echo (isset($_GET['tipoTransaccion']) && $_GET['tipoTransaccion'] == '4') ? 'selected' : ''; ?>>
                                                <?php echo $lang['transfers_sent']; ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-filter"></i> <?php echo $lang['apply_filter']; ?>
                                </button>
                                <a href="index.php?controller=cuenta&action=historial&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-secondary">
                                    <i class="icon-refresh"></i> <?php echo $lang['reset_filter']; ?>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Listado de transacciones -->
        <div class="card card-inner mt-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo $lang['transactions']; ?></h5>
                <div>
                    <a href="index.php?controller=cuenta&action=exportarPDF&id=<?php echo $cuenta->idCuenta; ?><?php echo isset($_GET['fechaInicio']) ? '&fechaInicio=' . $_GET['fechaInicio'] : ''; ?><?php echo isset($_GET['fechaFin']) ? '&fechaFin=' . $_GET['fechaFin'] : ''; ?><?php echo isset($_GET['tipoTransaccion']) ? '&tipoTransaccion=' . $_GET['tipoTransaccion'] : ''; ?>" class="btn btn-sm btn-light">
                        <i class="icon-file-pdf"></i> <?php echo $lang['export_pdf']; ?>
                    </a>
                    <a href="index.php?controller=cuenta&action=exportarExcel&id=<?php echo $cuenta->idCuenta; ?><?php echo isset($_GET['fechaInicio']) ? '&fechaInicio=' . $_GET['fechaInicio'] : ''; ?><?php echo isset($_GET['fechaFin']) ? '&fechaFin=' . $_GET['fechaFin'] : ''; ?><?php echo isset($_GET['tipoTransaccion']) ? '&tipoTransaccion=' . $_GET['tipoTransaccion'] : ''; ?>" class="btn btn-sm btn-light">
                        <i class="icon-file-excel"></i> <?php echo $lang['export_excel']; ?>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($transacciones) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?php echo $lang['transaction_type']; ?></th>
                                    <th><?php echo $lang['amount']; ?></th>
                                    <th><?php echo $lang['date']; ?></th>
                                    <th><?php echo $lang['time']; ?></th>
                                    <th><?php echo $lang['description']; ?></th>
                                    <th><?php echo $lang['related_account']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transacciones as $transaccion): ?>
                                    <tr>
                                        <td><?php echo $transaccion['idTransaccion']; ?></td>
                                        <td>
                                            <?php if ($transaccion['tipoTransaccion'] == 1): ?>
                                                <span class="badge" style="background-color: #dc3545;"><?php echo $lang['withdrawal']; ?></span>
                                            <?php elseif ($transaccion['tipoTransaccion'] == 2): ?>
                                                <span class="badge" style="background-color: #28a745;"><?php echo $lang['deposit']; ?></span>
                                            <?php elseif ($transaccion['tipoTransaccion'] == 3): ?>
                                                <span class="badge" style="background-color: #17a2b8;"><?php echo $lang['transfer_received']; ?></span>
                                            <?php elseif ($transaccion['tipoTransaccion'] == 4): ?>
                                                <span class="badge" style="background-color: #ffc107; color: #000;"><?php echo $lang['transfer_sent']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                                            if ($transaccion['tipoTransaccion'] == 1 || $transaccion['tipoTransaccion'] == 4) {
                                                echo '<span style="color: #dc3545;">- ' . $moneda . number_format($transaccion['monto'], 2) . '</span>';
                                            } else {
                                                echo '<span style="color: #28a745;">+ ' . $moneda . number_format($transaccion['monto'], 2) . '</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($transaccion['fecha'])); ?></td>
                                        <td><?php echo $transaccion['hora']; ?></td>
                                        <td><?php echo htmlspecialchars($transaccion['descripcion']); ?></td>
                                        <td>
                                            <?php 
                                            if ($transaccion['tipoTransaccion'] == 3 && isset($transaccion['cuentaOrigen'])) {
                                                echo $lang['from'] . ': ' . htmlspecialchars($transaccion['cuentaOrigen']);
                                            } elseif ($transaccion['tipoTransaccion'] == 4 && isset($transaccion['cuentaDestino'])) {
                                                echo $lang['to'] . ': ' . htmlspecialchars($transaccion['cuentaDestino']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo $lang['no_transactions_found']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>