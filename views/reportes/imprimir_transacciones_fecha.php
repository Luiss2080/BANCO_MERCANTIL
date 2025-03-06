<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['app_name']; ?> - <?php echo $lang['reports']; ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-logo {
            font-size: 24px;
            font-weight: bold;
            color: #056f1f;
        }
        .report-title {
            font-size: 18px;
            margin: 10px 0;
        }
        .report-info {
            margin-bottom: 20px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .print-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="report-logo"><?php echo $lang['app_name']; ?></div>
        <div class="report-title"><?php echo $lang['transactions_by_date']; ?></div>
        <div><?php echo date('d/m/Y H:i:s'); ?></div>
    </div>
    
    <div class="report-info">
        <p><strong><?php echo $lang['period']; ?>:</strong> <?php echo date('d/m/Y', strtotime($fechaInicio)); ?> - <?php echo date('d/m/Y', strtotime($fechaFin)); ?></p>
    </div>
    
    <div class="summary">
        <div class="summary-box">
            <strong><?php echo $lang['total_transactions']; ?>:</strong> <?php echo $totalTransacciones; ?>
        </div>
        <div class="summary-box">
            <strong><?php echo $lang['total_deposits']; ?>:</strong> <?php echo number_format($totalDepositos, 2); ?>
        </div>
        <div class="summary-box">
            <strong><?php echo $lang['total_withdrawals']; ?>:</strong> <?php echo number_format($totalRetiros, 2); ?>
        </div>
    </div>
    
    <?php if (count($transacciones) > 0): ?>
        <table>
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
                            <?php echo $transaccion['tipoTransaccion'] == 1 ? $lang['withdrawal'] : $lang['deposit']; ?>
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
        <p><?php echo $lang['no_transactions_found']; ?></p>
    <?php endif; ?>
    
    <div class="print-footer">
        <?php echo $lang['app_name']; ?> &copy; <?php echo date('Y'); ?>
    </div>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary"><?php echo $lang['print']; ?></button>
        <button onclick="window.close()" class="btn btn-secondary"><?php echo $lang['close']; ?></button>
    </div>
</body>
</html>