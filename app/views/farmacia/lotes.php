<div class="page-header">
    <h1><i class="fas fa-layer-group"></i> Gestión de Lotes</h1>
    <div>
        <a href="/farmacia/lotes?filtro=proximo_vencer" class="btn btn-warning"><i class="fas fa-exclamation-triangle"></i> Próximos a Vencer</a>
        <a href="/farmacia/lotes?filtro=vencidos" class="btn btn-danger"><i class="fas fa-times-circle"></i> Vencidos</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div style="display:flex;gap:10px;margin-bottom:15px;flex-wrap:wrap;">
            <a href="/farmacia/lotes?filtro=todos" class="btn <?php echo ($filtro ?? 'todos') === 'todos' ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Todos</a>
            <a href="/farmacia/lotes?filtro=disponibles" class="btn <?php echo ($filtro ?? '') === 'disponibles' ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Disponibles</a>
            <a href="/farmacia/lotes?filtro=proximo_vencer" class="btn <?php echo ($filtro ?? '') === 'proximo_vencer' ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Próximos a Vencer</a>
            <a href="/farmacia/lotes?filtro=vencidos" class="btn <?php echo ($filtro ?? '') === 'vencidos' ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Vencidos</a>
            <a href="/farmacia/lotes?filtro=agotados" class="btn <?php echo ($filtro ?? '') === 'agotados' ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Agotados</a>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Principio Activo</th>
                        <th>Forma</th>
                        <th>Nro. Lote</th>
                        <th>Vencimiento</th>
                        <th>Cantidad</th>
                        <th>Cant. Original</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lotesPaginados['data'])): ?>
                        <tr><td colspan="9" class="text-center">No se encontraron lotes</td></tr>
                    <?php else: ?>
                        <?php foreach ($lotesPaginados['data'] as $lote): ?>
                            <?php
                            $hoy = time();
                            $vencimiento = strtotime($lote['fecha_vencimiento']);
                            $diasRestantes = (int)(($vencimiento - $hoy) / 86400);

                            $rowClass = '';
                            $badgeClass = 'badge-success';

                            if ($diasRestantes <= 0 && (int)$lote['cantidad'] > 0) {
                                $rowClass = 'style="background-color:rgba(220,53,69,0.1);"';
                                $badgeClass = 'badge-danger';
                            } elseif ($diasRestantes <= 30 && (int)$lote['cantidad'] > 0) {
                                $rowClass = 'style="background-color:rgba(255,193,7,0.15);"';
                                $badgeClass = 'badge-danger';
                            } elseif ($diasRestantes <= 90 && (int)$lote['cantidad'] > 0) {
                                $rowClass = 'style="background-color:rgba(255,193,7,0.08);"';
                                $badgeClass = 'badge-warning';
                            } elseif ($lote['estado'] === 'agotado' || (int)$lote['cantidad'] <= 0) {
                                $badgeClass = 'badge-secondary';
                            }
                            ?>
                        <tr <?php echo $rowClass; ?>>
                            <td><strong><?php echo htmlspecialchars($lote['medicamento_nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($lote['principio_activo']); ?></td>
                            <td><?php echo htmlspecialchars($lote['forma_farmaceutica']); ?></td>
                            <td><?php echo htmlspecialchars($lote['nro_lote']); ?></td>
                            <td>
                                <?php if ($diasRestantes <= 0 && (int)$lote['cantidad'] > 0): ?>
                                    <span class="badge badge-danger">
                                        Vencido - <?php echo date('d/m/Y', $vencimiento); ?>
                                    </span>
                                <?php elseif ($diasRestantes <= 30): ?>
                                    <span class="badge badge-danger">
                                        <?php echo date('d/m/Y', $vencimiento); ?> (<?php echo $diasRestantes; ?> días)
                                    </span>
                                <?php elseif ($diasRestantes <= 90): ?>
                                    <span class="badge badge-warning">
                                        <?php echo date('d/m/Y', $vencimiento); ?> (<?php echo $diasRestantes; ?> días)
                                    </span>
                                <?php else: ?>
                                    <?php echo date('d/m/Y', $vencimiento); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $cant = (int)$lote['cantidad'];
                                if ($cant <= 0) {
                                    echo '<span class="badge badge-secondary">Agotado</span>';
                                } elseif ($cant <= 10) {
                                    echo '<span class="badge badge-danger">' . $cant . '</span>';
                                } else {
                                    echo $cant;
                                }
                                ?>
                            </td>
                            <td><?php echo (int)$lote['cantidad_original']; ?></td>
                            <td>
                                <?php
                                $estados = [
                                    'disponible' => '<span class="badge badge-success">Disponible</span>',
                                    'agotado' => '<span class="badge badge-secondary">Agotado</span>',
                                    'vencido' => '<span class="badge badge-danger">Vencido</span>'
                                ];
                                echo $estados[$lote['estado']] ?? '<span class="badge">' . htmlspecialchars($lote['estado']) . '</span>';
                                ?>
                            </td>
                            <td>
                                <a href="/farmacia/salida?lote_id=<?php echo $lote['id']; ?>" class="btn btn-sm btn-danger" title="Registrar Salida"><i class="fas fa-sign-out-alt"></i></a>
                                <a href="/farmacia/transferencia?lote_id=<?php echo $lote['id']; ?>" class="btn btn-sm btn-info" title="Transferir"><i class="fas fa-exchange-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($lotesPaginados['pages'] ?? 1) > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $lotesPaginados['pages']; $i++): ?>
                <?php if ($i == $lotesPaginados['page']): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="/farmacia/lotes?filtro=<?php echo urlencode($filtro ?? 'todos'); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>