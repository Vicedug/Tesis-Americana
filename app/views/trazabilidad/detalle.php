<div class="page-header">
    <h1><i class="fas fa-link"></i> Detalle de Trazabilidad <small>Cadena completa</small></h1>
</div>

<?php if ($trace): ?>
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h3>Cadena de Trazabilidad — Fases del Flujograma</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <!-- Fase 1 -->
            <div class="card" style="border:2px solid #3498db;">
                <div class="card-header" style="background:#d6eaf8;"><strong><i class="fas fa-user"></i> Fase 1: Identificación del Paciente</strong></div>
                <div class="card-body">
                    <p><strong>CI:</strong> <?php echo htmlspecialchars($trace['paciente_ci'] ?? ''); ?></p>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars(($trace['paciente_nombre'] ?? '') . ' ' . ($trace['paciente_apellido'] ?? '')); ?></p>
                    <p><strong>Asegurado:</strong> <?php echo ($trace['asegurado'] ?? 0) ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>'; ?></p>
                </div>
            </div>

            <!-- Fase 2 -->
            <div class="card" style="border:2px solid #27ae60;">
                <div class="card-header" style="background:#d5f5e3;"><strong><i class="fas fa-stethoscope"></i> Fase 2: Registro de Atención Médica</strong></div>
                <div class="card-body">
                    <p><strong>Diagnóstico:</strong> <?php echo htmlspecialchars($trace['diagnostico'] ?? 'N/A'); ?></p>
                    <p><strong>Profesional:</strong> <?php echo htmlspecialchars($trace['profesional_nombre'] ?? 'N/A'); ?></p>
                    <p><strong>Fecha Consulta:</strong> <?php echo isset($trace['fecha_consulta']) ? date('d/m/Y H:i', strtotime($trace['fecha_consulta'])) : 'N/A'; ?></p>
                </div>
            </div>

            <!-- Fase 3 -->
            <div class="card" style="border:2px solid #f39c12;">
                <div class="card-header" style="background:#fef9e7;"><strong><i class="fas fa-file-medical"></i> Fase 3: Validación de Receta</strong></div>
                <div class="card-body">
                    <p><strong>Código Receta:</strong> <?php echo htmlspecialchars($trace['codigo_receta'] ?? 'N/A'); ?></p>
                    <p><strong>Cobertura:</strong> <?php echo ($trace['cobertura_validada'] ?? 0) ? '<span class="badge badge-success">Validada</span>' : '<span class="badge badge-warning">Pendiente</span>'; ?></p>
                    <p><strong>Estado Receta:</strong> <span class="badge badge-success"><?php echo $trace['receta_estado'] ?? 'N/A'; ?></span></p>
                </div>
            </div>

            <!-- Fase 4 -->
            <div class="card" style="border:2px solid #9b59b6;">
                <div class="card-header" style="background:#f4ecf7;"><strong><i class="fas fa-pills"></i> Fase 4: Gestión de Farmacia</strong></div>
                <div class="card-body">
                    <p><strong>Medicamento:</strong> <?php echo htmlspecialchars($trace['medicamento_nombre'] ?? 'N/A'); ?></p>
                    <p><strong>Forma:</strong> <?php echo htmlspecialchars($trace['forma_farmaceutica'] ?? 'N/A'); ?></p>
                    <p><strong>Concentración:</strong> <?php echo htmlspecialchars($trace['concentracion'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <!-- Fase 5 -->
            <div class="card" style="border:2px solid #e74c3c;">
                <div class="card-header" style="background:#fadbd8;"><strong><i class="fas fa-hand-holding-medical"></i> Fase 5: Dispensación</strong></div>
                <div class="card-body">
                    <p><strong>Lote:</strong> <?php echo htmlspecialchars($trace['nro_lote'] ?? 'N/A'); ?></p>
                    <p><strong>Vencimiento:</strong> <?php echo isset($trace['fecha_vencimiento']) ? date('d/m/Y', strtotime($trace['fecha_vencimiento'])) : 'N/A'; ?></p>
                    <p><strong>Cantidad Dispensada:</strong> <?php echo $trace['cantidad_dispensada'] ?? 'N/A'; ?></p>
                    <p><strong>Fecha Dispensación:</strong> <?php echo isset($trace['fecha_dispensacion']) ? date('d/m/Y H:i', strtotime($trace['fecha_dispensacion'])) : 'N/A'; ?></p>
                    <p><strong>Farmacéutico:</strong> <?php echo htmlspecialchars($trace['farmaceutico_nombre'] ?? 'N/A'); ?></p>
                    <p><strong>Establecimiento:</strong> <?php echo htmlspecialchars($trace['establecimiento_nombre'] ?? 'N/A'); ?></p>
                </div>
            </div>

            <!-- Fase 6 -->
            <div class="card" style="border:2px solid #1a5276;">
                <div class="card-header" style="background:#d4e6f1;"><strong><i class="fas fa-check-circle"></i> Fase 6: Trazabilidad Completa</strong></div>
                <div class="card-body">
                    <p><strong>Estado:</strong> <span class="badge badge-success">Completa</span></p>
                    <p><strong>Fecha Registro:</strong> <?php echo isset($trace['fecha_registro']) ? date('d/m/Y H:i', strtotime($trace['fecha_registro'])) : 'N/A'; ?></p>
                    <p><strong>Observaciones:</strong> <?php echo htmlspecialchars($trace['observaciones'] ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> No se encontró el registro de trazabilidad.</div>
<?php endif; ?>

<div style="margin-top:15px;">
    <a href="/trazabilidad" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>