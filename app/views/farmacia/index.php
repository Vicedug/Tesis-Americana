<div class="page-header">
    <h1><i class="fas fa-clinic-medical"></i> Panel de Farmacia</h1>
    <div>
        <a href="/farmacia/ingreso" class="btn btn-success"><i class="fas fa-plus-circle"></i> Nuevo Ingreso</a>
        <a href="/farmacia/alertas" class="btn btn-warning"><i class="fas fa-bell"></i> Alertas</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--primary-color);">
            <i class="fas fa-pills"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalMedicamentos; ?></h3>
            <p>Items en Stock</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--success-color);">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stockTotal; ?></h3>
            <p>Unidades Totales</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--warning-color);">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo count($lotesProximosFiltrados); ?></h3>
            <p>Lotes por Vencer</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--danger-color);">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo count($stockBajoFiltrado); ?></h3>
            <p>Stock Bajo</p>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-exclamation-triangle"></i> Lotes Próximos a Vencer</h3>
        </div>
        <div class="card-body">
            <?php if (empty($lotesProximosFiltrados)): ?>
                <p class="text-center">No hay lotes próximos a vencer</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Lote</th>
                            <th>Vencimiento</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($lotesProximosFiltrados, 0, 5) as $lote): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lote['medicamento_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($lote['nro_lote']); ?></td>
                            <td>
                                <?php
                                $diasRestantes = (int)((strtotime($lote['fecha_vencimiento']) - time()) / 86400);
                                $clase = $diasRestantes <= 30 ? 'badge-danger' : 'badge-warning';
                                ?>
                                <span class="badge <?php echo $clase; ?>">
                                    <?php echo date('d/m/Y', strtotime($lote['fecha_vencimiento'])); ?>
                                    (<?php echo $diasRestantes; ?> días)
                                </span>
                            </td>
                            <td><?php echo (int)$lote['cantidad']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($lotesProximosFiltrados) > 5): ?>
                    <div style="text-align:center;margin-top:10px;">
                        <a href="/farmacia/lotes?filtro=proximo_vencer" class="btn btn-sm btn-warning">Ver todos (<?php echo count($lotesProximosFiltrados); ?>)</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-arrow-down"></i> Stock Bajo</h3>
        </div>
        <div class="card-body">
            <?php if (empty($stockBajoFiltrado)): ?>
                <p class="text-center">No hay medicamentos con stock bajo</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Lote</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($stockBajoFiltrado, 0, 5) as $lote): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lote['medicamento_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($lote['nro_lote']); ?></td>
                            <td><span class="badge badge-danger"><?php echo (int)$lote['cantidad']; ?></span></td>
                            <td><?php echo ucfirst($lote['estado']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($stockBajoFiltrado) > 5): ?>
                    <div style="text-align:center;margin-top:10px;">
                        <a href="/farmacia/alertas" class="btn btn-sm btn-danger">Ver todos (<?php echo count($stockBajoFiltrado); ?>)</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($movimientosRecientes['data'])): ?>
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3><i class="fas fa-history"></i> Movimientos Recientes</h3>
    </div>
    <div class="card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Medicamento</th>
                    <th>Cantidad</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientosRecientes['data'] as $mov): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha_movimiento'])); ?></td>
                    <td>
                        <?php
                        $tipos = [
                            'entrada' => '<span class="badge badge-success">Entrada</span>',
                            'salida' => '<span class="badge badge-danger">Salida</span>',
                            'transferencia' => '<span class="badge badge-info">Transferencia</span>'
                        ];
                        echo $tipos[$mov['tipo']] ?? $mov['tipo'];
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($mov['medicamento_nombre']); ?></td>
                    <td><?php echo (int)$mov['cantidad']; ?></td>
                    <td>
                        <?php if ($mov['tipo'] === 'transferencia'): ?>
                            <?php echo htmlspecialchars($mov['origen_nombre'] ?? 'N/A'); ?> → <?php echo htmlspecialchars($mov['destino_nombre'] ?? 'N/A'); ?>
                        <?php elseif ($mov['tipo'] === 'salida'): ?>
                            <?php echo htmlspecialchars($mov['motivo']); ?>
                        <?php else: ?>
                            <?php echo htmlspecialchars($mov['motivo']); ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>