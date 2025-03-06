<h1><?php echo $lang['deposit_funds']; ?></h1>

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
                        <th><?php echo $lang['current_balance']; ?>:</th>
                        <td>
                            <strong>
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
            
            <!-- Formulario de depósito -->
            <div class="col-md-6">
                <h4><?php echo $lang['deposit_details']; ?></h4>
                <form action="index.php?controller=transaccion&action=procesarDeposito" method="POST">
                    <input type="hidden" name="idCuenta" value="<?php echo $cuenta->idCuenta; ?>">
                    
                    <div class="form-group">
                        <label for="monto"><?php echo $lang['amount_to_deposit']; ?>:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?php echo ($cuenta->tipoMoneda == 1) ? 'Bs.' : '$'; ?></span>
                            </div>
                            <input type="number" class="form-control" id="monto" name="monto" min="0.01" step="0.01" required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion"><?php echo $lang['description']; ?> (<?php echo $lang['optional']; ?>):</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="<?php echo $lang['deposit_description_placeholder']; ?>">
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="icon-arrow-down"></i> <?php echo $lang['make_deposit']; ?>
                        </button>
                        <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-secondary btn-lg">
                            <?php echo $lang['cancel']; ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>