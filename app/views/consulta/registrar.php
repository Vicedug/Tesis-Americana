<div class="page-header">
    <h1><i class="fas fa-stethoscope"></i> Registrar Consulta Médica</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="/consulta/guardar" method="POST" id="form-consulta" onsubmit="return validateForm('form-consulta')">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="paciente_id" id="paciente_id" value="<?php echo $paciente['id'] ?? 0; ?>">

            <div class="card" style="margin-bottom:20px;border-left:4px solid #007bff;">
                <div class="card-header" style="background:#f8f9fa;">
                    <h3><i class="fas fa-user"></i> Datos del Paciente</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($paciente) && $paciente): ?>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:15px;">
                        <div class="form-group">
                            <label>Cédula de Identidad</label>
                            <p><strong><?php echo htmlspecialchars($paciente['ci']); ?></strong></p>
                        </div>
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <p><strong><?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre']); ?></strong></p>
                        </div>
                        <div class="form-group">
                            <label>Seguro Médico</label>
                            <p>
                                <?php if (!empty($paciente['asegurado'])): ?>
                                    <span class="badge badge-success">Asegurado - <?php echo htmlspecialchars($paciente['numero_asegurado'] ?? 'N/A'); ?></span>
                                <?php else: ?>
                                    <span class="badge badge-danger">No asegurado</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="form-group">
                        <label for="buscar_paciente">Buscar Paciente por CI</label>
                        <div style="display:flex;gap:10px;">
                            <input type="text" id="buscar_paciente" class="form-control" placeholder="Ingrese CI del paciente...">
                            <button type="button" class="btn btn-primary" onclick="buscarPaciente()"><i class="fas fa-search"></i> Buscar</button>
                            <a href="/paciente/crear" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo</a>
                        </div>
                    </div>
                    <div id="paciente-info" style="margin-top:10px;"></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card" style="margin-bottom:20px;border-left:4px solid #28a745;">
                <div class="card-header" style="background:#f8f9fa;">
                    <h3><i class="fas fa-user-md"></i> Profesional a Cargo</h3>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                        <div class="form-group">
                            <label>Profesional</label>
                            <p><strong><?php echo htmlspecialchars($profesionalNombre ?? ''); ?></strong></p>
                        </div>
                        <div class="form-group">
                            <label>Fecha y Hora</label>
                            <input type="datetime-local" name="fecha_consulta" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:20px;border-left:4px solid #ffc107;">
                <div class="card-header" style="background:#f8f9fa;">
                    <h3><i class="fas fa-diagnoses"></i> Diagnóstico y Observaciones</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="diagnostico">Diagnóstico *</label>
                        <textarea id="diagnostico" name="diagnostico" class="form-control" rows="3" required placeholder="Describa el diagnóstico del paciente..."></textarea>
                    </div>
                    <div class="form-group" style="margin-top:15px;">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" rows="3" placeholder="Observaciones adicionales, síntomas, signos vitales..."></textarea>
                    </div>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Registrar Consulta</button>
                <a href="/consulta" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function buscarPaciente() {
    var ci = document.getElementById('buscar_paciente').value.trim();
    if (!ci) {
        alert('Ingrese un CI para buscar');
        return;
    }

    fetch('/paciente/buscarAjax?ci=' + encodeURIComponent(ci))
        .then(function(response) { return response.json(); })
        .then(function(data) {
            var infoDiv = document.getElementById('paciente-info');
            if (data.found) {
                document.getElementById('paciente_id').value = data.id;
                var seguroBadge = data.asegurado
                    ? '<span class="badge badge-success">Asegurado</span>'
                    : '<span class="badge badge-danger">No asegurado</span>';
                infoDiv.innerHTML = '<div class="alert alert-success">' +
                    '<strong>' + data.apellido + ', ' + data.nombre + '</strong> - CI: ' + data.ci + ' ' + seguroBadge +
                    '</div>';
            } else {
                infoDiv.innerHTML = '<div class="alert alert-warning">No se encontró paciente con CI: ' + ci + '</div>';
                document.getElementById('paciente_id').value = 0;
            }
        })
        .catch(function() {
            alert('Error al buscar paciente');
        });
}
</script>
