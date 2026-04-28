<?php declare(strict_types=1);

class AuthMiddleware
{
    public static function check(): never
    {
        if (!Session::isLoggedIn()) {
            Flash::error('Debe iniciar sesión para acceder a esta página.');
            Redirect::to('/login');
        }
    }

    public static function guest(): never
    {
        if (Session::isLoggedIn()) {
            Redirect::to('/dashboard');
        }
    }

    public static function user(): ?array
    {
        if (!Session::isLoggedIn()) {
            return null;
        }

        return [
            'id' => Session::getUserId(),
            'role' => Session::getUserRole(),
            'name' => Session::getUserName(),
        ];
    }
}