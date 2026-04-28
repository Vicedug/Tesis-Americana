<div class="page-header">
    <h1><i class="fas fa-stethoscope"></i> Detalle de Consulta <small><?php echo date('d/m/Y H:i', strtotime($consulta['fecha_consulta'])); ?></small></h1>
    <div>
        <?php if ($consulta['estado'] === 'activa'): ?>
            <a href="/receta/crear?consulta_id=<?php echo $consulta['id']; ?>" class="btn btn-primary"><i class="fas fa-prescription"></i> Crear Receta</a>
        <?php endif; ?>
        <a href="/consulta" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3><i class="fas fa-info-circle"></i> Información de la Consulta</h3>
        <?php
        $estadoClass = $consulta['estado'] === 'activa' ? 'success' : ($consulta['estado'] === 'cerrada' ? 'info' : 'danger');
        ?>
        <span class="badge badge-<?php echo $estadoClass; ?>"><?php echo ucfirst($consulta['estado']); ?></span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
            <div class="form-group">
                <label>Paciente</label>
                <p><strong><?php echo htmlspecialchars(($consulta['paciente_apellido'] ?? '') . ', ' . ($consulta['paciente_nombre'] ?? '')); ?></strong></p>
            </div>
            <div class="form-group">
                <label>CI del Paciente</label>
                <p><?php echo htmlspecialchars($consulta['paciente_ci'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Seguro Médico</label>
                <p>
                    <?php if (!empty($consulta['paciente_asegurado'])): ?>
                        <span class="badge badge-success">Asegurado - <?php echo htmlspecialchars($consulta['numero_asegurado'] ?? 'N/A'); ?></span>
                    <?php else: ?>
                        <span class="badge badge-danger">No asegurado</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="form-group">
                <label>Profesional</label>
                <p><?php echo htmlspecialchars(($consulta['profesional_nombre'] ?? '') . ' ' . ($consulta['profesional_apellido'] ?? '')); ?></p>
            </div>
            <div class="form-group">
                <label>Establecimiento</label>
                <p><?php echo htmlspecialchars($consulta['establecimiento_nombre'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Fecha de Consulta</label>
                <p><?php echo date('d/m/Y H:i', strtotime($consulta['fecha_consulta'])); ?></p>
            </div>
        </div>

        <div style="margin-top:20px;border-top:1px solid #eee;padding-top:20px;">
            <div class="form-group">
                <label><strong>Diagnóstico</strong></label>
                <p style="background:#f8f9fa;padding:15px;border-radius:5px;"><?php echo nl2br(htmlspecialchars($consulta['diagnostico'])); ?></p>
            </div>
        </div>

        <?php if (!empty($consulta['observaciones'])): ?>
        <div style="margin-top:15px;">
            <div class="form-group">
                <label><strong>Observaciones</strong></label>
                <p style="background:#fff3cd;padding:15px;border-radius:5px;"><?php echo nl2br(htmlspecialchars($consulta['observaciones'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($recetas)): ?>
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-prescription"></i> Recetas Asociadas</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Fecha Emisión</th>
                        <th>Estado</th>
                        <th>Cobertura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recetas as $r): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($r['codigo_receta']); ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($r['fecha_emision'])); ?></td>
                        <td>
                            <?php
                            $recEstadoClass = $r['estado'] === 'emitida' ? 'warning' : ($r['estado'] === 'validada' ? 'success' : 'danger');
                            ?>
                            <span class="badge badge-<?php echo $recEstadoClass; ?>"><?php echo ucfirst($r['estado']); ?></span>
                        </td>
                        <td>
                            <?php if (!empty($r['cobertura_validada'])): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Validada</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/receta/detalle?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($consulta['estado'] === 'activa'): ?>
<div class="card" style="border-left:4px solid #dc3545;">
    <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h4 style="margin:0;color:#dc3545;"><i class="fas fa-exclamation-triangle"></i> Cerrar Consulta</h4>
            <p style="margin:5px 0 0;color:#666;">Al cerrar la consulta no se podrán agregar más recetas.</p>
        </div>
        <form action="/consulta/cerrar" method="POST" onsubmit="return confirm('¿Está seguro de cerrar esta consulta?')">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="id" value="<?php echo $consulta['id']; ?>">
            <button type="submit" class="btn btn-danger"><i class="fas fa-lock"></i> Cerrar Consulta</button>
        </form>
    </div>
</div>
<?php endif; ?>
