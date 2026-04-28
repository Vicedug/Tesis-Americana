<div class="page-header">
    <h1><i class="fas fa-edit"></i> Editar Paciente <small><?php echo htmlspecialchars($paciente['apellido'] . ', ' . $paciente['nombre']); ?></small></h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="/paciente/actualizar" method="POST" id="form-editar-paciente" onsubmit="return validateForm('form-editar-paciente')">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label>CI</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($paciente['ci']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($paciente['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($paciente['apellido']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?php echo $paciente['fecha_nacimiento']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="sexo">Sexo *</label>
                    <select id="sexo" name="sexo" class="form-control" required>
                        <option value="M" <?php echo $paciente['sexo'] === 'M' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo $paciente['sexo'] === 'F' ? 'selected' : ''; ?>>Femenino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo htmlspecialchars($paciente['telefono'] ?? ''); ?>">
                </div>

                <div class="form-group" style="grid-column:1/3;">
                    <label for="direccion">Direccion</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" value="<?php echo htmlspecialchars($paciente['direccion'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="numero_asegurado">Numero de Asegurado</label>
                    <input type="text" id="numero_asegurado" name="numero_asegurado" class="form-control" value="<?php echo htmlspecialchars($paciente['numero_asegurado'] ?? ''); ?>">
                </div>

                <div class="form-group" style="display:flex;align-items:center;padding-top:25px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" id="asegurado" name="asegurado" value="1" <?php echo $paciente['asegurado'] ? 'checked' : ''; ?> style="width:auto;">
                        Paciente asegurado (IPS)
                    </label>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar Paciente</button>
                <a href="/paciente/perfil?id=<?php echo $paciente['id']; ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>