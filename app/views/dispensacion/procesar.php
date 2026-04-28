<div class="page-header">
    <h1><i class="fas fa-hand-holding-medical"></i> Procesar Dispensación <small>Fase 5: Entrega de medicamentos</small></h1>
</div>

<?php if ($receta): ?>
<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="background:#d5f5e3;"><h3><i class="fas fa-check-circle"></i> Receta encontrada - <?php echo htmlspecialchars($receta['codigo_receta']); ?></h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;">
            <div><strong>Paciente:</strong> <?php echo htmlspecialchars(($receta['paciente_nombre'] ?? '') . ' ' . ($receta['paciente_apellido'] ?? '')); ?></div>
            <div><strong>CI:</strong> <?php echo htmlspecialchars($receta['paciente_ci'] ?? ''); ?></div>
            <div><strong>Estado:</strong> <span class="badge badge-success"><?php echo $receta['estado'] ?? 'Validada'; ?></span></div>
            <div><strong>Diagnóstico:</strong> <?php echo htmlspecialchars($receta['diagnostico'] ?? ''); ?></div>
            <div><strong>Profesional:</strong> <?php echo htmlspecialchars($receta['profesional_nombre'] ?? ''); ?></div>
            <div><strong>Cobertura:</strong> <?php echo ($receta['cobertura_validada'] ?? 0) ? '<span class="badge badge-success">Validada</span>' : '<span class="badge badge-warning">Pendiente</span>'; ?></div>
        </div>
    </div>
</div>

<form action="/dispensacion/guardar" method="POST" id="form-dispensacion">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
    <input type="hidden" name="receta_id" value="<?php echo $receta['id']; ?>">
    <input type="hidden" name="paciente_id" value="<?php echo $receta['paciente_id'] ?? ''; ?>">

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h3>Medicamentos a Dispensar</h3></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Cantidad Prescrita</th>
                            <th>Indicaciones</th>
                            <th>Lote Disponible</th>
                            <th>Cantidad a Dispensar</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($medicamentosReceta)): ?>
                            <?php foreach ($medicamentosReceta as $i => $med): ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($med['medicamento_nombre'] ?? $med['nombre'] ?? ''); ?>
                                    <input type="hidden" name="medicamentos[<?php echo $i; ?>][medicamento_id]" value="<?php echo $med['medicamento_id'] ?? $med['id'] ?? ''; ?>">
                                </td>
                                <td><?php echo $med['cantidad_prescrita'] ?? 0; ?></td>
                                <td><?php echo htmlspecialchars($med['indicaciones'] ?? ''); ?></td>
                                <td>
                                    <select name="medicamentos[<?php echo $i; ?>][lote_id]" class="form-control" required>
                                        <option value="">Seleccionar lote</option>
                                    </select>
                                    <button type="button" onclick="loadLotes(<?php echo $i; ?>, <?php echo $med['medicamento_id'] ?? $med['id'] ?? 0; ?>)" class="btn btn-sm btn-info" style="margin-top:5px;"><i class="fas fa-search"></i> Buscar lotes</button>
                                </td>
                                <td>
                                    <input type="number" name="medicamentos[<?php echo $i; ?>][cantidad]" class="form-control" min="1" max="<?php echo $med['cantidad_prescrita'] ?? 1; ?>" value="<?php echo $med['cantidad_prescrita'] ?? 1; ?>" required style="width:80px;">
                                </td>
                                <td>
                                    <input type="text" name="medicamentos[<?php echo $i; ?>][observaciones]" class="form-control" placeholder="Observaciones" style="width:150px;">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No hay medicamentos en esta receta</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check-circle"></i> Confirmar Dispensación</button>
        <a href="/dispensacion" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
    </div>
</form>

<script>
function loadLotes(index, medicamentoId) {
    fetch('/dispensacion/getLotesAjax?medicamento_id=' + medicamentoId)
        .then(res => res.json())
        .then(data => {
            const select = document.querySelector(`select[name="medicamentos[${index}][lote_id]"]`);
            select.innerHTML = '<option value="">Seleccionar lote</option>';
            data.forEach(lote => {
                select.innerHTML += `<option value="${lote.id}">${lote.nro_lote} - Vto: ${lote.fecha_vencimiento} (Stock: ${lote.cantidad})</option>`;
            });
        });
}
</script>

<?php else: ?>
<div class="card">
    <div class="card-header"><h3>Buscar Receta Validada</h3></div>
    <div class="card-body">
        <p class="text-muted">Ingrese el número de receta o busque por paciente para iniciar la dispensación.</p>
        <div style="display:flex;gap:10px;margin-bottom:20px;">
            <a href="/receta?estado=validada" class="btn btn-primary"><i class="fas fa-file-medical"></i> Ver Recetas Validadas</a>
            <a href="/paciente/buscar" class="btn btn-info"><i class="fas fa-search"></i> Buscar Paciente</a>
        </div>
    </div>
</div>
<?php endif; ?>