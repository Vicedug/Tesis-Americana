<div class="page-header">
    <h1><i class="fas fa-users"></i> Gestión de Pacientes <small>Listado general</small></h1>
    <div>
        <a href="/paciente/buscar" class="btn btn-primary"><i class="fas fa-search"></i> Buscar por CI</a>
        <a href="/paciente/crear" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo Paciente</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="/paciente" class="form-inline" style="display:flex;gap:10px;margin-bottom:15px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por CI, nombre o apellido..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="flex:1;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if ($search): ?>
                <a href="/paciente" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>CI</th>
                        <th>Nombre Completo</th>
                        <th>Fecha Nac.</th>
                        <th>Sexo</th>
                        <th>Asegurado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pacientes['data'])): ?>
                        <tr><td colspan="6" class="text-center">No se encontraron pacientes</td></tr>
                    <?php else: ?>
                        <?php foreach ($pacientes['data'] as $p): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p['ci']); ?></strong></td>
                            <td><?php echo htmlspecialchars($p['apellido'] . ', ' . $p['nombre']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p['fecha_nacimiento'])); ?></td>
                            <td><?php echo $p['sexo'] === 'M' ? 'Masculino' : 'Femenino'; ?></td>
                            <td>
                                <?php if ($p['asegurado']): ?>
                                    <span class="badge badge-success">Asegurado</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No asegurado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/paciente/perfil?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="/paciente/editar?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($pacientes['pages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pacientes['pages']; $i++): ?>
                <?php if ($i == $pacientes['page']): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="/paciente?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>