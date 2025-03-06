<h1><?php echo $lang['reports']; ?> - <?php echo $lang['balances_by_branch']; ?></h1>

<div class="card">
    <div class="card-header"><?php echo $lang['balances_by_branch']; ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <canvas id="chartClientes" width="400" height="300"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="chartSaldos" width="400" height="300"></canvas>
            </div>
        </div>
        
        <div class="mt-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo $lang['office_name']; ?></th>
                        <th><?php echo $lang['total_clients']; ?></th>
                        <th><?php echo $lang['total_accounts']; ?></th>
                        <th><?php echo $lang['bolivianos']; ?></th>
                        <th><?php echo $lang['dollars']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($oficinas as $oficina): ?>
                        <tr>
                            <td>
                                <?php echo $oficina['oficina_nombre']; ?>
                                <?php if (isset($oficina['central']) && $oficina['central']): ?>
                                    <span class="badge" style="background-color: #17a2b8;"><?php echo $lang['central_office']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $oficina['total_clientes']; ?></td>
                            <td><?php echo $oficina['total_cuentas']; ?></td>
                            <td><?php echo number_format($oficina['saldo_bolivianos'], 2); ?> BOB</td>
                            <td><?php echo number_format($oficina['saldo_dolares'], 2); ?> USD</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?php echo $lang['totals']; ?></th>
                        <th><?php echo $totalClientes; ?></th>
                        <th><?php echo $totalCuentas; ?></th>
                        <th><?php echo number_format($totalBolivianos, 2); ?> BOB</th>
                        <th><?php echo number_format($totalDolares, 2); ?> USD</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="mt-3 text-center">
            <a href="index.php?controller=reporte&action=index" class="btn btn-secondary"><?php echo $lang['back']; ?></a>
        </div>
    </div>
</div>

<!-- Script para generar gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para los gráficos
        var oficinas = [
            <?php foreach ($oficinas as $oficina): ?>
                '<?php echo $oficina['oficina_nombre']; ?>',
            <?php endforeach; ?>
        ];
        
        var clientes = [
            <?php foreach ($oficinas as $oficina): ?>
                <?php echo $oficina['total_clientes']; ?>,
            <?php endforeach; ?>
        ];
        
        var cuentas = [
            <?php foreach ($oficinas as $oficina): ?>
                <?php echo $oficina['total_cuentas']; ?>,
            <?php endforeach; ?>
        ];
        
        var bolivianos = [
            <?php foreach ($oficinas as $oficina): ?>
                <?php echo $oficina['saldo_bolivianos']; ?>,
            <?php endforeach; ?>
        ];
        
        var dolares = [
            <?php foreach ($oficinas as $oficina): ?>
                <?php echo $oficina['saldo_dolares']; ?>,
            <?php endforeach; ?>
        ];
        
        // Gráfico de clientes y cuentas
        var ctxClientes = document.getElementById('chartClientes').getContext('2d');
        var chartClientes = new Chart(ctxClientes, {
            type: 'bar',
            data: {
                labels: oficinas,
                datasets: [
                    {
                        label: '<?php echo $lang['clients']; ?>',
                        data: clientes,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: '<?php echo $lang['accounts']; ?>',
                        data: cuentas,
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: '<?php echo $lang['clients_and_accounts_by_branch']; ?>'
                    }
                }
            }
        });
        
        // Gráfico de saldos
        var ctxSaldos = document.getElementById('chartSaldos').getContext('2d');
        var chartSaldos = new Chart(ctxSaldos, {
            type: 'bar',
            data: {
                labels: oficinas,
                datasets: [
                    {
                        label: '<?php echo $lang['bolivianos']; ?>',
                        data: bolivianos,
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: '<?php echo $lang['dollars']; ?>',
                        data: dolares,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: '<?php echo $lang['balances_by_branch']; ?>'
                    }
                }
            }
        });
    });
</script>