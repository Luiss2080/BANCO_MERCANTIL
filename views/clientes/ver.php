<!-- Título de la página -->
<h1><?php echo $lang['client_details']; ?></h1>

<!-- Tarjeta que contiene los detalles del cliente -->
<div class="card">
    <!-- Encabezado de la tarjeta -->
    <div class="card-header">
        <div class="row">
            <!-- Nombre completo del cliente -->
            <div class="col-md-8">
                <h3><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno . ' ' . $cliente->apellidoMaterno); ?></h3>
            </div>
            <!-- Botones de acción -->
            <div class="col-md-4 text-right">
                <!-- Botón para editar el cliente -->
                <a href="index.php?controller=cliente&action=editar&id=<?php echo $cliente->idPersona; ?>" class="btn btn-warning">
                    <i class="icon-edit"></i> <?php echo $lang['edit']; ?>
                </a>
                <!-- Botón para eliminar el cliente (solo visible si no tiene cuentas asociadas) -->
                <?php if (count($cuentas) == 0): ?>
                    <a href="index.php?controller=cliente&action=eliminar&id=<?php echo $cliente->idPersona; ?>" class="btn btn-danger delete-button" onclick="return confirm('<?php echo $lang['confirm_delete']; ?>')">
                        <i class="icon-trash"></i> <?php echo $lang['delete']; ?>
                    </a>
                <?php endif; ?>
                <!-- Botón para volver a la lista de clientes -->
                <a href="index.php?controller=cliente&action=listar" class="btn btn-secondary">
                    <i class="icon-arrow-left"></i> <?php echo $lang['back']; ?>
                </a>
            </div>
        </div>
    </div>
    <!-- Cuerpo de la tarjeta -->
    <div class="card-body">
        <div class="row">
            <!-- Información personal del cliente -->
            <div class="col-md-6">
                <h4><?php echo $lang['personal_information']; ?></h4>
                <table class="table table-borderless">
                    <tr>
                        <th><?php echo $lang['id_number']; ?>:</th>
                        <td><?php echo htmlspecialchars($cliente->ci); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['name']; ?>:</th>
                        <td><?php echo htmlspecialchars($cliente->nombre); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['paternal_last_name']; ?>:</th>
                        <td><?php echo htmlspecialchars($cliente->apellidoPaterno); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['maternal_last_name']; ?>:</th>
                        <td><?php echo htmlspecialchars($cliente->apellidoMaterno); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['birth_date']; ?>:</th>
                        <td><?php echo $cliente->fechaNacimiento ? date('d/m/Y', strtotime($cliente->fechaNacimiento)) : '-'; ?></td>
                    </tr>
                </table>
            </div>
            <!-- Información de contacto del cliente -->
            <div class="col-md-6">
                <h4><?php echo $lang['contact_information']; ?></h4>
                <table class="table table-borderless">
                    <tr>
                        <th><?php echo $lang['address']; ?>:</th>
                        <td><?php echo htmlspecialchars($cliente->direccion); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['phone']; ?>:</th>
                        <td><?php echo htmlspecialchars($cliente->telefono); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['email']; ?>:</th>
                        <td><?php echo htmlspecialchars($cliente->email); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo $lang['branches']; ?>:</th>
                        <td><?php 
                            // Obtener el nombre de la oficina asociada al cliente
                            foreach ($oficinas as $oficina) {
                                if ($oficina['idOficina'] == $cliente->idOficina) {
                                    echo htmlspecialchars($oficina['nombre']);
                                    if ($oficina['central']) {
                                        echo ' (' . $lang['central_office'] . ')';
                                    }
                                    break;
                                }
                            }
                        ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Sección de cuentas asociadas al cliente -->
<div class="card mt-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3><?php echo $lang['associated_accounts']; ?></h3>
            <!-- Botón para crear una nueva cuenta para el cliente -->
            <a href="index.php?controller=cuenta&action=crear&idCliente=<?php echo $cliente->idPersona; ?>" class="btn btn-success">
                <i class="icon-plus"></i> <?php echo $lang['new_account']; ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($cuentas) > 0): ?>
            <!-- Tabla que muestra las cuentas asociadas al cliente -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $lang['account_number']; ?></th>
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
                                <td><?php echo $cuenta['idCuenta']; ?></td>
                                <td><?php echo htmlspecialchars($cuenta['nroCuenta']); ?></td>
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
                                <td><?php echo number_format($cuenta['saldo'], 2); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($cuenta['fechaApertura'])); ?></td>
                                <td>
                                    <?php if ($cuenta['estado'] == 1): ?>
                                        <!-- Estado activo de la cuenta -->
                                        <span class="badge" style="background-color: #28a745;"><?php echo $lang['active']; ?></span>
                                    <?php else: ?>
                                        <!-- Estado inactivo de la cuenta -->
                                        <span class="badge" style="background-color: #dc3545;"><?php echo $lang['inactive']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Botones de acciones para cada cuenta -->
                                    <div class="btn-group" role="group">
                                        <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-info" title="<?php echo $lang['view']; ?>">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <a href="index.php?controller=cuenta&action=editar&id=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-warning" title="<?php echo $lang['edit']; ?>">
                                            <i class="icon-edit"></i>
                                        </a>
                                        <a href="index.php?controller=transaccion&action=depositar&idCuenta=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-success" title="<?php echo $lang['deposit']; ?>">
                                            <i class="icon-arrow-down"></i>
                                        </a>
                                        <a href="index.php?controller=transaccion&action=retirar&idCuenta=<?php echo $cuenta['idCuenta']; ?>" class="btn btn-sm btn-danger" title="<?php echo $lang['withdrawal']; ?>">
                                            <i class="icon-arrow-up"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensaje si no hay cuentas asociadas al cliente -->
            <div class="alert alert-info">
                <?php echo $lang['no_accounts']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>