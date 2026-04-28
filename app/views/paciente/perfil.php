<div class="page-header">
    <h1><i class="fas fa-user"></i> Perfil del Paciente <small><?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre']); ?></small></h1>
    <div>
        <a href="/consulta/crear?paciente_id=<?php echo $paciente['id']; ?>" class="btn btn-primary"><i class="fas fa-stethoscope"></i> Nueva Consulta</a>
        <a href="/paciente/editar?id=<?php echo $paciente['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Datos del Paciente</h3>
        <?php if ($paciente['asegurado']): ?>
            <span class="badge badge-success">Asegurado</span>
        <?php else: ?>
            <span class="badge badge-danger">No asegurado</span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
            <div class="form-group">
                <label>CI</label>
                <p><strong><?php echo htmlspecialchars($paciente['ci']); ?></strong></p>
            </div>
            <div class="form-group">
                <label>Nombre</label>
                <p><?php echo htmlspecialchars($paciente['nombre']); ?></p>
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <p><?php echo htmlspecialchars($paciente['apellido']); ?></p>
            </div>
            <div class="form-group">
                <label>Fecha de Nacimiento</label>
                <p><?php echo date('d/m/Y', strtotime($paciente['fecha_nacimiento'])); ?></p>
            </div>
            <div class="form-group">
                <label>Sexo</label>
                <p><?php echo $paciente['sexo'] === 'M' ? 'Masculino' : 'Femenino'; ?></p>
            </div>
            <div class="form-group">
                <label>Telefono</label>
                <p><?php echo htmlspecialchars($paciente['telefono'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Direccion</label>
                <p><?php echo htmlspecialchars($paciente['direccion'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Nro. Asegurado</label>
                <p><?php echo htmlspecialchars($paciente['numero_asegurado'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Establecimiento</label>
                <p><?php echo htmlspecialchars($paciente['establecimiento_nombre'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($history['consultas'])): ?>
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3><i class="fas fa-notes-medical"></i> Historial de Consultas</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Profesional</th>
                        <th>Diagnostico</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history['consultas'] as $c): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($c['fecha_consulta'])); ?></td>
                        <td><?php echo htmlspecialchars(($c['profesional_nombre'] ?? '') . ' ' . ($c['profesional_apellido'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($c['diagnostico']); ?></td>
                        <td><span class="badge badge-<?php echo $c['estado'] === 'activa' ? 'success' : ($c['estado'] === 'cerrada' ? 'info' : 'danger'); ?>"><?php echo $c['estado']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($history['dispensaciones'])): ?>
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3><i class="fas fa-pills"></i> Historial de Dispensaciones</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Medicamento</th>
                        <th>Lote</th>
                        <th>Vencimiento</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history['dispensaciones'] as $d): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($d['fecha_dispensacion'])); ?></td>
                        <td><?php echo htmlspecialchars($d['medicamento_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($d['nro_lote']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($d['fecha_vencimiento'])); ?></td>
                        <td><?php echo $d['cantidad_dispensada']; ?></td>
                        <td><span class="badge badge-<?php echo $d['estado'] === 'completada' ? 'success' : 'warning'; ?>"><?php echo $d['estado']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div style="margin-top:15px;">
    <a href="/paciente/buscar" class="btn btn-secondary"><i class="fas fa-search"></i> Buscar otro paciente</a>
    <a href="/paciente" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>