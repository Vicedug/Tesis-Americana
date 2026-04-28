<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <h1><i class="fas fa-pills"></i> TrazaIPS</h1>
            <small>Sistema de Trazabilidad de Medicamentos</small>
        </div>

        <?php
        $flashMessages = App\Helpers\Flash::get();
        if (!empty($flashMessages)):
            foreach ($flashMessages as $msg):
        ?>
            <div class="alert alert-<?php echo $msg['type']; ?>">
                <i class="fas fa-<?php echo $msg['type'] === 'success' ? 'check-circle' : ($msg['type'] === 'danger' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                <?php echo htmlspecialchars($msg['text']); ?>
            </div>
        <?php endforeach; endif; ?>

        <form action="/auth/authenticate" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Usuario</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Ingrese su usuario" required autofocus>
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>

        <div class="login-footer">
            <p>Instituto de Previsión Social &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/login.css">