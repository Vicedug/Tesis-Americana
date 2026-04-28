<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Registrar Ingreso de Stock</h1>
    <div>
        <a href="/farmacia/stock" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Stock</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="/farmacia/guardar-ingreso" method="POST" id="form-ingreso">
            <input type="hidden" name="_csrf" value="<?php echo $csrf; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group" style="grid-column:1/3;">
                    <label for="medicamento_id">Medicamento *</label>
                    <div style="position:relative;">
                        <input type="text" id="medicamento_search" class="form-control" placeholder="Buscar medicamento por nombre, principio activo o código..." autocomplete="off">
                        <input type="hidden" id="medicamento_id" name="medicamento_id">
                        <div id="medicamento_results" class="search-results" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ddd;border-radius:4px;max-height:250px;overflow-y:auto;z-index:1000;box-shadow:0 4px 6px rgba(0,0,0,0.1);"></div>
                    </div>
                    <small id="medicamento_selected" class="form-text" style="color:var(--success-color);display:none;"></small>
                </div>

                <div class="form-group">
                    <label for="nro_lote">Número de Lote *</label>
                    <input type="text" id="nro_lote" name="nro_lote" class="form-control" required placeholder="Ej: LOT-2024-001">
                </div>

                <div class="form-group">
                    <label for="fecha_vencimiento">Fecha de Vencimiento *</label>
                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad *</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" required min="1" placeholder="Cantidad de unidades">
                </div>

                <div class="form-group">
                    <label for="precio_unitario">Precio Unitario</label>
                    <input type="number" id="precio_unitario" name="precio_unitario" class="form-control" step="0.01" min="0" placeholder="0.00" value="0">
                </div>

                <div class="form-group">
                    <label for="proveedor">Proveedor</label>
                    <input type="text" id="proveedor" name="proveedor" class="form-control" placeholder="Nombre del proveedor" list="proveedores_list" autocomplete="off">
                    <datalist id="proveedores_list">
                        <?php foreach ($proveedores as $prov): ?>
                            <option value="<?php echo htmlspecialchars($prov['proveedor']); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Registrar Ingreso</button>
                <a href="/farmacia/stock" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('medicamento_search');
    const resultsDiv = document.getElementById('medicamento_results');
    const hiddenInput = document.getElementById('medicamento_id');
    const selectedText = document.getElementById('medicamento_selected');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const term = this.value.trim();

        if (term.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(function() {
            fetch('/farmacia/buscar-ajax?term=' + encodeURIComponent(term))
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';

                    if (data.results.length === 0) {
                        resultsDiv.innerHTML = '<div style="padding:10px;color:#999;">No se encontraron medicamentos</div>';
                        resultsDiv.style.display = 'block';
                        return;
                    }

                    data.results.forEach(med => {
                        const div = document.createElement('div');
                        div.style.cssText = 'padding:10px;cursor:pointer;border-bottom:1px solid #eee;';
                        div.innerHTML = '<strong>' + med.nombre + '</strong><br>' +
                            '<small>' + med.principio_activo + ' | ' + med.forma_farmaceutica + ' | ' +
                            med.concentracion + ' ' + med.unidad_medida + '</small>';

                        div.addEventListener('click', function() {
                            hiddenInput.value = med.id;
                            searchInput.value = med.nombre;
                            selectedText.textContent = 'Seleccionado: ' + med.nombre + ' (' + med.principio_activo + ' - ' + med.forma_farmaceutica + ')';
                            selectedText.style.display = 'block';
                            resultsDiv.style.display = 'none';
                        });

                        div.addEventListener('mouseover', function() {
                            this.style.backgroundColor = '#f0f0f0';
                        });
                        div.addEventListener('mouseout', function() {
                            this.style.backgroundColor = '';
                        });

                        resultsDiv.appendChild(div);
                    });

                    resultsDiv.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>