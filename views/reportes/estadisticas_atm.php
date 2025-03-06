<h1><?php echo $lang['reports']; ?> - <?php echo $lang['atm_statistics']; ?></h1>

<div class="card">
    <div class="card-header"><?php echo $lang['search_criteria']; ?></div>
    <div class="card-body">
        <form method="POST" action="index.php?controller=reporte&action=estadisticasATM">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="idATM"><?php echo $lang['select_atm']; ?></label>
                        <select class="form-control" id="idATM" name="idATM" required>
                            <option value=""><?php echo $lang['select_option']; ?></option>
                            <?php foreach ($atms as $atm): ?>
                                <option value="<?php echo $atm['idATM']; ?>" <?php echo (isset($_POST['idATM']) && $_POST['idATM'] == $atm['idATM']) ? 'selected' : ''; ?>>
                                    ATM #<?php echo $atm['idATM']; ?> - <?php echo $atm['ubicacion']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && $atmSeleccionado): ?>
<div class="card">
    <div class="card-header">
        <?php echo $lang['results']; ?>: ATM #<?php echo $atmSeleccionado->idATM; ?> - <?php echo $atmSeleccionado->ubicacion; ?>
    </div>
    <div class="card-body">
        <?php if (count($estadisticas) > 0): ?>
            <div class="row">
                <div class="col-md-12">
                    <canvas id="chartTransacciones" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang['period']; ?></th>
                            <th><?php echo $lang['withdrawals']; ?></th>
                            <th><?php echo $lang['deposits']; ?></th>
                            <th><?php echo $lang['withdrawal_amount']; ?></th>
                            <th><?php echo $lang['deposit_amount']; ?></th>
                            <th><?php echo $lang['total_transactions']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estadisticas as $est): ?>
                            <tr>
                                <td><?php echo $est['nombre_mes'] . ' ' . $est['anio']; ?></td>
                                <td><?php echo $est['retiros']; ?></td>
                                <td><?php echo $est['depositos']; ?></td>
                                <td><?php echo number_format($est['monto_retiros'], 2); ?></td>
                                <td><?php echo number_format($est['monto_depositos'], 2); ?></td>
                                <td><?php echo $est['total_transacciones']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <?php echo $lang['no_transactions_found']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Script para generar gráfico -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('chartTransacciones').getContext('2d');
        
        // Datos para el gráfico
        var labels = [
            <?php foreach ($estadisticas as $est): ?>
                '<?php echo $est['etiqueta']; ?>',
            <?php endforeach; ?>
        ];
        
        var depositos = [
            <?php foreach ($estadisticas as $est): ?>
                <?php echo $est['depositos']; ?>,
            <?php endforeach; ?>
        ];
        
        var retiros = [
            <?php foreach ($estadisticas as $est): ?>
                <?php echo $est['retiros']; ?>,
            <?php endforeach; ?>
        ];
        
        // Crear gráfico
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '<?php echo $lang['deposits']; ?>',
                        data: depositos,
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: '<?php echo $lang['withdrawals']; ?>',
                        data: retiros,
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
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
                }
            }
        });
    });
</script>
<?php endif; ?>