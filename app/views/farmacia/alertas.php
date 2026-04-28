<div class="page-header">
    <h1><i class="fas fa-bell"></i> Alertas de Farmacia</h1>
    <div>
        <a href="/farmacia/ingreso" class="btn btn-success"><i class="fas fa-plus-circle"></i> Registrar Ingreso</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--danger-color);">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo count($lotesVencidos); ?></h3>
            <p>Lotes Vencidos</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--warning-color);">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo count($lotesProximosFiltrados); ?></h3>
            <p>Próximos a Vencer</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e67e22;">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo count($stockBajoFiltrado); ?></h3>
            <p>Stock Bajo</p>
        </div>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3><i class="fas fa-times-circle" style="color:var(--danger-color);"></i> Lotes Vencidos</h3>
    </div>
    <div class="card-body">
        <?php if (empty($lotesVencidos)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> No hay lotes vencidos.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Principio Activo</th>
                            <th>Forma</th>
                            <th>Nro. Lote</th>
                            <th>Fecha Vencimiento</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lotesVencidos as $lote): ?>
                        <tr style="background-color:rgba(220,53,69,0.1);">
                            <td><strong><?php echo htmlspecialchars($lote['medicamento_nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($lote['principio_activo']); ?></td>
                            <td><?php echo htmlspecialchars($lote['forma_farmaceutica']); ?></td>
                            <td><?php echo htmlspecialchars($lote['nro_lote']); ?></td>
                            <td>
                                <span class="badge badge-danger">
                                    Vencido el <?php echo date('d/m/Y', strtotime($lote['fecha_vencimiento'])); ?>
                                </span>
                            </td>
                            <td><span class="badge badge-danger"><?php echo (int)$lote['cantidad']; ?></span></td>
                            <td>
                                <a href="/farmacia/salida?lote_id=<?php echo $lote['id']; ?>" class="btn btn-sm btn-danger" title="Registrar Salida (Deterioro)"><i class="fas fa-sign-out-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3><i class="fas fa-exclamation-triangle" style="color:var(--warning-color);"></i> Lotes Próximos a Vencer (<?php echo $diasVencimiento; ?> días)</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="/farmacia/alertas" style="margin-bottom:15px;display:flex;gap:10px;align-items:center;">
            <label for="dias" style="white-space:nowrap;">Días de anticipación:</label>
            <input type="number" id="dias" name="dias" class="form-control" value="<?php echo $diasVencimiento; ?>" min="1" max="365" style="width:100px;">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
        </form>

        <?php if (empty($lotesProximosFiltrados)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> No hay lotes próximos a vencer en los próximos <?php echo $diasVencimiento; ?> días.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Principio Activo</th>
                            <th>Nro. Lote</th>
                            <th>Fecha Vencimiento</th>
                            <th>Días Restantes</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lotesProximosFiltrados as $lote): ?>
                            <?php
                            $diasRestantes = (int)((strtotime($lote['fecha_vencimiento']) - time()) / 86400);
                            $badgeClass = $diasRestantes <= 30 ? 'badge-danger' : 'badge-warning';
                            ?>
                        <tr style="background-color:<?php echo $diasRestantes <= 30 ? 'rgba(220,53,69,0.1)' : 'rgba(255,193,7,0.08)'; ?>;">
                            <td><strong><?php echo htmlspecialchars($lote['medicamento_nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($lote['principio_activo']); ?></td>
                            <td><?php echo htmlspecialchars($lote['nro_lote']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($lote['fecha_vencimiento'])); ?></td>
                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $diasRestantes; ?> días</span></td>
                            <td><?php echo (int)$lote['cantidad']; ?></td>
                            <td>
                                <a href="/farmacia/salida?lote_id=<?php echo $lote['id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-sign-out-alt"></i></a>
                                <a href="/farmacia/transferencia?lote_id=<?php echo $lote['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-exchange-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <h3><i class="fas fa-arrow-down" style="color:#e67e22;"></i> Stock Bajo (umbral: <?php echo $umbralStock; ?> unidades)</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="/farmacia/alertas" style="margin-bottom:15px;display:flex;gap:10px;align-items:center;">
            <label for="umbral" style="white-space:nowrap;">Umbral de stock bajo:</label>
            <input type="number" id="umbral" name="umbral" class="form-control" value="<?php echo $umbralStock; ?>" min="1" max="100" style="width:100px;">
            <input type="hidden" name="dias" value="<?php echo $diasVencimiento; ?>">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
        </form>

        <?php if (empty($stockBajoFiltrado)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> No hay medicamentos con stock por debajo del umbral de <?php echo $umbralStock; ?> unidades.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Principio Activo</th>
                            <th>Forma</th>
                            <th>Nro. Lote</th>
                            <th>Cantidad</th>
                            <th>Establecimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stockBajoFiltrado as $lote): ?>
                        <tr style="background-color:rgba(230,126,34,0.1);">
                            <td><strong><?php echo htmlspecialchars($lote['medicamento_nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($lote['principio_activo']); ?></td>
                            <td><?php echo htmlspecialchars($lote['forma_farmaceutica']); ?></td>
                            <td><?php echo htmlspecialchars($lote['nro_lote']); ?></td>
                            <td><span class="badge badge-danger"><?php echo (int)$lote['cantidad']; ?></span></td>
                            <td><?php echo htmlspecialchars($lote['establecimiento_nombre']); ?></td>
                            <td>
                                <a href="/farmacia/ingreso?medicamento_id=<?php echo $lote['medicamento_id']; ?>" class="btn btn-sm btn-success" title="Registrar Ingreso"><i class="fas fa-plus-circle"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>