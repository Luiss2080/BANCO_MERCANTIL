<h1><?php echo $lang['new_account']; ?></h1>

<div class="card">
    <div class="card-header"><?php echo $lang['account_details']; ?></div>
    <div class="card-body">
        <!-- Formulario para crear una nueva cuenta -->
        <form method="POST" action="index.php?controller=cuenta&action=crear" class="needs-validation">
            <div class="row">
                <!-- Campo para seleccionar el cliente asociado a la cuenta -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="idPersona" class="form-label"><?php echo $lang['select_client']; ?> <span class="text-danger">*</span></label>
                        <select class="form-control" id="idPersona" name="idPersona" required <?php echo isset($_GET['idCliente']) ? 'disabled' : ''; ?>>
                            <option value=""><?php echo $lang['select_option']; ?></option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['idPersona']; ?>" 
                                    <?php echo (isset($_GET['idCliente']) && $_GET['idCliente'] == $cliente['idPersona']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidoPaterno'] . ' ' . $cliente['apellidoMaterno'] . ' (' . $cliente['ci'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Si el cliente viene predefinido por URL, se oculta el campo pero se envía su valor -->
                        <?php if (isset($_GET['idCliente'])): ?>
                            <input type="hidden" name="idPersona" value="<?php echo $_GET['idCliente']; ?>">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Campo para seleccionar el tipo de cuenta -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipoCuenta" class="form-label"><?php echo $lang['account_type']; ?> <span class="text-danger">*</span></label>
                        <select class="form-control" id="tipoCuenta" name="tipoCuenta" required>
                            <option value=""><?php echo $lang['select_option']; ?></option>
                            <option value="1"><?php echo $lang['savings_account']; ?></option>
                            <option value="2"><?php echo $lang['checking_account']; ?></option>
                        </select>
                    </div>
                </div>
                
                <!-- Campo para seleccionar el tipo de moneda -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tipoMoneda" class="form-label"><?php echo $lang['currency']; ?> <span class="text-danger">*</span></label>
                        <select class="form-control" id="tipoMoneda" name="tipoMoneda" required>
                            <option value=""><?php echo $lang['select_option']; ?></option>
                            <option value="1"><?php echo $lang['bolivianos']; ?></option>
                            <option value="2"><?php echo $lang['dollars']; ?></option>
                        </select>
                    </div>
                </div>
                
                <!-- Campo para ingresar el saldo inicial de la cuenta -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="saldoInicial" class="form-label"><?php echo $lang['initial_balance']; ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="monedaSymbol">Bs.</span>
                            </div>
                            <input type="number" class="form-control" id="saldoInicial" name="saldoInicial" min="0" step="0.01" value="0">
                        </div>
                        <small class="form-text text-muted"><?php echo $lang['initial_deposit_message']; ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción: Guardar y Cancelar -->
            <div class="form-group mt-3 text-center">
                <button type="submit" class="btn btn-primary"><?php echo $lang['save']; ?></button>
                <?php if (isset($_GET['idCliente'])): ?>
                    <!-- Si se está creando la cuenta desde la vista de un cliente, redirige a la vista del cliente -->
                    <a href="index.php?controller=cliente&action=ver&id=<?php echo $_GET['idCliente']; ?>" class="btn btn-secondary"><?php echo $lang['cancel']; ?></a>
                <?php else: ?>
                    <!-- Si no, redirige a la lista de cuentas -->
                    <a href="index.php?controller=cuenta&action=listar" class="btn btn-secondary"><?php echo $lang['cancel']; ?></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario al intentar enviarlo
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
    
    // Actualizar el símbolo de la moneda según la selección del usuario
    const tipoMonedaSelect = document.getElementById('tipoMoneda');
    const monedaSymbol = document.getElementById('monedaSymbol');
    
    tipoMonedaSelect.addEventListener('change', function() {
        if (this.value == '1') {
            monedaSymbol.textContent = 'Bs.';
        } else if (this.value == '2') {
            monedaSymbol.textContent = '$';
        } else {
            monedaSymbol.textContent = '';
        }
    });
});
</script>