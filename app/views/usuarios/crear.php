<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Crear Usuario</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="/usuarios/guardar" method="POST" id="form-crear-usuario" onsubmit="return validateForm('form-crear-usuario')">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label for="username">Usuario *</label>
                    <input type="text" id="username" name="username" class="form-control" required placeholder="Nombre de usuario">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required placeholder="Nombre">
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required placeholder="Apellido">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Mínimo 8 caracteres">
                </div>
                <div class="form-group">
                    <label for="rol">Rol *</label>
                    <select id="rol" name="rol" class="form-control" required>
                        <option value="">Seleccionar rol</option>
                        <option value="administrador">Administrador</option>
                        <option value="profesional_salud">Profesional de Salud</option>
                        <option value="farmaceutico">Farmacéutico</option>
                        <option value="auditor">Auditor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="establecimiento_id">Establecimiento</label>
                    <select id="establecimiento_id" name="establecimiento_id" class="form-control">
                        <option value="">Seleccionar establecimiento</option>
                        <?php foreach ($establecimientos as $e): ?>
                        <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Crear Usuario</button>
                <a href="/usuarios" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>