<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Registrar Nuevo Paciente</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="/paciente/guardar" method="POST" id="form-paciente" onsubmit="return validateForm('form-paciente')">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;">
                <div class="form-group">
                    <label for="ci">Cédula de Identidad *</label>
                    <input type="text" id="ci" name="ci" class="form-control" value="<?php echo htmlspecialchars($preCI ?? ''); ?>" required placeholder="Ej: 1234567">
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required placeholder="Nombre del paciente">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" required placeholder="Apellido del paciente">
                </div>

                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="sexo">Sexo *</label>
                    <select id="sexo" name="sexo" class="form-control" required>
                        <option value="">Seleccionar</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ej: 0987654321">
                </div>

                <div class="form-group" style="grid-column:1/3;">
                    <label for="direccion">Direccion</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección del paciente">
                </div>

                <div class="form-group">
                    <label for="numero_asegurado">Numero de Asegurado</label>
                    <input type="text" id="numero_asegurado" name="numero_asegurado" class="form-control" placeholder="Nro. de carnet de seguro">
                </div>

                <div class="form-group" style="display:flex;align-items:center;padding-top:25px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" id="asegurado" name="asegurado" value="1" checked style="width:auto;">
                        Paciente asegurado (IPS)
                    </label>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Registrar Paciente</button>
                <a href="/paciente" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
            </div>
        </form>
    </div>
</div>