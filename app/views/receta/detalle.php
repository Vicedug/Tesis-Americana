<div class="page-header">
    <h1><i class="fas fa-prescription"></i> Detalle de Receta <small><?php echo htmlspecialchars($receta['codigo_receta']); ?></small></h1>
    <div style="display:flex;gap:10px;">
        <?php if ($receta['estado'] === 'emitida'): ?>
            <a href="/receta/validar?id=<?php echo $receta['id']; ?>" class="btn btn-warning"><i class="fas fa-clipboard-check"></i> Validar Cobertura</a>
        <?php endif; ?>
        <a href="/receta" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3><i class="fas fa-file-medical"></i> Información de la Receta</h3>
        <div style="display:flex;gap:8px;">
            <?php
            $estadoClass = $receta['estado'] === 'emitida' ? 'warning' : ($receta['estado'] === 'validada' ? 'success' : 'danger');
            ?>
            <span class="badge badge-<?php echo $estadoClass; ?>"><?php echo ucfirst($receta['estado']); ?></span>
            <?php if (!empty($receta['cobertura_validada'])): ?>
                <span class="badge badge-success"><i class="fas fa-shield-alt"></i> Cobertura Validada</span>
            <?php else: ?>
                <span class="badge badge-secondary"><i class="fas fa-clock"></i> Cobertura Pendiente</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
            <div class="form-group">
                <label>Código de Receta</label>
                <p><strong><?php echo htmlspecialchars($receta['codigo_receta']); ?></strong></p>
            </div>
            <div class="form-group">
                <label>Fecha de Emisión</label>
                <p><?php echo date('d/m/Y H:i', strtotime($receta['fecha_emision'])); ?></p>
            </div>
            <div class="form-group">
                <label>Establecimiento</label>
                <p><?php echo htmlspecialchars($receta['establecimiento_nombre'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Paciente</label>
                <p><strong><?php echo htmlspecialchars(($receta['paciente_apellido'] ?? '') . ', ' . ($receta['paciente_nombre'] ?? '')); ?></strong></p>
            </div>
            <div class="form-group">
                <label>CI del Paciente</label>
                <p><?php echo htmlspecialchars($receta['paciente_ci'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Seguro Médico</label>
                <p>
                    <?php if (!empty($receta['paciente_asegurado'])): ?>
                        <span class="badge badge-success">Asegurado - <?php echo htmlspecialchars($receta['numero_asegurado'] ?? 'N/A'); ?></span>
                    <?php else: ?>
                        <span class="badge badge-danger">No asegurado</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="form-group">
                <label>Profesional</label>
                <p><?php echo htmlspecialchars(($receta['profesional_nombre'] ?? '') . ' ' . ($receta['profesional_apellido'] ?? '')); ?></p>
            </div>
            <div class="form-group">
                <label>Fecha de Consulta</label>
                <p><?php echo date('d/m/Y H:i', strtotime($receta['fecha_consulta'])); ?></p>
            </div>
            <div class="form-group">
                <label>Diagnóstico</label>
                <p><?php echo htmlspecialchars($receta['diagnostico'] ?? 'N/A'); ?></p>
            </div>
        </div>

        <?php if (!empty($receta['consulta_observaciones'])): ?>
        <div style="margin-top:15px;border-top:1px solid #eee;padding-top:15px;">
            <div class="form-group">
                <label><strong>Observaciones de la Consulta</strong></label>
                <p style="background:#fff3cd;padding:10px;border-radius:4px;"><?php echo nl2br(htmlspecialchars($receta['consulta_observaciones'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-pills"></i> Medicamentos Prescritos</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Medicamento</th>
                        <th>Principio Activo</th>
                        <th>Concentración</th>
                        <th>Forma Farmacéutica</th>
                        <th>Cantidad</th>
                        <th>Indicaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($receta['medicamentos'])): ?>
                        <tr><td colspan="7" class="text-center">No hay medicamentos registrados</td></tr>
                    <?php else: ?>
                        <?php $num = 1; ?>
                        <?php foreach ($receta['medicamentos'] as $med): ?>
                        <tr>
                            <td><?php echo $num++; ?></td>
                            <td><strong><?php echo htmlspecialchars($med['medicamento_nombre'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars($med['principio_activo'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($med['concentracion'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($med['forma_farmaceutica'] ?? 'N/A'); ?></td>
                            <td><strong><?php echo (int)($med['cantidad_prescrita'] ?? 0); ?></strong></td>
                            <td><?php echo htmlspecialchars($med['indicaciones'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="margin-top:15px;display:flex;gap:10px;">
    <?php if (!empty($receta['consulta_id'])): ?>
        <a href="/consulta/detalle?id=<?php echo $receta['consulta_id']; ?>" class="btn btn-info"><i class="fas fa-stethoscope"></i> Ver Consulta</a>
    <?php endif; ?>
    <a href="/receta" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Listado</a>
</div>
