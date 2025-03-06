<h1><?php echo $lang['transaction_history']; ?></h1> <!-- Título de la página: "Historial de transacciones" (traducido según el idioma seleccionado) -->

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>
                <?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno . ' ' . $cliente->apellidoMaterno); ?> - 
                <?php echo $lang['account_number']; ?>: <?php echo htmlspecialchars($cuenta->nroCuenta); ?> <!-- Nombre del cliente y número de cuenta -->
            </h3>
            <div>
                <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-secondary">
                    <i class="icon-arrow-left"></i> <?php echo $lang['back']; ?> <!-- Botón para volver a la vista de la cuenta -->
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Formulario para filtrar transacciones por fechas -->
        <form method="POST" action="index.php?controller=cuenta&action=historial&id=<?php echo $cuenta->idCuenta; ?>" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fechaInicio"><?php echo $lang['start_date']; ?></label>
                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?php echo $fechaInicio; ?>" required> <!-- Campo para la fecha de inicio -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fechaFin"><?php echo $lang['end_date']; ?></label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?php echo $fechaFin; ?>" required> <!-- Campo para la fecha de fin -->
                    </div>
                </div>
                <div class="col-md-4" style="display: flex; align-items: flex-end;">
                    <div class="form-group" style="width: 100%;">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-search"></i> <?php echo $lang['search']; ?> <!-- Botón para buscar transacciones -->
                        </button>
                        <a href="index.php?controller=cuenta&action=imprimirHistorial&id=<?php echo $cuenta->idCuenta; ?>&inicio=<?php echo $fechaInicio; ?>&fin=<?php echo $fechaFin; ?>" target="_blank" class="btn btn-info">
                            <i class="icon-printer"></i> <?php echo $lang['print']; ?> <!-- Botón para imprimir el historial -->
                        </a>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Resumen de transacciones -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h4><?php echo $lang['current_balance']; ?></h4> <!-- Título: "Saldo actual" -->
                        <h2 style="color: #056f1f;">
                            <?php
                            $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ '; // Símbolo de la moneda
                            echo $moneda . number_format($cuenta->saldo, 2); // Saldo formateado
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h4><?php echo $lang['transactions_for_period']; ?></h4> <!-- Título: "Transacciones en el período" -->
                        <h2><?php echo count($transacciones); ?></h2> <!-- Número de transacciones -->
                        <p>
                            <?php echo $fechaInicio; ?> - <?php echo $fechaFin; ?> <!-- Rango de fechas seleccionado -->
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de transacciones -->
        <?php if (count($transacciones) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th> <!-- Columna: ID de la transacción -->
                            <th><?php echo $lang['transaction_type']; ?></th> <!-- Columna: Tipo de transacción -->
                            <th><?php echo $lang['amount']; ?></th> <!-- Columna: Monto -->
                            <th><?php echo $lang['date']; ?></th> <!-- Columna: Fecha -->
                            <th><?php echo $lang['time']; ?></th> <!-- Columna: Hora -->
                            <th><?php echo $lang['description']; ?></th> <!-- Columna: Descripción -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Calcular totales de depósitos y retiros
                        $totalDepositos = 0;
                        $totalRetiros = 0;
                        
                        foreach ($transacciones as $transaccion): 
                            if ($transaccion['tipoTransaccion'] == 1) { // Retiro
                                $totalRetiros += $transaccion['monto'];
                            } else { // Depósito
                                $totalDepositos += $transaccion['monto'];
                            }
                        ?>
                            <tr>
                                <td><?php echo $transaccion['idTransaccion']; ?></td> <!-- ID de la transacción -->
                                <td>
                                    <?php if ($transaccion['tipoTransaccion'] == 1): ?>
                                        <span class="badge" style="background-color: #dc3545;"><?php echo $lang['withdrawal']; ?></span> <!-- Badge para retiros -->
                                    <?php else: ?>
                                        <span class="badge" style="background-color: #28a745;"><?php echo $lang['deposit']; ?></span> <!-- Badge para depósitos -->
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ '; // Símbolo de la moneda
                                    echo $moneda . number_format($transaccion['monto'], 2); // Monto formateado
                                    ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($transaccion['fecha'])); ?></td> <!-- Fecha formateada -->
                                <td><?php echo $transaccion['hora']; ?></td> <!-- Hora de la transacción -->
                                <td><?php echo htmlspecialchars($transaccion['descripcion']); ?></td> <!-- Descripción de la transacción -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2"><?php echo $lang['totals']; ?></th> <!-- Título: "Totales" -->
                            <th>
                                <span style="color: #28a745;">
                                    <?php 
                                    $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                                    echo $moneda . number_format($totalDepositos, 2); // Total de depósitos
                                    ?>
                                </span>
                                <br>
                                <span style="color: #dc3545;">
                                    <?php 
                                    echo $moneda . number_format($totalRetiros, 2); // Total de retiros
                                    ?>
                                </span>
                            </th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensaje si no hay transacciones -->
            <div class="alert alert-info">
                <?php echo $lang['no_transactions_found']; ?> <!-- Mensaje: "No se encontraron transacciones" -->
            </div>
        <?php endif; ?>
    </div>
</div>