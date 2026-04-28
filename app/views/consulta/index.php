<div class="page-header">
    <h1><i class="fas fa-stethoscope"></i> Gestión de Consultas <small>Listado general</small></h1>
    <div>
        <a href="/consulta/crear" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Consulta</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="/consulta" class="form-inline" style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por CI, nombre o diagnóstico..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="flex:1;min-width:250px;">
            <select name="estado" class="form-control" style="width:180px;">
                <option value="">Todos los estados</option>
                <option value="activa" <?php echo ($estado ?? '') === 'activa' ? 'selected' : ''; ?>>Activa</option>
                <option value="cerrada" <?php echo ($estado ?? '') === 'cerrada' ? 'selected' : ''; ?>>Cerrada</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if (($search ?? '') !== '' || ($estado ?? '') !== ''): ?>
                <a href="/consulta" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>CI</th>
                        <th>Profesional</th>
                        <th>Diagnóstico</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($consultas['data'])): ?>
                        <tr><td colspan="7" class="text-center">No se encontraron consultas</td></tr>
                    <?php else: ?>
                        <?php foreach ($consultas['data'] as $c): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($c['fecha_consulta'])); ?></td>
                            <td><?php echo htmlspecialchars(($c['paciente_apellido'] ?? '') . ', ' . ($c['paciente_nombre'] ?? '')); ?></td>
                            <td><strong><?php echo htmlspecialchars($c['paciente_ci'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars(($c['profesional_nombre'] ?? '') . ' ' . ($c['profesional_apellido'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars(mb_strimwidth($c['diagnostico'], 0, 50, '...')); ?></td>
                            <td>
                                <?php
                                $estadoClass = ($c['estado'] ?? '') === 'activa' ? 'success' : (($c['estado'] ?? '') === 'cerrada' ? 'info' : 'danger');
                                ?>
                                <span class="badge badge-<?php echo $estadoClass; ?>"><?php echo ucfirst($c['estado'] ?? ''); ?></span>
                            </td>
                            <td>
                                <a href="/consulta/detalle?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <?php if (($c['estado'] ?? '') === 'activa'): ?>
                                <a href="/receta/crear?consulta_id=<?php echo $c['id']; ?>" class="btn btn-sm btn-primary" title="Crear receta"><i class="fas fa-prescription"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($consultas['pages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $consultas['pages']; $i++): ?>
                <?php if ($i == $consultas['page']): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="/consulta?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($estado) ? '&estado=' . urlencode($estado) : ''; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
