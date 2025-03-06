<h1><?php echo $lang['reports']; ?></h1>

<div class="dashboard">
    <div class="card">
        <div class="card-header"><?php echo $lang['transactions_by_date']; ?></div>
        <div class="card-body">
            <p><?php echo $lang['transactions_by_date_desc']; ?></p>
            <a href="index.php?controller=reporte&action=transaccionesPorFecha" class="btn btn-primary"><?php echo $lang['view_report']; ?></a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header"><?php echo $lang['transactions_by_client']; ?></div>
        <div class="card-body">
            <p><?php echo $lang['transactions_by_client_desc']; ?></p>
            <a href="index.php?controller=reporte&action=transaccionesPorCliente" class="btn btn-primary"><?php echo $lang['view_report']; ?></a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header"><?php echo $lang['atm_statistics']; ?></div>
        <div class="card-body">
            <p><?php echo $lang['atm_statistics_desc']; ?></p>
            <a href="index.php?controller=reporte&action=estadisticasATM" class="btn btn-primary"><?php echo $lang['view_report']; ?></a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><?php echo $lang['balances_by_branch']; ?></div>
    <div class="card-body">
        <p><?php echo $lang['balances_by_branch_desc']; ?></p>
        <a href="index.php?controller=reporte&action=saldosPorOficina" class="btn btn-primary"><?php echo $lang['view_report']; ?></a>
    </div>
</div>