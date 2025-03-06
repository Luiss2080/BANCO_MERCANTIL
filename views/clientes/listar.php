<!-- Título de la página -->
<h1><?php echo $lang['client_list']; ?></h1>

<!-- Tarjeta que contiene la lista de clientes -->
<div class="card">
    <!-- Encabezado de la tarjeta -->
    <div class="card-header">
        <div class="row">
            <!-- Formulario de búsqueda -->
            <div class="col-md-6">
                <form method="GET" action="index.php" class="search-form">
                    <!-- Campos ocultos para mantener el controlador y la acción -->
                    <input type="hidden" name="controller" value="cliente">
                    <input type="hidden" name="action" value="listar">
                    <div class="input-group">
                        <!-- Campo de búsqueda -->
                        <input type="text" class="form-control" name="busqueda" placeholder="<?php echo $lang['search']; ?>..." value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
                        <!-- Botón para realizar la búsqueda -->
                        <button type="submit" class="btn btn-primary"><?php echo $lang['search']; ?></button>
                    </div>
                </form>
            </div>
            <!-- Botón para crear un nuevo cliente -->
            <div class="col-md-6 text-right">
                <a href="index.php?controller=cliente&action=crear" class="btn btn-success">
                    <i class="icon-plus"></i> <?php echo $lang['new_client']; ?>
                </a>
            </div>
        </div>
    </div>
    <!-- Cuerpo de la tarjeta -->
    <div class="card-body">
        <!-- Verifica si hay clientes para mostrar -->
        <?php if (count($clientes) > 0): ?>
            <!-- Tabla responsiva que muestra la lista de clientes -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <!-- Encabezado de la tabla -->
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?php echo $lang['name']; ?></th>
                            <th><?php echo $lang['paternal_last_name']; ?></th>
                            <th><?php echo $lang['maternal_last_name']; ?></th>
                            <th><?php echo $lang['id_number']; ?></th>
                            <th><?php echo $lang['phone']; ?></th>
                            <th><?php echo $lang['email']; ?></th>
                            <th><?php echo $lang['branches']; ?></th>
                            <th><?php echo $lang['actions']; ?></th>
                        </tr>
                    </thead>
                    <!-- Cuerpo de la tabla -->
                    <tbody>
                        <!-- Itera sobre la lista de clientes -->
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <!-- Muestra el ID del cliente -->
                                <td><?php echo $cliente['idPersona']; ?></td>
                                <!-- Muestra el nombre del cliente -->
                                <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                <!-- Muestra el apellido paterno del cliente -->
                                <td><?php echo htmlspecialchars($cliente['apellidoPaterno']); ?></td>
                                <!-- Muestra el apellido materno del cliente -->
                                <td><?php echo htmlspecialchars($cliente['apellidoMaterno']); ?></td>
                                <!-- Muestra el número de identificación del cliente -->
                                <td><?php echo htmlspecialchars($cliente['ci']); ?></td>
                                <!-- Muestra el número de teléfono del cliente -->
                                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                <!-- Muestra el correo electrónico del cliente -->
                                <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <!-- Muestra la oficina asignada al cliente -->
                                <td><?php echo htmlspecialchars($cliente['oficina_nombre']); ?></td>
                                <!-- Acciones disponibles para cada cliente -->
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Botón para ver los detalles del cliente -->
                                        <a href="index.php?controller=cliente&action=ver&id=<?php echo $cliente['idPersona']; ?>" class="btn btn-sm btn-info" title="<?php echo $lang['view']; ?>">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <!-- Botón para editar el cliente -->
                                        <a href="index.php?controller=cliente&action=editar&id=<?php echo $cliente['idPersona']; ?>" class="btn btn-sm btn-warning" title="<?php echo $lang['edit']; ?>">
                                            <i class="icon-edit"></i>
                                        </a>
                                        <!-- Botón para eliminar el cliente -->
                                        <a href="index.php?controller=cliente&action=eliminar&id=<?php echo $cliente['idPersona']; ?>" class="btn btn-sm btn-danger delete-button" title="<?php echo $lang['delete']; ?>" onclick="return confirm('<?php echo $lang['confirm_delete']; ?>')">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Mensaje que se muestra si no hay clientes -->
            <div class="alert alert-info">
                <?php echo $lang['no_clients']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>