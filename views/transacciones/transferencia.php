<h1><?php echo $lang['transfer_funds']; ?></h1>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <h3><?php echo $lang['account_origin']; ?></h3>
                <h4><?php echo $lang['account_number']; ?>: <?php echo htmlspecialchars($cuentaOrigen['nroCuenta']); ?></h4>
            </div>
            <div class="col-md-4 text-right">
                <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuentaOrigen['idCuenta']; ?>" class="btn btn-secondary">
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
        
        <div class="row">
            <!-- Información de la cuenta de origen -->
            <div class="col-md-6">
                <div class="card card-inner">
                    <div class="card-header bg-info text-white">
                        <h5><?php echo $lang['source_account']; ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th><?php echo $lang['account_holder']; ?>:</th>
                                <td><?php echo htmlspecialchars($cuentaOrigen['cliente_nombre']); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang['account_number']; ?>:</th>
                                <td><?php echo htmlspecialchars($cuentaOrigen['nroCuenta']); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo $lang['account_type']; ?>:</th>
                                <td>
                                    <?php
                                    if ($cuentaOrigen['tipoCuenta'] == 1) {
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
                                    if ($cuentaOrigen['tipoMoneda'] == 1) {
                                        echo $lang['bolivianos'];
                                    } else {
                                        echo $lang['dollars'];
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo $lang['available_balance']; ?>:</th>
                                <td>
                                    <strong style="color: #056f1f; font-size: 1.2rem;">
                                        <?php
                                        $moneda = ($cuentaOrigen['tipoMoneda'] == 1) ? 'Bs. ' : '$ ';
                                        echo $moneda . number_format($cuentaOrigen['saldo'], 2);
                                        ?>
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Cuenta de destino o formulario de búsqueda -->
            <div class="col-md-6">
                <?php if (!isset($cuentaDestino)): ?>
                    <div class="card card-inner">
                        <div class="card-header bg-warning">
                            <h5><?php echo $lang['search_destination_account']; ?></h5>
                        </div>
                        <div class="card-body">
                            <form action="index.php?controller=transaccion&action=buscarCuentaDestino" method="POST">
                                <input type="hidden" name="idCuentaOrigen" value="<?php echo $cuentaOrigen['idCuenta']; ?>">
                                
                                <div class="form-group">
                                    <label for="nroCuentaDestino"><?php echo $lang['destination_account_number']; ?>:</label>
                                    <input type="text" class="form-control" id="nroCuentaDestino" name="nroCuentaDestino" required autofocus>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="icon-search"></i> <?php echo $lang['search_account']; ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card card-inner">
                        <div class="card-header bg-success text-white">
                            <h5><?php echo $lang['destination_account']; ?></h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th><?php echo $lang['account_holder']; ?>:</th>
                                    <td><?php echo htmlspecialchars($cuentaDestino['cliente_nombre']); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang['account_number']; ?>:</th>
                                    <td><?php echo htmlspecialchars($cuentaDestino['nroCuenta']); ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo $lang['account_type']; ?>:</th>
                                    <td>
                                        <?php
                                        if ($cuentaDestino['tipoCuenta'] == 1) {
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
                                        if ($cuentaDestino['tipoMoneda'] == 1) {
                                            echo $lang['bolivianos'];
                                        } else {
                                            echo $lang['dollars'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($cuentaDestino)): ?>
            <!-- Formulario de transferencia -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card card-inner">
                        <div class="card-header bg-primary text-white">
                            <h5><?php echo $lang['transfer_details']; ?></h5>
                        </div>
                        <div class="card-body">
                            <form action="index.php?controller=transaccion&action=procesarTransferencia" method="POST">
                                <input type="hidden" name="idCuentaOrigen" value="<?php echo $cuentaOrigen['idCuenta']; ?>">
                                <input type="hidden" name="idCuentaDestino" value="<?php echo $cuentaDestino['idCuenta']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="monto"><?php echo $lang['amount_to_transfer']; ?>:</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><?php echo ($cuentaOrigen['tipoMoneda'] == 1) ? 'Bs.' : '$'; ?></span>
                                                </div>
                                                <input type="number" class="form-control" id="monto" name="monto" min="0.01" max="<?php echo $cuentaOrigen['saldo']; ?>" step="0.01" required>
                                            </div>
                                            <small class="form-text text-muted">
                                                <?php echo $lang['max_transfer_amount']; ?>: <?php echo $moneda . number_format($cuentaOrigen['saldo'], 2); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="descripcion"><?php echo $lang['description']; ?> (<?php echo $lang['optional']; ?>):</label>
                                            <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="<?php echo $lang['transfer_description_placeholder']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="icon-exchange"></i> <?php echo $lang['make_transfer']; ?>
                                    </button>
                                    <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuentaOrigen['idCuenta']; ?>" class="btn btn-secondary btn-lg">
                                        <?php echo $lang['cancel']; ?>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>