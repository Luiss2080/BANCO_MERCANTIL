<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['app_name']; ?> - <?php echo $lang['transaction_history']; ?></title>
    <style>
        /* ---------- ESTILOS GENERALES ---------- */
        body {
            font-family: Arial, sans-serif; /* Fuente principal del documento */
            margin: 20px; /* Margen exterior del cuerpo */
            color: #333; /* Color del texto principal */
        }

        /* ---------- ESTILOS DEL ENCABEZADO ---------- */
        .header {
            text-align: center; /* Centra el contenido del encabezado */
            margin-bottom: 30px; /* Margen inferior para separar del siguiente bloque */
            border-bottom: 1px solid #ddd; /* Línea divisoria inferior */
            padding-bottom: 10px; /* Espaciado inferior */
        }
        .logo {
            font-size: 24px; /* Tamaño de fuente del logo */
            font-weight: bold; /* Texto en negrita */
            color: #056f1f; /* Color del logo */
            margin-bottom: 5px; /* Margen inferior */
        }
        .bank-info {
            font-size: 14px; /* Tamaño de fuente de la información del banco */
            margin-bottom: 10px; /* Margen inferior */
        }
        .report-title {
            font-size: 18px; /* Tamaño de fuente del título del reporte */
            margin-bottom: 5px; /* Margen inferior */
        }
        .report-date {
            font-size: 12px; /* Tamaño de fuente de la fecha del reporte */
            color: #666; /* Color del texto de la fecha */
        }

        /* ---------- ESTILOS DE LA INFORMACIÓN DE LA CUENTA ---------- */
        .account-info {
            margin-bottom: 20px; /* Margen inferior */
        }
        .account-info table {
            width: 100%; /* Ancho completo de la tabla */
            border-collapse: collapse; /* Elimina el espacio entre celdas */
        }
        .account-info th, .account-info td {
            padding: 8px; /* Espaciado interno de las celdas */
            text-align: left; /* Alineación del texto a la izquierda */
            border-bottom: 1px solid #ddd; /* Línea divisoria inferior */
        }

        /* ---------- ESTILOS DE LA TABLA DE TRANSACCIONES ---------- */
        .transaction-table {
            width: 100%; /* Ancho completo de la tabla */
            border-collapse: collapse; /* Elimina el espacio entre celdas */
            margin-bottom: 20px; /* Margen inferior */
        }
        .transaction-table th {
            background-color: #f2f2f2; /* Color de fondo del encabezado */
            font-weight: bold; /* Texto en negrita */
        }
        .transaction-table th, .transaction-table td {
            border: 1px solid #ddd; /* Borde de las celdas */
            padding: 8px; /* Espaciado interno de las celdas */
            text-align: left; /* Alineación del texto a la izquierda */
        }
        .transaction-table tr:nth-child(even) {
            background-color: #f9f9f9; /* Color de fondo alterno para filas */
        }
        .transaction-type {
            font-weight: bold; /* Texto en negrita para el tipo de transacción */
        }
        .deposit {
            color: #28a745; /* Color verde para depósitos */
        }
        .withdrawal {
            color: #dc3545; /* Color rojo para retiros */
        }

        /* ---------- ESTILOS DEL RESUMEN DE TRANSACCIONES ---------- */
        .summary {
            margin-top: 20px; /* Margen superior */
            border-top: 1px solid #ddd; /* Línea divisoria superior */
            padding-top: 10px; /* Espaciado superior */
        }
        .summary-table {
            width: 60%; /* Ancho de la tabla de resumen */
            margin: 0 auto; /* Centra la tabla horizontalmente */
        }
        .summary-table th, .summary-table td {
            padding: 8px; /* Espaciado interno de las celdas */
            text-align: left; /* Alineación del texto a la izquierda */
        }
        .summary-table .amount {
            text-align: right; /* Alineación del texto a la derecha */
            font-weight: bold; /* Texto en negrita */
        }

        /* ---------- ESTILOS DEL PIE DE PÁGINA ---------- */
        .footer {
            margin-top: 30px; /* Margen superior */
            text-align: center; /* Centra el contenido */
            font-size: 12px; /* Tamaño de fuente */
            color: #666; /* Color del texto */
            border-top: 1px solid #ddd; /* Línea divisoria superior */
            padding-top: 10px; /* Espaciado superior */
        }

        /* ---------- ESTILOS DE LOS BOTONES DE IMPRESIÓN ---------- */
        .print-buttons {
            text-align: center; /* Centra los botones */
            margin-top: 20px; /* Margen superior */
        }
        .btn {
            display: inline-block; /* Muestra los botones en línea */
            padding: 8px 16px; /* Espaciado interno */
            background-color: #056f1f; /* Color de fondo */
            color: white; /* Color del texto */
            text-decoration: none; /* Sin subrayado */
            border-radius: 4px; /* Bordes redondeados */
            font-size: 14px; /* Tamaño de fuente */
            margin: 0 5px; /* Margen entre botones */
            cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
            border: none; /* Sin borde */
        }
        .btn-secondary {
            background-color: rgb(17, 7, 135); /* Color de fondo alternativo */
        }

        /* ---------- ESTILOS PARA IMPRESIÓN ---------- */
        @media print {
            .print-buttons {
                display: none; /* Oculta los botones al imprimir */
            }
        }
    </style>
