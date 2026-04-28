<?php

declare(strict_types=1);

defined('ROLE_PERMISSIONS') || define('ROLE_PERMISSIONS', [
    'administrador' => [
        'paciente', 'consulta', 'receta', 'farmacia',
        'dispensacion', 'trazabilidad', 'reportes',
        'dashboard', 'usuarios',
    ],
    'profesional_salud' => [
        'paciente', 'consulta', 'receta', 'dashboard',
    ],
    'farmaceutico' => [
        'paciente', 'farmacia', 'dispensacion',
        'trazabilidad', 'dashboard',
    ],
    'auditor' => [
        'trazabilidad', 'reportes', 'dashboard', 'auditoria',
    ],
]);

defined('MENU_LABELS') || define('MENU_LABELS', [
    'dashboard'      => 'Dashboard',
    'paciente'       => 'Pacientes',
    'consulta'       => 'Consultas',
    'receta'         => 'Recetas',
    'farmacia'       => 'Farmacia',
    'dispensacion'   => 'Dispensación',
    'trazabilidad'   => 'Trazabilidad',
    'reportes'       => 'Reportes',
    'usuarios'       => 'Usuarios',
    'auditoria'      => 'Auditoría',
]);

defined('MENU_ICONS') || define('MENU_ICONS', [
    'dashboard'      => 'fas fa-tachometer-alt',
    'paciente'       => 'fas fa-user-injured',
    'consulta'       => 'fas fa-stethoscope',
    'receta'         => 'fas fa-prescription',
    'farmacia'       => 'fas fa-pills',
    'dispensacion'   => 'fas fa-hand-holding-medical',
    'trazabilidad'   => 'fas fa-route',
    'reportes'       => 'fas fa-chart-bar',
    'usuarios'       => 'fas fa-users-cog',
    'auditoria'      => 'fas fa-clipboard-check',
]);

function canAccess(string $role, string $module): bool
{
    if (!in_array($role, ROLES, true)) {
        return false;
    }

    $permissions = ROLE_PERMISSIONS[$role] ?? [];

    return in_array($module, $permissions, true);
}

function getMenuItems(string $role): array
{
    if (!in_array($role, ROLES, true)) {
        return [];
    }

    $permissions = ROLE_PERMISSIONS[$role] ?? [];
    $items = [];

    foreach ($permissions as $module) {
        $items[] = [
            'module' => $module,
            'label'  => MENU_LABELS[$module] ?? ucfirst($module),
            'icon'   => MENU_ICONS[$module] ?? 'fas fa-circle',
            'route'  => '/' . $module,
        ];
    }

    return $items;
}
