<h1><?php echo $lang['edit']; ?></h1> <!-- Título de la página: "Editar" (traducido según el idioma seleccionado) -->

<div class="card">
    <div class="card-header"><?php echo $lang['client_details']; ?></div> <!-- Encabezado de la tarjeta: "Detalles del cliente" (traducido) -->
    <div class="card-body">
        <!-- Formulario para editar los datos del cliente. Se envía al controlador "cliente" con la acción "editar" y el ID del cliente -->
        <form method="POST" action="index.php?controller=cliente&action=editar&id=<?php echo $cliente->idPersona; ?>" class="needs-validation">
            <div class="row">
                <!-- Sección de datos personales -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nombre" class="form-label"><?php echo $lang['name']; ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente->nombre); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="apellidoPaterno" class="form-label"><?php echo $lang['paternal_last_name']; ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?php echo htmlspecialchars($cliente->apellidoPaterno); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="apellidoMaterno" class="form-label"><?php echo $lang['maternal_last_name']; ?></label>
                        <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?php echo htmlspecialchars($cliente->apellidoMaterno); ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ci" class="form-label"><?php echo $lang['id_number']; ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ci" name="ci" value="<?php echo htmlspecialchars($cliente->ci); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fechaNacimiento" class="form-label"><?php echo $lang['birth_date']; ?></label>
                        <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo $cliente->fechaNacimiento; ?>">
                    </div>
                </div>
                
                <!-- Sección de datos de contacto -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="direccion" class="form-label"><?php echo $lang['address']; ?></label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente->direccion); ?>">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefono" class="form-label"><?php echo $lang['phone']; ?></label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{1,3}-[0-9]{7,8}" value="<?php echo htmlspecialchars($cliente->telefono); ?>">
                        <small class="form-text text-muted">Formato: 3-3456789 o 70-1234567</small> <!-- Indicación del formato esperado para el teléfono -->
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email" class="form-label"><?php echo $lang['email']; ?></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($cliente->email); ?>">
                    </div>
                </div>
                
                <!-- Sección de selección de oficina asignada -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="idOficina" class="form-label"><?php echo $lang['branches']; ?> <span class="text-danger">*</span></label>
                        <select class="form-control" id="idOficina" name="idOficina" required>
                            <option value=""><?php echo $lang['select_option']; ?></option> <!-- Opción por defecto: "Seleccione una opción" -->
                            <?php foreach ($oficinas as $oficina): ?>
                                <option value="<?php echo $oficina['idOficina']; ?>" <?php echo ($cliente->idOficina == $oficina['idOficina']) ? 'selected' : ''; ?>>
                                    <?php echo $oficina['nombre']; ?>
                                    <?php if ($oficina['central']): ?> 
                                        (<?php echo $lang['central_office']; ?>) <!-- Si es la oficina central, se muestra como "(Oficina central)" -->
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción del formulario -->
            <div class="form-group mt-3 text-center">
                <button type="submit" class="btn btn-primary"><?php echo $lang['save']; ?></button> <!-- Botón para guardar los cambios -->
                <a href="index.php?controller=cliente&action=ver&id=<?php echo $cliente->idPersona; ?>" class="btn btn-info"><?php echo $lang['back']; ?></a> <!-- Botón para volver a la vista del cliente -->
                <a href="index.php?controller=cliente&action=listar" class="btn btn-secondary"><?php echo $lang['cancel']; ?></a> <!-- Botón para cancelar y volver a la lista de clientes -->
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
            event.preventDefault(); // Evita que el formulario se envíe si no es válido
            event.stopPropagation(); // Detiene la propagación del evento
        }
        form.classList.add('was-validated'); // Añade la clase para mostrar mensajes de validación
    }, false);
    
    // Formateo automático del número de teléfono mientras el usuario escribe
    const telefonoInput = document.getElementById('telefono');
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Elimina todos los caracteres que no sean dígitos
            if (value.length > 0) {
                if (value.length <= 8) {
                    value = value.replace(/^(\d{1,3})(\d{0,})$/, function(match, p1, p2) {
                        return p2 ? p1 + '-' + p2 : p1; // Formato para números de 1 a 8 dígitos (ejemplo: 3-3456789)
                    });
                } else {
                    value = value.replace(/^(\d{2})(\d{0,})$/, function(match, p1, p2) {
                        return p2 ? p1 + '-' + p2 : p1; // Formato para números de más de 8 dígitos (ejemplo: 70-1234567)
                    });
                }
            }
            e.target.value = value; // Actualiza el valor del campo de teléfono con el formato aplicado
        });
    }
});
</script>