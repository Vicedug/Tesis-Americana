<div class="page-header">
    <h1><i class="fas fa-search"></i> Buscar Paciente <small>Identificación por CI</small></h1>
</div>

<div class="card">
    <div class="card-header">
        <h3>Fase 1: Identificación del Paciente</h3>
    </div>
    <div class="card-body">
        <p class="text-muted">Ingrese el número de cédula de identidad para verificar la condición de asegurado y continuar con el flujo de trazabilidad.</p>

        <div class="form-group">
            <label for="search_ci"><i class="fas fa-id-card"></i> Número de Cédula de Identidad (CI)</label>
            <div style="display:flex;gap:10px;">
                <input type="text" id="search_ci" class="form-control" placeholder=" Ej: 1234567" maxlength="20" style="flex:1;" autofocus>
                <button onclick="searchPaciente()" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </div>

        <div id="paciente-result"></div>

        <?php if (isset($paciente) && $paciente): ?>
        <div class="card" style="margin-top:20px;">
            <div class="card-header" style="background:#d5f5e3;">
                <h3><i class="fas fa-check-circle"></i> Paciente Encontrado</h3>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
                    <div class="form-group">
                        <label>CI</label>
                        <p><strong><?php echo htmlspecialchars($paciente['ci']); ?></strong></p>
                    </div>
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <p><strong><?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre']); ?></strong></p>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Nacimiento</label>
                        <p><?php echo date('d/m/Y', strtotime($paciente['fecha_nacimiento'])); ?></p>
                    </div>
                    <div class="form-group">
                        <label>Condición</label>
                        <?php if ($paciente['asegurado']): ?>
                            <p><span class="badge badge-success">Asegurado</span></p>
                        <?php else: ?>
                            <p><span class="badge badge-danger">No asegurado</span></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($paciente['asegurado']): ?>
                <div style="display:flex;gap:10px;margin-top:15px;">
                    <a href="/consulta/crear?paciente_id=<?php echo $paciente['id']; ?>" class="btn btn-primary"><i class="fas fa-stethoscope"></i> Registrar Consulta</a>
                    <a href="/paciente/perfil?id=<?php echo $paciente['id']; ?>" class="btn btn-info"><i class="fas fa-user"></i> Ver Perfil</a>
                </div>
                <?php else: ?>
                <div class="alert alert-danger" style="margin-top:15px;">
                    <i class="fas fa-exclamation-triangle"></i> El paciente no cuenta con cobertura de seguro. No puede continuar con el flujo de dispensación.
                    <br><a href="/paciente/perfil?id=<?php echo $paciente['id']; ?>" class="btn btn-sm btn-secondary" style="margin-top:10px;">Ver perfil del paciente</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top:15px;">
    <a href="/paciente" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al listado</a>
</div>