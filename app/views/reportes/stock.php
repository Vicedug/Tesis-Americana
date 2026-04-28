<div class="page-header">
    <h1><i class="fas fa-boxes"></i> Reporte de Stock</h1>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" action="/reportes/stock" style="display:flex;gap:10px;align-items:end;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Establecimiento</label>
                <select name="establecimiento_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($establecimientos as $e): ?>
                    <option value="<?php echo $e['id']; ?>" <?php echo ($establecimientoId ?? 0) == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <a href="/reportes/exportarExcel?tipo=stock" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</a>
        </form>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header"><h3>Stock Actual</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th>Vencimiento</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lotes)): ?>
                        <?php foreach ($lotes as $l): ?>
                        <?php
                            $estadoClass = 'badge-success';
                            if (($l['estado'] ?? '') === 'agotado') $estadoClass = 'badge-danger';
                            if (($l['estado'] ?? '') === 'vencido') $estadoClass = 'badge-danger';
                            if (($l['estado'] ?? '') === 'disponible' && $l['cantidad'] <= 10) $estadoClass = 'badge-warning';
                            $ diasRestantes = (strtotime($l['fecha_vencimiento']) - time()) / 86400;
                            if ($diasRestantes < 90 && $diasRestantes > 0) $estadoClass = 'badge-warning';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($l['medicamento_nombre'] ?? $l['medicamento_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($l['nro_lote']); ?></td>
                            <td><?php echo $l['cantidad']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($l['fecha_vencimiento'])); ?></td>
                            <td><?php echo htmlspecialchars($l['proveedor'] ?? 'N/A'); ?></td>
                            <td><span class="badge <?php echo $estadoClass; ?>"><?php echo $l['estado'] ?? 'N/A'; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No hay datos de stock</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>