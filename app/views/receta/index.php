<div class="page-header">
    <h1><i class="fas fa-prescription"></i> Gestión de Recetas <small>Listado general</small></h1>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="/receta" class="form-inline" style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por código, CI, nombre..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="flex:1;min-width:250px;">
            <select name="estado" class="form-control" style="width:180px;">
                <option value="">Todos los estados</option>
                <option value="emitida" <?php echo ($estado ?? '') === 'emitida' ? 'selected' : ''; ?>>Emitida</option>
                <option value="validada" <?php echo ($estado ?? '') === 'validada' ? 'selected' : ''; ?>>Validada</option>
                <option value="rechazada" <?php echo ($estado ?? '') === 'rechazada' ? 'selected' : ''; ?>>Rechazada</option>
                <option value="dispensada" <?php echo ($estado ?? '') === 'dispensada' ? 'selected' : ''; ?>>Dispensada</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if (($search ?? '') !== '' || ($estado ?? '') !== ''): ?>
                <a href="/receta" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Fecha Emisión</th>
                        <th>Paciente</th>
                        <th>CI</th>
                        <th>Profesional</th>
                        <th>Diagnóstico</th>
                        <th>Estado</th>
                        <th>Cobertura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recetas['data'])): ?>
                        <tr><td colspan="9" class="text-center">No se encontraron recetas</td></tr>
                    <?php else: ?>
                        <?php foreach ($recetas['data'] as $r): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($r['codigo_receta']); ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($r['fecha_emision'])); ?></td>
                            <td><?php echo htmlspecialchars(($r['paciente_apellido'] ?? '') . ', ' . ($r['paciente_nombre'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($r['paciente_ci'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(($r['profesional_nombre'] ?? '') . ' ' . ($r['profesional_apellido'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars(mb_strimwidth($r['diagnostico'] ?? '', 0, 40, '...')); ?></td>
                            <td>
                                <?php
                                $estadoLabels = [
                                    'emitida' => 'warning',
                                    'validada' => 'success',
                                    'rechazada' => 'danger',
                                    'dispensada' => 'info',
                                ];
                                $badgeClass = $estadoLabels[$r['estado'] ?? ''] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?php echo $badgeClass; ?>"><?php echo ucfirst($r['estado'] ?? ''); ?></span>
                            </td>
                            <td>
                                <?php if (!empty($r['cobertura_validada'])): ?>
                                    <span class="badge badge-success"><i class="fas fa-check"></i></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fas fa-clock"></i></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/receta/detalle?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <?php if (($r['estado'] ?? '') === 'emitida'): ?>
                                    <a href="/receta/validar?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-warning" title="Validar cobertura"><i class="fas fa-clipboard-check"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($recetas['pages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $recetas['pages']; $i++): ?>
                <?php if ($i == $recetas['page']): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="/receta?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($estado) ? '&estado=' . urlencode($estado) : ''; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
