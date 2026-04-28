<?php declare(strict_types=1);

class RoleMiddleware
{
    private static function rolesConfig(): array
    {
        static $config = null;
        if ($config === null) {
            $config = require __DIR__ . '/../../config/roles.php';
        }
        return $config;
    }

    public static function hasRole(string $role): bool
    {
        $userRole = Session::getUserRole();
        if ($userRole === null) {
            return false;
        }
        return $userRole === $role;
    }

    public static function hasPermission(string $module): bool
    {
        $userRole = Session::getUserRole();
        if ($userRole === null) {
            return false;
        }
        $roles = self::rolesConfig();
        if (!isset($roles[$userRole])) {
            return false;
        }
        return in_array($module, $roles[$userRole]['permissions'], true);
    }

    public static function requireRole(string $role): never
    {
        if (!self::hasRole($role)) {
            Flash::error('No tiene el rol requerido para acceder a esta página.');
            Redirect::to('/dashboard');
        }
    }

    public static function requirePermission(string $module): never
    {
        if (!self::hasPermission($module)) {
            Flash::error('No tiene permisos para acceder al módulo solicitado.');
            Redirect::to('/dashboard');
        }
    }
}