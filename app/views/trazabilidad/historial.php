<div class="page-header">
    <h1><i class="fas fa-history"></i> Historial de Trazabilidad</h1>
</div>

<div class="card">
    <div class="card-header"><h3>Filtros de búsqueda</h3></div>
    <div class="card-body">
        <form method="GET" action="/trazabilidad/historial" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;">
            <div class="form-group" style="margin-bottom:0;">
                <label>Desde</label>
                <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde ?? ''); ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <a href="/trazabilidad/historial" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
        </form>
    </div>
</div>

<?php if (!empty($results) && is_array($results) && isset($results['data'])): ?>
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3>Resultados</h3>
        <a href="/reportes/trazabilidad" class="btn btn-sm btn-info"><i class="fas fa-file-export"></i> Exportar</a>
    </div>
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
                        <th>Establecimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results['data'] as $row): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_registro'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars(($row['paciente_nombre'] ?? '') . ' ' . ($row['paciente_apellido'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($row['medicamento_nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['nro_lote'] ?? ''); ?></td>
                        <td><?php echo isset($row['fecha_vencimiento']) ? date('d/m/Y', strtotime($row['fecha_vencimiento'])) : ''; ?></td>
                        <td><?php echo htmlspecialchars($row['establecimiento_nombre'] ?? ''); ?></td>
                        <td><a href="/trazabilidad/detalle?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>