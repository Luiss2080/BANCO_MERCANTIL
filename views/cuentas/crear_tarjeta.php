<h1><?php echo $lang['request_card']; ?></h1> <!-- Título de la página: "Solicitar tarjeta" (traducido según el idioma seleccionado) -->

<div class="card">
    <div class="card-header">
        <h3><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno . ' ' . $cliente->apellidoMaterno); ?></h3> <!-- Nombre completo del cliente -->
        <p><?php echo $lang['account_number']; ?>: <strong><?php echo htmlspecialchars($cuenta->nroCuenta); ?></strong></p> <!-- Número de cuenta del cliente -->
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <!-- Formulario para solicitar una tarjeta -->
                <form method="POST" action="index.php?controller=cuenta&action=crearTarjeta&id=<?php echo $cuenta->idCuenta; ?>" class="needs-validation">
                    <div class="form-group">
                        <label for="pin" class="form-label"><?php echo $lang['pin']; ?> <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="pin" name="pin" pattern="[0-9]{4}" maxlength="4" required> <!-- Campo para ingresar el PIN -->
                        <small class="form-text text-muted"><?php echo $lang['pin_must_be_4_digits']; ?></small> <!-- Indicación de que el PIN debe tener 4 dígitos -->
                    </div>
                    
                    <div class="form-group">
                        <label for="pin_confirmacion" class="form-label"><?php echo $lang['confirm_pin']; ?> <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="pin_confirmacion" name="pin_confirmacion" pattern="[0-9]{4}" maxlength="4" required> <!-- Campo para confirmar el PIN -->
                    </div>
                    
                    <!-- Mensaje informativo sobre el PIN -->
                    <div class="alert alert-info mt-3">
                        <strong><?php echo $lang['important']; ?>:</strong> <?php echo $lang['card_pin_info']; ?>
                    </div>
                    
                    <!-- Botones de acción del formulario -->
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary"><?php echo $lang['request_card']; ?></button> <!-- Botón para solicitar la tarjeta -->
                        <a href="index.php?controller=cuenta&action=ver&id=<?php echo $cuenta->idCuenta; ?>" class="btn btn-secondary"><?php echo $lang['cancel']; ?></a> <!-- Botón para cancelar y volver a la vista de la cuenta -->
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <!-- Vista previa de la tarjeta -->
                <div class="card-preview" style="background: linear-gradient(45deg, #056f1f, #0a4d14); color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; min-height: 200px;">
                    <div style="font-size: 1.2rem; margin-bottom: 20px; letter-spacing: 2px;">
                        **** **** **** **** <!-- Número de tarjeta simulado -->
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <div style="font-size: 0.8rem; text-transform: uppercase;"><?php echo $lang['cardholder']; ?></div> <!-- Título: "Titular de la tarjeta" -->
                            <div><?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidoPaterno); ?></div> <!-- Nombre del titular -->
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; text-transform: uppercase;"><?php echo $lang['expiration_date']; ?></div> <!-- Título: "Fecha de expiración" -->
                            <div>--/--</div> <!-- Fecha de expiración simulada -->
                        </div>
                    </div>
                </div>
                
                <!-- Sección de beneficios de la tarjeta -->
                <div class="card bg-light">
                    <div class="card-body">
                        <h5><?php echo $lang['card_benefits']; ?></h5> <!-- Título: "Beneficios de la tarjeta" -->
                        <ul>
                            <li><?php echo $lang['card_benefit_1']; ?></li> <!-- Beneficio 1 -->
                            <li><?php echo $lang['card_benefit_2']; ?></li> <!-- Beneficio 2 -->
                            <li><?php echo $lang['card_benefit_3']; ?></li> <!-- Beneficio 3 -->
                            <li><?php echo $lang['card_benefit_4']; ?></li> <!-- Beneficio 4 -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario al intentar enviarlo
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        const pin = document.getElementById('pin').value; // Obtiene el valor del campo PIN
        const pinConfirmacion = document.getElementById('pin_confirmacion').value; // Obtiene el valor del campo de confirmación del PIN
        
        // Verifica si los PINs coinciden
        if (pin !== pinConfirmacion) {
            event.preventDefault(); // Evita que el formulario se envíe
            alert('<?php echo $lang['pins_dont_match']; ?>'); // Muestra un mensaje de error
            return false;
        }
        
        // Verifica si el formulario es válido
        if (!form.checkValidity()) {
            event.preventDefault(); // Evita que el formulario se envíe
            event.stopPropagation(); // Detiene la propagación del evento
        }
        form.classList.add('was-validated'); // Añade la clase para mostrar mensajes de validación
    }, false);
    
    // Restringir la entrada a solo números en los campos de PIN
    const pinInputs = document.querySelectorAll('input[type="password"]');
    pinInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, ''); // Elimina caracteres que no sean números
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4); // Limita la longitud del PIN a 4 dígitos
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault(); // Evita que se ingresen caracteres que no sean números
            }
        });
    });
});
</script>