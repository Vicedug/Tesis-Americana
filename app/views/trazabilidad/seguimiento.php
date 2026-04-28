<div class="page-header">
    <h1><i class="fas fa-search"></i> Seguimiento de Trazabilidad</h1>
</div>

<div class="card">
    <div class="card-header"><h3>Buscar por</h3></div>
    <div class="card-body">
        <div class="form-group">
            <label for="search_type">Tipo de búsqueda</label>
            <select id="search_type" name="search_type" class="form-control" style="max-width:300px;">
                <option value="paciente" <?php echo ($type ?? 'paciente') === 'paciente' ? 'selected' : ''; ?>>Paciente (CI o nombre)</option>
                <option value="medicamento" <?php echo ($type ?? '') === 'medicamento' ? 'selected' ; ?>>Medicamento</option>
                <option value="lote" <?php echo ($type ?? '') === 'lote' ? 'selected' : ''; ?>>Número de lote</option>
            </select>
        </div>
        <div class="form-group">
            <label for="search_value">Buscar</label>
            <input type="text" id="search_value" class="form-control" placeholder="Ingrese término de búsqueda..." value="<?php echo htmlspecialchars($value ?? ''); ?>" style="max-width:400px;">
        </div>
    </div>
</div>

<div id="traceability-results">
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
                            <th>CI</th>
                            <th>Medicamento</th>
                            <th>Lote</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_registro'] ?? $row['fecha_dispensacion'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars(($row['paciente_nombre'] ?? '') . ' ' . ($row['paciente_apellido'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($row['ci'] ?? $row['paciente_ci'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['medicamento_nombre'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['nro_lote'] ?? $row['lote_id'] ?? ''); ?></td>
                            <td><?php echo isset($row['fecha_vencimiento']) ? date('d/m/Y', strtotime($row['fecha_vencimiento'])) : ''; ?></td>
                            <td><span class="badge <?php echo ($row['dispensacion_estado'] ?? '') === 'completada' ? 'badge-success' : 'badge-warning'; ?>"><?php echo $row['dispensacion_estado'] ?? 'N/A'; ?></span></td>
                            <td><a href="/trazabilidad/detalle?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Ver</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div style="margin-top:15px;">
    <a href="/trazabilidad" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<script src="/assets/js/traceability.js"></script>