<div class="page-header">
    <h1><i class="fas fa-hand-holding-medical"></i> Dispensaciones <small>Listado general</small></h1>
    <div>
        <a href="/dispensacion/procesar" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Dispensación</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="/dispensacion" style="display:flex;gap:10px;margin-bottom:15px;">
            <select name="estado" class="form-control" style="max-width:200px;">
                <option value="">Todos los estados</option>
                <option value="completada" <?php echo ($estado ?? '') === 'completada' ? 'selected' : ''; ?>>Completada</option>
                <option value="parcial" <?php echo ($estado ?? '') === 'parcial' ? 'selected' : ''; ?>>Parcial</option>
                <option value="cancelada" <?php echo ($estado ?? '') === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dispensaciones['data'])): ?>
                        <?php foreach ($dispensaciones['data'] as $d): ?>
                        <tr>
                            <td><?php echo $d['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($d['fecha_dispensacion'])); ?></td>
                            <td><?php echo htmlspecialchars(($d['paciente_nombre'] ?? '') . ' ' . ($d['paciente_apellido'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($d['medicamento_nombre'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($d['nro_lote'] ?? $d['lote_id'] ?? ''); ?></td>
                            <td><?php echo $d['cantidad_dispensada'] ?? ''; ?></td>
                            <td><span class="badge badge-<?php echo ($d['estado'] ?? '') === 'completada' ? 'success' : 'warning'; ?>"><?php echo $d['estado'] ?? 'N/A'; ?></span></td>
                            <td><a href="/dispensacion/confirmacion?id=<?php echo $d['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">No hay dispensaciones registradas</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($dispensaciones['pages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $dispensaciones['pages']; $i++): ?>
                <?php if ($i == $dispensaciones['page']): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="/dispensacion?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>