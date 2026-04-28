<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard <small>Panel de Control</small></h1>
    <div>
        <span style="color:var(--gray);font-size:0.9rem;"><?php echo date('d/m/Y'); ?></span>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['pacientes_hoy'] ?? 0; ?></div>
            <div class="stat-label">Pacientes Registrados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-stethoscope"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['consultas_hoy'] ?? 0; ?></div>
            <div class="stat-label">Consultas Hoy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info"><i class="fas fa-pills"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['dispensaciones_hoy'] ?? 0; ?></div>
            <div class="stat-label">Dispensaciones Hoy</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning"><i class="fas fa-file-medical"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['recetas_pendientes'] ?? 0; ?></div>
            <div class="stat-label">Recetas Pendientes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['lotes_por_vencer'] ?? 0; ?></div>
            <div class="stat-label">Lotes por Vencer</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-danger"><i class="fas fa-box-open"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['stock_bajo'] ?? 0; ?></div>
            <div class="stat-label">Stock Bajo</div>
        </div>
    </div>
</div>

<div class="quick-actions" style="margin-bottom:25px;">
    <a href="/paciente/buscar" class="btn btn-primary"><i class="fas fa-search"></i> Buscar Paciente</a>
    <a href="/consulta/crear" class="btn btn-success"><i class="fas fa-stethoscope"></i> Nueva Consulta</a>
    <a href="/farmacia" class="btn btn-info"><i class="fas fa-pills"></i> Farmacia</a>
    <a href="/dispensacion/procesar" class="btn btn-warning"><i class="fas fa-hand-holding-medical"></i> Dispensar</a>
    <a href="/trazabilidad" class="btn btn-secondary"><i class="fas fa-link"></i> Trazabilidad</a>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-history"></i> Últimas Dispensaciones</h3></div>
        <div class="card-body">
            <?php if (empty($recentDispensations)): ?>
                <p class="text-muted">No hay dispensaciones recientes.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr><th>Fecha</th><th>Paciente</th><th>Medicamento</th><th>Lote</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentDispensations as $d): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($d['fecha_dispensacion'])); ?></td>
                            <td><?php echo htmlspecialchars(($d['paciente_nombre'] ?? '') . ' ' . ($d['paciente_apellido'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($d['medicamento_nombre'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($d['nro_lote'] ?? ''); ?></td>
                            <td><span class="badge badge-<?php echo ($d['estado'] ?? '') === 'completada' ? 'success' : 'warning'; ?>"><?php echo $d['estado'] ?? 'N/A'; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <?php if (!empty($expiringLots)): ?>
        <div class="card" style="margin-bottom:15px;">
            <div class="card-header" style="background:#fef9e7;"><h3><i class="fas fa-exclamation-triangle"></i> Lotes por Vencer</h3></div>
            <div class="card-body">
                <?php foreach (array_slice($expiringLots, 0, 5) as $l): ?>
                <div class="alert alert-warning" style="margin-bottom:8px;">
                    <strong><?php echo htmlspecialchars($l['medicamento_nombre'] ?? $l['nro_lote']); ?></strong><br>
                    <small>Vence: <?php echo date('d/m/Y', strtotime($l['fecha_vencimiento'])); ?> — Quedan: <?php echo $l['cantidad'] ?? 0; ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($lowStock)): ?>
        <div class="card">
            <div class="card-header" style="background:#fadbd8;"><h3><i class="fas fa-box-open"></i> Stock Bajo</h3></div>
            <div class="card-body">
                <?php foreach (array_slice($lowStock, 0, 5) as $ls): ?>
                <div class="alert alert-danger" style="margin-bottom:8px;">
                    <strong><?php echo htmlspecialchars($ls['medicamento_nombre'] ?? ''); ?></strong><br>
                    <small>Stock: <?php echo $ls['cantidad'] ?? 0; ?> unidades</small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>