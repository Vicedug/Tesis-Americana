<div class="page-header">
    <h1><i class="fas fa-link"></i> Trazabilidad <small>Monitoreo integral de medicamentos</small></h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary"><i class="fas fa-link"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['total_trazabilidades'] ?? 0; ?></div>
            <div class="stat-label">Registros de Trazabilidad</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['total_pacientes'] ?? 0; ?></div>
            <div class="stat-label">Pacientes Registrados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info"><i class="fas fa-pills"></i></div>
        <div>
            <div class="stat-value"><?php echo $stats['total_dispensaciones_hoy'] ?? 0; ?></div>
            <div class="stat-label">Dispensaciones Hoy</div>
        </div>
    </div>
</div>

<div class="quick-actions">
    <a href="/trazabilidad/seguimiento" class="btn btn-primary"><i class="fas fa-search"></i> Seguimiento</a>
    <a href="/trazabilidad/historial" class="btn btn-success"><i class="fas fa-history"></i> Historial</a>
    <a href="/trazabilidad/alertas" class="btn btn-warning"><i class="fas fa-bell"></i> Alertas</a>
    <a href="/reportes/trazabilidad" class="btn btn-info"><i class="fas fa-file-alt"></i> Reporte</a>
</div>