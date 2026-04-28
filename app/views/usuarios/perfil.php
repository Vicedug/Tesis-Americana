<div class="page-header">
    <h1><i class="fas fa-user"></i> Mi Perfil</h1>
</div>

<?php if ($usuario): ?>
<div class="card">
    <div class="card-header"><h3>Información Personal</h3></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div class="form-group">
                <label>Usuario</label>
                <p><strong><?php echo htmlspecialchars($usuario['username']); ?></strong></p>
            </div>
            <div class="form-group">
                <label>Nombre Completo</label>
                <p><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></p>
            </div>
            <div class="form-group">
                <label>Email</label>
                <p><?php echo htmlspecialchars($usuario['email'] ?? 'N/A'); ?></p>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <?php
                    $roleLabels = ['administrador' => 'Administrador', 'profesional_salud' => 'Profesional de Salud', 'farmaceutico' => 'Farmacéutico', 'auditor' => 'Auditor'];
                ?>
                <p><span class="badge badge-primary"><?php echo $roleLabels[$usuario['rol']] ?? $usuario['rol']; ?></span></p>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <div class="card-header"><h3><i class="fas fa-lock"></i> Cambiar Contraseña</h3></div>
    <div class="card-body">
        <form action="/usuarios/cambiarPassword" method="POST" id="form-password">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label for="current_password">Contraseña Actual *</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nueva Contraseña *</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required placeholder="Mínimo 8 caracteres">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
            </div>

            <div style="margin-top:15px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cambiar Contraseña</button>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning">No se pudo cargar la información del perfil.</div>
<?php endif; ?>