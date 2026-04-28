<div class="page-header">
    <h1><i class="fas fa-chart-bar"></i> Reporte de Consumo</h1>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros</h3></div>
    <div class="card-body">
        <form method="GET" action="/reportes/consumo" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Desde</label>
                <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde ?? date('Y-m-01')); ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta ?? date('Y-m-d')); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <a href="/reportes/exportarExcel?tipo=consumo" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</a>
            <a href="/reportes/exportarPdf?tipo=consumo" class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
        </form>
    </div>
</div>

<?php if (!empty($results)): ?>
<div class="card" style="margin-top:20px;">
    <div class="card-header"><h3>Resultados (<?php echo count($results); ?> registros)</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th>Establecimiento</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($r['fecha_dispensacion'])); ?></td>
                        <td><?php echo htmlspecialchars(($r['paciente_nombre'] ?? '') . ' ' . ($r['paciente_apellido'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($r['medicamento_nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['nro_lote'] ?? ''); ?></td>
                        <td><?php echo $r['cantidad_dispensada'] ?? 0; ?></td>
                        <td><?php echo htmlspecialchars($r['establecimiento_nombre'] ?? ''); ?></td>
                        <td><span class="badge badge-<?php echo ($r['dispensacion_estado'] ?? $r['estado'] ?? '') === 'completada' ? 'success' : 'warning'; ?>"><?php echo $r['dispensacion_estado'] ?? $r['estado'] ?? 'N/A'; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>