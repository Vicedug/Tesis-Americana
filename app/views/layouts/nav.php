<?php
use App\Helpers\Session;
$userName = Session::getUserName() ?? 'Usuario';
$userRole = Session::getUserRole() ?? '';
$roleLabels = [
    'administrador' => 'Administrador',
    'profesional_salud' => 'Profesional de Salud',
    'farmaceutico' => 'Farmacéutico',
    'auditor' => 'Auditor'
];
?>
<nav class="top-header">
    <div>
        <button class="btn btn-sm btn-secondary" id="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="user-info">
        <span><?php echo htmlspecialchars($userName); ?></span>
        <span class="badge-role"><?php echo $roleLabels[$userRole] ?? $userRole; ?></span>
        <a href="/auth/logout" class="btn btn-sm btn-danger">
            <i class="fas fa-sign-out-alt"></i> Salir
        </a>
    </div>
</nav>