<div class="page-header">
    <h1><i class="fas fa-clipboard-check"></i> Validar Cobertura <small><?php echo htmlspecialchars($receta['codigo_receta']); ?></small></h1>
    <a href="/receta" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3><i class="fas fa-info-circle"></i> Información de la Receta</h3>
        <?php
        $estadoClass = $receta['estado'] === 'emitida' ? 'warning' : ($receta['estado'] === 'validada' ? 'success' : 'danger');
        ?>
        <span class="badge badge-<?php echo $estadoClass; ?>"><?php echo ucfirst($receta['estado']); ?></span>
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
                <label>Cobertura Actual</label>
                <p>
                    <?php if (!empty($receta['cobertura_validada'])): ?>
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Validada</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Pendiente de validación</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="form-group">
                <label>Paciente</label>
                <p><?php echo htmlspecialchars(($receta['paciente_apellido'] ?? '') . ', ' . ($receta['paciente_nombre'] ?? '')); ?></p>
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
                <label>Establecimiento</label>
                <p><?php echo htmlspecialchars($receta['establecimiento_nombre'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Diagnóstico</label>
                <p><?php echo htmlspecialchars($receta['diagnostico'] ?? 'N/A'); ?></p>
            </div>
        </div>
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
                        <th>Medicamento</th>
                        <th>Principio Activo</th>
                        <th>Concentración</th>
                        <th>Forma Farm.</th>
                        <th>Cantidad</th>
                        <th>Indicaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($receta['medicamentos'])): ?>
                        <tr><td colspan="6" class="text-center">No hay medicamentos registrados</td></tr>
                    <?php else: ?>
                        <?php foreach ($receta['medicamentos'] as $med): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($med['medicamento_nombre'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars($med['principio_activo'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($med['concentracion'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($med['forma_farmaceutica'] ?? 'N/A'); ?></td>
                            <td><?php echo (int)($med['cantidad_prescrita'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($med['indicaciones'] ?? 'N/A'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($receta['estado'] === 'emitida'): ?>
<div class="card" style="border-left:4px solid #ffc107;">
    <div class="card-header" style="background:#fff3cd;">
        <h3><i class="fas fa-clipboard-check"></i> Decisión de Cobertura</h3>
    </div>
    <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;gap:20px;">
        <p style="margin:0;color:#666;">Revise los medicamentos y la información del paciente para determinar si la cobertura es válida.</p>
        <div style="display:flex;gap:10px;">
            <form action="/receta/validarCobertura" method="POST" onsubmit="return confirm('¿Confirma la validación de cobertura para esta receta?')">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="id" value="<?php echo $receta['id']; ?>">
                <button type="submit" class="btn btn-success"><i class="fas fa-check-circle"></i> Validar Cobertura</button>
            </form>
            <form action="/receta/rechazar" method="POST" onsubmit="return confirm('¿Confirma el rechazo de esta receta?')">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="id" value="<?php echo $receta['id']; ?>">
                <button type="submit" class="btn btn-danger"><i class="fas fa-times-circle"></i> Rechazar Receta</button>
            </form>
        </div>
    </div>
</div>
<?php elseif ($receta['estado'] === 'validada'): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Esta receta ya tiene la cobertura <strong>validada</strong>.
</div>
<?php elseif ($receta['estado'] === 'rechazada'): ?>
<div class="alert alert-danger">
    <i class="fas fa-times-circle"></i> Esta receta ha sido <strong>rechazada</strong>.
</div>
<?php endif; ?>
