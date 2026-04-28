<div class="page-header">
    <h1><i class="fas fa-exchange-alt"></i> Transferencia entre Establecimientos</h1>
    <div>
        <a href="/farmacia/stock" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Stock</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="/farmacia/guardar-transferencia" method="POST" id="form-transferencia">
            <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group" style="grid-column:1/3;">
                    <label for="lote_id">Medicamento / Lote a transferir *</label>
                    <select id="lote_id" name="lote_id" class="form-control" required>
                        <option value="">Seleccionar lote...</option>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?php echo $lote['id']; ?>"
                                data-cantidad="<?php echo (int)$lote['cantidad']; ?>"
                                data-medicamento="<?php echo htmlspecialchars($lote['medicamento_nombre']); ?>">
                                <?php echo htmlspecialchars($lote['medicamento_nombre'] . ' (' . $lote['principio_activo'] . ')'); ?>
                                - Lote: <?php echo htmlspecialchars($lote['nro_lote']); ?>
                                - Stock disponible: <?php echo (int)$lote['cantidad']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small id="stock_info" class="form-text" style="color:var(--info-color);display:none;"></small>
                </div>

                <div class="form-group">
                    <label for="establecimiento_destino_id">Establecimiento Destino *</label>
                    <select id="establecimiento_destino_id" name="establecimiento_destino_id" class="form-control" required>
                        <option value="">Seleccionar establecimiento...</option>
                        <?php foreach ($establecimientos as $est): ?>
                            <option value="<?php echo $est['id']; ?>">
                                <?php echo htmlspecialchars($est['nombre'] . ' (' . ucfirst($est['tipo']) . ' - ' . $est['ciudad'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad a transferir *</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" required min="1" placeholder="Cantidad">
                </div>

                <div class="form-group" style="grid-column:1/3;">
                    <label for="motivo">Motivo de la transferencia</label>
                    <textarea id="motivo" name="motivo" class="form-control" rows="3" placeholder="Razón de la transferencia (opcional)">transferencia</textarea>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-info"><i class="fas fa-exchange-alt"></i> Registrar Transferencia</button>
                <a href="/farmacia/stock" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loteSelect = document.getElementById('lote_id');
    const cantidadInput = document.getElementById('cantidad');
    const stockInfo = document.getElementById('stock_info');

    loteSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const cantidadMax = selected.dataset.cantidad;
            const medicamento = selected.dataset.medicamento;
            cantidadInput.max = cantidadMax;
            stockInfo.textContent = 'Medicamento: ' + medicamento + ' | Stock disponible: ' + cantidadMax + ' unidades';
            stockInfo.style.display = 'block';
        } else {
            cantidadInput.max = '';
            stockInfo.style.display = 'none';
        }
    });
});
</script>