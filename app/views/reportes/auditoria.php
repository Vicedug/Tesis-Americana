<div class="page-header">
    <h1><i class="fas fa-clipboard-list"></i> Reporte de Auditoría</h1>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" action="/reportes/auditoria" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Desde</label>
                <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde ?? date('Y-m-01')); ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta ?? date('Y-m-d')); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
        </form>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header"><h3>Registro de Movimientos</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th>Origen/Destino</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($movimientos)): ?>
                        <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($m['fecha_movimiento'])); ?></td>
                            <td><span class="badge badge-<?php echo $m['tipo'] === 'entrada' ? 'success' : ($m['tipo'] === 'salida' ? 'danger' : 'info'); ?>"><?php echo ucfirst($m['tipo']); ?></span></td>
                            <td><?php echo htmlspecialchars($m['medicamento_nombre'] ?? $m['medicamento_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($m['nro_lote'] ?? $m['lote_id'] ?? ''); ?></td>
                            <td><?php echo $m['cantidad']; ?></td>
                            <td><?php echo htmlspecialchars($m['establecimiento_origen'] ?? $m['establecimiento_destino'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($m['usuario_nombre'] ?? $m['usuario_id'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No hay movimientos en el período seleccionado</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>