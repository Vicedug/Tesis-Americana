<div class="page-header">
    <h1><i class="fas fa-prescription"></i> Crear Receta Médica</h1>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="background:#f8f9fa;">
        <h3><i class="fas fa-stethoscope"></i> Consulta Asociada</h3>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;">
            <div class="form-group">
                <label>Paciente</label>
                <p><strong><?php echo htmlspecialchars(($consulta['paciente_apellido'] ?? '') . ', ' . ($consulta['paciente_nombre'] ?? '')); ?></strong></p>
            </div>
            <div class="form-group">
                <label>CI</label>
                <p><?php echo htmlspecialchars($consulta['paciente_ci'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Seguro Médico</label>
                <p>
                    <?php if (!empty($consulta['paciente_asegurado'])): ?>
                        <span class="badge badge-success">Asegurado - <?php echo htmlspecialchars($consulta['numero_asegurado'] ?? 'N/A'); ?></span>
                    <?php else: ?>
                        <span class="badge badge-danger">No asegurado</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="form-group" style="grid-column:1/4;">
                <label>Diagnóstico</label>
                <p style="background:#f8f9fa;padding:10px;border-radius:4px;"><?php echo htmlspecialchars($consulta['diagnostico'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="/receta/guardar" method="POST" id="form-receta">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="consulta_id" value="<?php echo $consulta['id']; ?>">

            <div class="card-header" style="background:#f8f9fa;margin-bottom:15px;">
                <h3><i class="fas fa-pills"></i> Medicamentos Prescritos</h3>
            </div>

            <div id="medicamentos-container">
                <div class="medicamento-row" style="display:grid;grid-template-columns:3fr 1fr 2fr auto;gap:10px;margin-bottom:10px;align-items:end;">
                    <div class="form-group">
                        <label>Medicamento</label>
                        <input type="text" class="form-control medicamento-search" placeholder="Buscar medicamento..." data-index="0" autocomplete="off">
                        <input type="hidden" name="medicamento_id[]" class="medicamento-id" value="">
                        <div class="medicamento-results" style="display:none;position:absolute;background:#fff;border:1px solid #ddd;border-radius:4px;max-height:200px;overflow-y:auto;z-index:1000;width:92%;"></div>
                    </div>
                    <div class="form-group">
                        <label>Cantidad</label>
                        <input type="number" name="cantidad_prescrita[]" class="form-control" min="1" value="1" placeholder="Cant.">
                    </div>
                    <div class="form-group">
                        <label>Indicaciones</label>
                        <input type="text" name="indicaciones[]" class="form-control" placeholder="Ej: 1 comp. cada 8 horas">
                    </div>
                    <div class="form-group" style="padding-top:25px;">
                        <button type="button" class="btn btn-danger btn-sm remove-med" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>

            <div style="margin:15px 0;">
                <button type="button" class="btn btn-secondary" id="add-medicamento"><i class="fas fa-plus"></i> Agregar Medicamento</button>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Emitir Receta</button>
                <a href="/consulta/detalle?id=<?php echo $consulta['id']; ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
var medIndex = 1;

document.getElementById('add-medicamento').addEventListener('click', function() {
    var container = document.getElementById('medicamentos-container');
    var row = document.createElement('div');
    row.className = 'medicamento-row';
    row.style.cssText = 'display:grid;grid-template-columns:3fr 1fr 2fr auto;gap:10px;margin-bottom:10px;align-items:end;';
    row.innerHTML = '<div class="form-group">' +
        '<label>Medicamento</label>' +
        '<input type="text" class="form-control medicamento-search" placeholder="Buscar medicamento..." data-index="' + medIndex + '" autocomplete="off">' +
        '<input type="hidden" name="medicamento_id[]" class="medicamento-id" value="">' +
        '<div class="medicamento-results" style="display:none;position:absolute;background:#fff;border:1px solid #ddd;border-radius:4px;max-height:200px;overflow-y:auto;z-index:1000;"></div>' +
        '</div>' +
        '<div class="form-group">' +
        '<label>Cantidad</label>' +
        '<input type="number" name="cantidad_prescrita[]" class="form-control" min="1" value="1" placeholder="Cant.">' +
        '</div>' +
        '<div class="form-group">' +
        '<label>Indicaciones</label>' +
        '<input type="text" name="indicaciones[]" class="form-control" placeholder="Ej: 1 comp. cada 8 horas">' +
        '</div>' +
        '<div class="form-group" style="padding-top:25px;">' +
        '<button type="button" class="btn btn-danger btn-sm remove-med" title="Eliminar"><i class="fas fa-trash"></i></button>' +
        '</div>';
    container.appendChild(row);
    medIndex++;
    bindSearch(row);
    bindRemove(row);
});

function bindRemove(row) {
    var btn = row.querySelector('.remove-med');
    btn.addEventListener('click', function() {
        if (document.querySelectorAll('.medicamento-row').length > 1) {
            row.remove();
        } else {
            alert('Debe haber al menos un medicamento en la receta');
        }
    });
}

function bindSearch(row) {
    var input = row.querySelector('.medicamento-search');
    var resultsDiv = row.querySelector('.medicamento-results');
    var hiddenId = row.querySelector('.medicamento-id');
    var debounceTimer;

    input.addEventListener('input', function() {
        var term = this.value.trim();
        clearTimeout(debounceTimer);

        if (term.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(function() {
            fetch('/receta/buscarMedicamentosAjax?q=' + encodeURIComponent(term))
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    resultsDiv.innerHTML = '';
                    if (data.results.length === 0) {
                        resultsDiv.innerHTML = '<div style="padding:8px;color:#999;">No se encontraron medicamentos</div>';
                    } else {
                        data.results.forEach(function(med) {
                            var item = document.createElement('div');
                            item.style.cssText = 'padding:8px;cursor:pointer;border-bottom:1px solid #eee;';
                            item.innerHTML = '<strong>' + med.nombre + '</strong><br>' +
                                '<small>' + med.principio_activo + ' - ' + med.concentracion + ' - ' + med.forma_farmaceutica + '</small>';
                            item.addEventListener('click', function() {
                                input.value = med.nombre + ' (' + med.concentracion + ')';
                                hiddenId.value = med.id;
                                resultsDiv.style.display = 'none';
                            });
                            resultsDiv.appendChild(item);
                        });
                    }
                    resultsDiv.style.display = 'block';
                })
                .catch(function() {
                    resultsDiv.innerHTML = '<div style="padding:8px;color:red;">Error al buscar</div>';
                    resultsDiv.style.display = 'block';
                });
        }, 300);
    });

    input.addEventListener('blur', function() {
        setTimeout(function() { resultsDiv.style.display = 'none'; }, 200);
    });

    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && resultsDiv.innerHTML !== '') {
            resultsDiv.style.display = 'block';
        }
    });
}

document.querySelectorAll('.medicamento-row').forEach(function(row) {
    bindSearch(row);
    bindRemove(row);
});

document.getElementById('form-receta').addEventListener('submit', function(e) {
    var valid = true;
    document.querySelectorAll('.medicamento-id').forEach(function(input) {
        if (!input.value || parseInt(input.value) <= 0) {
            valid = false;
        }
    });
    if (!valid) {
        e.preventDefault();
        alert('Debe seleccionar un medicamento válido en todas las filas');
    }
});
</script>