</head>
<body>
    <!-- ---------- ENCABEZADO DEL REPORTE ---------- -->
    <div class="header">
        <div class="logo"><?php echo $lang['app_name']; ?></div>
        <div class="bank-info"><?php echo $lang['bank_address']; ?></div>
        <div class="report-title"><?php echo $lang['transaction_history']; ?></div>
        <div class="report-date"><?php echo $lang['generated_on']; ?>: <?php echo date('d/m/Y H:i:s'); ?></div>
    </div>
    
    <!-- ---------- INFORMACIÓN DE LA CUENTA ---------- -->
    <div class="account-info">
        <table>
            <tr>
                <th><?php echo $lang['client']; ?>:</th>
                <td><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno . ' ' . $cliente->apellidoMaterno); ?></td>
                <th><?php echo $lang['id_number']; ?>:</th>
                <td><?php echo htmlspecialchars($cliente->ci); ?></td>
            </tr>
            <tr>
                <th><?php echo $lang['account_number']; ?>:</th>
                <td><?php echo htmlspecialchars($cuenta->nroCuenta); ?></td>
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
                <th><?php echo $lang['current_balance']; ?>:</th>
                <td>
                    <?php
                    $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                    echo $moneda . number_format($cuenta->saldo, 2);
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php echo $lang['period']; ?>:</th>
                <td colspan="3"><?php echo date('d/m/Y', strtotime($fechaInicio)); ?> - <?php echo date('d/m/Y', strtotime($fechaFin)); ?></td>
            </tr>
        </table>
    </div>
    
    <!-- ---------- TABLA DE TRANSACCIONES ---------- -->
    <?php if (count($transacciones) > 0): ?>
        <table class="transaction-table">
            <thead>
                <tr>
                    <th><?php echo $lang['date']; ?></th>
                    <th><?php echo $lang['time']; ?></th>
                    <th><?php echo $lang['transaction_type']; ?></th>
                    <th><?php echo $lang['description']; ?></th>
                    <th><?php echo $lang['amount']; ?></th>
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
                        <td><?php echo date('d/m/Y', strtotime($transaccion['fecha'])); ?></td>
                        <td><?php echo $transaccion['hora']; ?></td>
                        <td class="transaction-type <?php echo ($transaccion['tipoTransaccion'] == 1) ? 'withdrawal' : 'deposit'; ?>">
                            <?php
                            if ($transaccion['tipoTransaccion'] == 1) {
                                echo $lang['withdrawal'];
                            } else {
                                echo $lang['deposit'];
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($transaccion['descripcion']); ?></td>
                        <td style="text-align: right;">
                            <?php
                            $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                            echo $moneda . number_format($transaccion['monto'], 2);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- ---------- RESUMEN DE TRANSACCIONES ---------- -->
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <th><?php echo $lang['total_transactions']; ?>:</th>
                    <td class="amount"><?php echo count($transacciones); ?></td>
                </tr>
                <tr>
                    <th><?php echo $lang['total_deposits']; ?>:</th>
                    <td class="amount" style="color: #28a745;">
                        <?php
                        $moneda = ($cuenta->tipoMoneda == 1) ? 'Bs. ' : '$ ';
                        echo $moneda . number_format($totalDepositos, 2);
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang['total_withdrawals']; ?>:</th>
                    <td class="amount" style="color: #dc3545;">
                        <?php
                        echo $moneda . number_format($totalRetiros, 2);
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $lang['net_movement']; ?>:</th>
                    <td class="amount" style="color: <?php echo ($totalDepositos - $totalRetiros >= 0) ? '#28a745' : '#dc3545'; ?>">
                        <?php
                        echo $moneda . number_format($totalDepositos - $totalRetiros, 2);
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    <?php else: ?>
        <!-- Mensaje si no hay transacciones -->
        <div style="text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 4px;">
            <p><?php echo $lang['no_transactions_found']; ?></p>
        </div>
    <?php endif; ?>
    
    <!-- ---------- PIE DE PÁGINA ---------- -->
    <div class="footer">
        <p><?php echo $lang['app_name']; ?> &copy; <?php echo date('Y'); ?> - <?php echo $lang['transaction_report_footer']; ?></p>
    </div>
    
    <!-- ---------- BOTONES DE IMPRESIÓN Y CIERRE ---------- -->
    <div class="print-buttons">
        <button onclick="window.print();" class="btn"><?php echo $lang['print']; ?></button>
        <button onclick="window.close();" class="btn btn-secondary"><?php echo $lang['close']; ?></button>
    </div>
</body>
</html>