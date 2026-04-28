<div class="page-header">
    <h1><i class="fas fa-pills"></i> Medicamentos</h1>
    <div>
        <a href="/farmacia/ingreso" class="btn btn-success"><i class="fas fa-plus-circle"></i> Nuevo Ingreso</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="/farmacia/medicamentos" class="form-inline" style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap;">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, principio activo o código..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="flex:1;min-width:250px;">
            <select name="forma" class="form-control" style="min-width:200px;">
                <option value="">Todas las formas</option>
                <?php foreach ($formas as $f): ?>
                    <option value="<?php echo htmlspecialchars($f['forma_farmaceutica']); ?>" <?php echo (($forma ?? '') === $f['forma_farmaceutica']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($f['forma_farmaceutica']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if (!empty($search) || !empty($forma)): ?>
                <a href="/farmacia/medicamentos" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Principio Activo</th>
                        <th>Forma Farmacéutica</th>
                        <th>Concentración</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicamentos['data'])): ?>
                        <tr><td colspan="7" class="text-center">No se encontraron medicamentos</td></tr>
                    <?php else: ?>
                        <?php foreach ($medicamentos['data'] as $med): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($med['codigo_nacional'] ?? 'N/A'); ?></td>
                            <td><strong><?php echo htmlspecialchars($med['nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($med['principio_activo']); ?></td>
                            <td><?php echo htmlspecialchars($med['forma_farmaceutica']); ?></td>
                            <td><?php echo htmlspecialchars($med['concentracion'] . ' ' . ($med['unidad_medida'] ?? '')); ?></td>
                            <td>
                                <?php if ((int)$med['activo'] === 1): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" action="/farmacia/toggle-medicamento" style="display:inline;">
                                    <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>">
                                    <input type="hidden" name="id" value="<?php echo $med['id']; ?>">
                                    <button type="submit" class="btn btn-sm <?php echo (int)$med['activo'] === 1 ? 'btn-warning' : 'btn-success'; ?>" title="<?php echo (int)$med['activo'] === 1 ? 'Desactivar' : 'Activar'; ?>">
                                        <i class="fas fa-<?php echo (int)$med['activo'] === 1 ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($medicamentos['pages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $medicamentos['pages']; $i++): ?>
                <?php if ($i == $medicamentos['page']): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="/farmacia/medicamentos?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($forma) ? '&forma=' . urlencode($forma) : ''; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>