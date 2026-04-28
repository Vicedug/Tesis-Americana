<div class="page-header">
    <h1><i class="fas fa-sign-out-alt"></i> Registrar Salida de Stock</h1>
    <div>
        <a href="/farmacia/stock" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Stock</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="/farmacia/guardar-salida" method="POST" id="form-salida">
            <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group" style="grid-column:1/3;">
                    <label for="lote_id">Medicamento / Lote *</label>
                    <select id="lote_id" name="lote_id" class="form-control" required>
                        <option value="">Seleccionar lote...</option>
                        <?php foreach ($lotes as $lote): ?>
                            <option value="<?php echo $lote['id']; ?>"
                                data-cantidad="<?php echo (int)$lote['cantidad']; ?>"
                                data-vencimiento="<?php echo $lote['fecha_vencimiento']; ?>">
                                <?php echo htmlspecialchars($lote['medicamento_nombre'] . ' (' . $lote['principio_activo'] . ')'); ?>
                                - Lote: <?php echo htmlspecialchars($lote['nro_lote']); ?>
                                - Stock: <?php echo (int)$lote['cantidad']; ?>
                                - Vence: <?php echo date('d/m/Y', strtotime($lote['fecha_vencimiento'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad a extraer *</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" required min="1" placeholder="Cantidad">
                    <small id="cantidad_max" class="form-text" style="color:var(--info-color);display:none;">Stock disponible: <span id="stock_disponible"></span> unidades</small>
                </div>

                <div class="form-group">
                    <label for="motivo">Motivo de la salida *</label>
                    <select id="motivo" name="motivo" class="form-control" required>
                        <option value="">Seleccionar motivo...</option>
                        <option value="dispensacion">Dispensación a paciente</option>
                        <option value="ajuste">Ajuste de inventario</option>
                        <option value="deterioro">Deterioro / Vencimiento</option>
                        <option value="devolucion">Devolución a proveedor</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div class="form-group" id="grupo_referencia" style="display:none;">
                    <label for="referencia_id">ID de Referencia (dispensación, etc.)</label>
                    <input type="number" id="referencia_id" name="referencia_id" class="form-control" min="0" placeholder="Opcional">
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> Registrar Salida</button>
                <a href="/farmacia/stock" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loteSelect = document.getElementById('lote_id');
    const cantidadInput = document.getElementById('cantidad');
    const stockSpan = document.getElementById('stock_disponible');
    const stockSmall = document.getElementById('cantidad_max');
    const motivoSelect = document.getElementById('motivo');
    const grupoReferencia = document.getElementById('grupo_referencia');

    loteSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const cantidadMax = selected.dataset.cantidad;
            cantidadInput.max = cantidadMax;
            stockSpan.textContent = cantidadMax;
            stockSmall.style.display = 'block';
        } else {
            cantidadInput.max = '';
            stockSmall.style.display = 'none';
        }
    });

    motivoSelect.addEventListener('change', function() {
        grupoReferencia.style.display = this.value === 'dispensacion' ? 'block' : 'none';
    });
});
</script>