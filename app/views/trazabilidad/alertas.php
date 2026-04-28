<div class="page-header">
    <h1><i class="fas fa-bell"></i> Alertas del Sistema</h1>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="background:#fadbd8;"><h3><i class="fas fa-exclamation-triangle"></i> Lotes Próximos a Vencer</h3></div>
    <div class="card-body">
        <?php if (empty($vencimientoAlertas)): ?>
            <p class="text-muted">No hay alertas de vencimiento.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>Medicamento</th><th>Lote</th><th>Vencimiento</th><th>Cantidad</th><th>Establecimiento</th><th>Prioridad</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($vencimientoAlertas as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['medicamento_nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($a['nro_lote'] ?? ''); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($a['fecha_vencimiento'] ?? '')); ?></td>
                        <td><?php echo $a['cantidad'] ?? 0; ?></td>
                        <td><?php echo htmlspecialchars($a['establecimiento_nombre'] ?? ''); ?></td>
                        <td><span class="badge badge-<?php echo ($a['prioridad'] ?? '') === 'alta' ? 'danger' : 'warning'; ?>"><?php echo $a['prioridad'] ?? 'media'; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header" style="background:#d6eaf8;"><h3><i class="fas fa-box-open"></i> Stock Bajo</h3></div>
    <div class="card-body">
        <?php if (empty($stockBajoAlertas)): ?>
            <p class="text-muted">No hay alertas de stock bajo.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>Medicamento</th><th>Establecimiento</th><th>Prioridad</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($stockBajoAlertas as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['medicamento_nombre'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($a['establecimiento_nombre'] ?? ''); ?></td>
                        <td><span class="badge badge-warning"><?php echo $a['prioridad'] ?? 'media'; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top:15px;">
    <a href="/trazabilidad" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>