<div class="page-header">
    <h1><i class="fas fa-edit"></i> Editar Usuario</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="/usuarios/actualizar" method="POST" id="form-editar-usuario">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['username']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol *</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="administrador" <?php echo $usuario['rol'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="profesional_salud" <?php echo $usuario['rol'] === 'profesional_salud' ? 'selected' : ''; ?>>Profesional de Salud</option>
                        <option value="farmaceutico" <?php echo $usuario['rol'] === 'farmaceutico' ? 'selected' : ''; ?>>Farmacéutico</option>
                        <option value="auditor" <?php echo $usuario['rol'] === 'auditor' ? 'selected' : ''; ?>>Auditor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="establecimiento_id">Establecimiento</label>
                    <select id="establecimiento_id" name="establecimiento_id" class="form-control">
                        <option value="">Seleccionar</option>
                        <?php foreach ($establecimientos as $e): ?>
                        <option value="<?php echo $e['id']; ?>" <?php echo $usuario['establecimiento_id'] == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="display:flex;align-items:center;padding-top:25px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="activo" value="1" <?php echo $usuario['activo'] ? 'checked' : ''; ?> style="width:auto;">
                        Usuario activo
                    </label>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Actualizar</button>
                <a href="/usuarios" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>