<div class="page-header">
    <h1><i class="fas fa-boxes"></i> Stock de Medicamentos</h1>
    <div>
        <a href="/farmacia/ingreso" class="btn btn-success"><i class="fas fa-plus-circle"></i> Nuevo Ingreso</a>
        <a href="/farmacia/alertas" class="btn btn-warning"><i class="fas fa-bell"></i> Alertas</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="/farmacia/stock" class="form-inline" style="display:flex;gap:10px;margin-bottom:15px;">
            <input type="text" name="search" class="form-control" placeholder="Buscar medicamento..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="flex:1;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if (!empty($search)): ?>
                <a href="/farmacia/stock" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Principio Activo</th>
                        <th>Forma</th>
                        <th>Concentración</th>
                        <th>Lotes Disponibles</th>
                        <th>Stock Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stockPorMedicamento)): ?>
                        <tr><td colspan="7" class="text-center">No se encontraron medicamentos con stock disponible</td></tr>
                    <?php else: ?>
                        <?php foreach ($stockPorMedicamento as $med): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($med['medicamento_nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($med['principio_activo']); ?></td>
                            <td><?php echo htmlspecialchars($med['forma_farmaceutica']); ?></td>
                            <td><?php echo htmlspecialchars($med['concentracion'] . ' ' . $med['unidad_medida']); ?></td>
                            <td>
                                <?php foreach ($med['lotes'] as $lote): ?>
                                    <?php
                                    $clase = 'badge-success';
                                    if (strtotime($lote['fecha_vencimiento']) < strtotime('+30 days')) {
                                        $clase = 'badge-danger';
                                    } elseif (strtotime($lote['fecha_vencimiento']) < strtotime('+90 days')) {
                                        $clase = 'badge-warning';
                                    }
                                    ?>
                                    <span class="badge <?php echo $clase; ?>" title="Vence: <?php echo date('d/m/Y', strtotime($lote['fecha_vencimiento'])); ?>">
                                        Lote: <?php echo htmlspecialchars($lote['nro_lote']); ?> (<?php echo (int)$lote['cantidad']; ?> u.)
                                    </span><br>
                                <?php endforeach; ?>
                            </td>
                            <td><strong><?php echo $med['stock_total']; ?></strong> u.</td>
                            <td>
                                <a href="/farmacia/salida?medicamento_id=<?php echo $med['medicamento_id']; ?>" class="btn btn-sm btn-danger" title="Registrar Salida"><i class="fas fa-sign-out-alt"></i></a>
                                <a href="/farmacia/transferencia?medicamento_id=<?php echo $med['medicamento_id']; ?>" class="btn btn-sm btn-info" title="Transferir"><i class="fas fa-exchange-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>