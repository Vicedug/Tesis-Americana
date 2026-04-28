<div class="page-header">
    <h1><i class="fas fa-check-circle"></i> Confirmación de Dispensación</h1>
</div>

<?php if ($dispensacion): ?>
<div class="card" style="border:2px solid #27ae60;margin-bottom:20px;">
    <div class="card-header" style="background:#d5f5e3;">
        <h3><i class="fas fa-check-circle"></i> Dispensación Registrada Exitosamente</h3>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
            <div class="form-group">
                <label>Paciente</label>
                <p><strong><?php echo htmlspecialchars(($dispensacion['paciente_nombre'] ?? '') . ' ' . ($dispensacion['paciente_apellido'] ?? '')); ?></strong></p>
            </div>
            <div class="form-group">
                <label>CI</label>
                <p><?php echo htmlspecialchars($dispensacion['paciente_ci'] ?? ''); ?></p>
            </div>
            <div class="form-group">
                <label>Fecha Dispensación</label>
                <p><?php echo date('d/m/Y H:i', strtotime($dispensacion['fecha_dispensacion'])); ?></p>
            </div>
            <div class="form-group">
                <label>Medicamento</label>
                <p><strong><?php echo htmlspecialchars($dispensacion['medicamento_nombre'] ?? ''); ?></strong></p>
            </div>
            <div class="form-group">
                <label>Lote</label>
                <p><?php echo htmlspecialchars($dispensacion['nro_lote'] ?? ''); ?></p>
            </div>
            <div class="form-group">
                <label>Vencimiento</label>
                <p><?php echo date('d/m/Y', strtotime($dispensacion['fecha_vencimiento'] ?? '')); ?></p>
            </div>
            <div class="form-group">
                <label>Cantidad</label>
                <p><?php echo $dispensacion['cantidad_dispensada'] ?? ''; ?></p>
            </div>
            <div class="form-group">
                <label>Farmacéutico</label>
                <p><?php echo htmlspecialchars(($dispensacion['farmaceutico_nombre'] ?? '') . ' ' . ($dispensacion['farmaceutico_apellido'] ?? '')); ?></p>
            </div>
            <div class="form-group">
                <label>Establecimiento</label>
                <p><?php echo htmlspecialchars($dispensacion['establecimiento_nombre'] ?? ''); ?></p>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <p><span class="badge badge-success"><?php echo $dispensacion['estado'] ?? 'completada'; ?></span></p>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:10px;">
    <a href="/trazabilidad/detalle?id=<?php echo $dispensacion['id']; ?>" class="btn btn-primary"><i class="fas fa-link"></i> Ver Trazabilidad</a>
    <a href="/dispensacion/procesar" class="btn btn-success"><i class="fas fa-plus"></i> Nueva Dispensación</a>
    <a href="/dispensacion" class="btn btn-secondary"><i class="fas fa-list"></i> Listado</a>
</div>
<?php else: ?>
<div class="alert alert-warning">No se encontró la información de la dispensación.</div>
<a href="/dispensacion" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
<?php endif; ?>