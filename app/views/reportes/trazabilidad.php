<div class="page-header">
    <h1><i class="fas fa-link"></i> Reporte de Trazabilidad</h1>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros de Búsqueda</h3></div>
    <div class="card-body">
        <form method="GET" action="/reportes/trazabilidad" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Paciente ID</label>
                <input type="number" name="paciente_id" class="form-control" value="<?php echo htmlspecialchars($pacienteId ?? ''); ?>" placeholder="ID del paciente" style="width:150px;">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Desde</label>
                <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde ?? ''); ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <a href="/reportes/exportarExcel?tipo=trazabilidad" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</a>
        </form>
    </div>
</div>

<?php if (!empty($results)): ?>
<div class="card" style="margin-top:20px;">
    <div class="card-header"><h3>Cadena de Trazabilidad (<?php echo count($results); ?> registros)</h3></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha Registro</th>
                        <th>Paciente</th>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th>Vencimiento</th>
                        <th>Receta</th>
                        <th>Establecimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($r['fecha_registro'] ?? $r['fecha_dispensacion'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars(($r['paciente_nombre'] ?? '') . ' ' . ($r['paciente_apellido'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($r['medicamento_nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['nro_lote'] ?? ''); ?></td>
                        <td><?php echo isset($r['fecha_vencimiento']) ? date('d/m/Y', strtotime($r['fecha_vencimiento'])) : ''; ?></td>
                        <td><?php echo htmlspecialchars($r['codigo_receta'] ?? $r['receta_id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($r['establecimiento_nombre'] ?? ''); ?></td>
                        <td><a href="/trazabilidad/detalle?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Ver</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>