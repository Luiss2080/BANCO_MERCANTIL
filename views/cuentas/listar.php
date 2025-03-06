<h1><?php echo $lang['account_list']; ?></h1>

<div class="card">
    <!-- ---------- ENCABEZADO DE LA TARJETA ---------- -->
    <div class="card-header">
        <div class="row">
            <!-- ---------- FORMULARIO DE BÚSQUEDA ---------- -->
            <div class="col-md-6">
                <form method="GET" action="index.php" class="search-form">
                    <input type="hidden" name="controller" value="cuenta">
                    <input type="hidden" name="action" value="listar">
                    <div class="input-group">
                        <input type="text" class="form-control" name="busqueda" placeholder="<?php echo $lang['search']; ?>..." value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                        <button type="submit" class="btn btn-primary"><?php echo $lang['search']; ?></button>
                    </div>
                </form>
            </div>
            <!-- ---------- BOTÓN PARA CREAR NUEVA CUENTA ---------- -->
            <div class="col-md-6 text-right">
                <a href="index.php?controller=cuenta&action=crear" class="btn btn-success">
                    <i class="icon-plus"></i> <?php echo $lang['new_account']; ?>
                </a>
            </div>
        </div>
    </div>

    <!-- ---------- CUERPO DE LA TARJETA ---------- -->
    <div class="card-body">
        <?php if (count($cuentas) > 0): ?>
            <!-- ---------- TABLA DE CUENTAS ---------- -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $lang['account_number']; ?></th>
                            <th><?php echo $lang['client_details']; ?></th>
                            <th><?php echo $lang['account_type']; ?></th>
                            <th><?php echo $lang['currency']; ?></th>
                            <th><?php echo $lang['balance']; ?></th>
                            <th><?php echo $lang['opening_date']; ?></th>
                            <th><?php echo $lang['status']; ?></th>
                            <th><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cuentas as $cuenta): ?>
                            <tr>
                                <!-- ID de la cuenta -->
                                <td><?php echo $cuenta['idCuenta']; ?></td>
                                <!-- Número de cuenta -->
                                <td><?php echo htmlspecialchars($cuenta['nroCuenta']); ?></td>
                                <!-- Nombre del cliente asociado -->
                                <td><?php echo htmlspecialchars($cuenta['cliente_nombre']); ?></td>
                                <!-- Tipo de cuenta (Ahorro o Corriente) -->
                                <td>
                                    <?php
                                    if ($cuenta['tipoCuenta'] == 1) {
                                        echo $lang['savings_account'];
                                    } else {
                                        echo $lang['checking_account'];
                                    }
                                    ?>
                                </td>
                                <!-- Tipo de moneda (Bolivianos o Dólares) -->
                                <td>
                                    <?php
                                    if ($cuenta['tipoMoneda'] == 1) {
                                        echo $lang['bolivianos'];
                                    } else {
                                        echo $lang['dollars'];
                                    }
                                    ?>
                                </td>
                                <!-- Saldo de la cuenta -->
                                <td>
                                    <?php 
                                    $moneda = ($cuenta['tipoMoneda'] == 1) ? 'Bs. ' : '$ ';
                                    echo $moneda . number_format($cuenta['saldo'], 2); 
                                    ?>
                                </td>
                                <!-- Fecha de apertura de la cuenta -->
                                <td><?php echo date('d/m/Y', strtotime($cuenta['fechaApertura'])); ?></td>
                                <!-- Estado de la cuenta (Activo o Inactivo) -->
                                <td>
                                    <?php if ($cuenta['estado'] == 1): ?>
                                        <span class="badge" style="background-color: #28a745;"><?php echo $lang['active']; ?></span>
                                    <?php else: ?>
                                        <span class="badge" style="background-color: #dc3545;"><?php echo $lang['inactive']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <!-- Acciones disponibles para la cuenta -->
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Botón para ver detalles de la cuenta -->
                                        <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-info" title="<?php echo $lang['view']; ?>">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <!-- Botón para editar la cuenta -->
                                        <a href="index.php?controller=cuenta&action=editar&id=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-warning" title="<?php echo $lang['edit']; ?>">
                                            <i class="icon-edit"></i>
                                        </a>
                                        <!-- Botón para realizar un depósito -->
                                        <a href="index.php?controller=transaccion&action=depositar&idCuenta=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-success" title="<?php echo $lang['deposit']; ?>">
                                            <i class="icon-arrow-down"></i>
                                        </a>
                                        <!-- Botón para realizar un retiro -->
                                        <a href="index.php?controller=transaccion&action=retirar&idCuenta=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-danger" title="<?php echo $lang['withdrawal']; ?>">
                                            <i class="icon-arrow-up"></i>
                                        </a>
                                        <!-- Botón para ver el historial de transacciones -->
                                        <a href="index.php?controller=cuenta&action=historial&id=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-primary" title="<?php echo $lang['transaction_history']; ?>">
                                            <i class="icon-list"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensaje si no hay cuentas registradas -->
            <div class="alert alert-info">
                <?php echo $lang['no_accounts']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>