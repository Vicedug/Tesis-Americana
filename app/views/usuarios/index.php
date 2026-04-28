<div class="page-header">
    <h1><i class="fas fa-users-cog"></i> Gestión de Usuarios</h1>
    <div>
        <a href="/usuarios/crear" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo Usuario</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="/usuarios" style="margin-bottom:15px;display:flex;gap:10px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o usuario..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="max-width:300px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Establecimiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios['data'] ?? $usuarios)): ?>
                        <?php foreach (($usuarios['data'] ?? $usuarios) as $u): ?>
                        <?php
                            $roleLabels = ['administrador' => 'Administrador', 'profesional_salud' => 'Prof. de Salud', 'farmaceutico' => 'Farmacéutico', 'auditor' => 'Auditor'];
                            $roleBadges = ['administrador' => 'badge-primary', 'profesional_salud' => 'badge-success', 'farmaceutico' => 'badge-info', 'auditor' => 'badge-warning'];
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['nombre'] . ' ' . $u['apellido']); ?></td>
                            <td><?php echo htmlspecialchars($u['email'] ?? 'N/A'); ?></td>
                            <td><span class="badge <?php echo $roleBadges[$u['rol']] ?? 'badge-primary'; ?>"><?php echo $roleLabels[$u['rol']] ?? $u['rol']; ?></span></td>
                            <td><?php echo htmlspecialchars($u['establecimiento_nombre'] ?? $u['establecimiento_id'] ?? 'N/A'); ?></td>
                            <td><?php echo $u['activo'] ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'; ?></td>
                            <td>
                                <a href="/usuarios/editar?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No hay usuarios registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>