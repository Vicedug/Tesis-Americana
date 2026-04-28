<?php
use App\Helpers\Session;
use App\Config\Roles;

$role = Session::getUserRole() ?? '';
$menuItems = Roles\getMenuItems($role);
$userName = Session::getUserName() ?? 'Usuario';
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h2><i class="fas fa-pills"></i> TrazaIPS</h2>
        <small>Trazabilidad de Medicamentos</small>
    </div>

    <ul class="sidebar-menu">
        <?php foreach ($menuItems as $item): ?>
            <?php if (isset($item['section']) && $item['section']): ?>
                <li class="sidebar-section-title"><?php echo $item['label']; ?></li>
            <?php else: ?>
                <li>
                    <a href="<?php echo $item['route']; ?>" class="<?php echo strpos($currentPath, $item['route']) !== false ? 'active' : ''; ?>">
                        <i class="fas fa-<?php echo $item['icon']; ?>"></i>
                        <?php echo $item['label']; ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</aside>